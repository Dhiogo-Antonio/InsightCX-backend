<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class ChatbotService
{
    public function responder($feedback, $prioridade)
    {
        $apiKey = config('services.openai.api_key');

        if (empty($apiKey)) {
            throw new RuntimeException(
                'A chave da API da OpenAI não foi configurada.'
            );
        }

        $instrucoes = "
            Você é o assistente virtual do Insight CX,
            um sistema de experiência do cliente.

            Sua função é ajudar clientes a resolver problemas
            relacionados aos serviços de uma empresa.

            Analise o assunto e o comentário do cliente e
            forneça uma resposta clara, educada e objetiva.

            REGRAS:

            1. Entenda o problema relatado antes de responder.
            2. Ofereça procedimentos práticos e seguros.
            3. Explique as etapas de solução de maneira simples.
            4. Faça perguntas quando precisar de mais informações.
            5. Não invente informações sobre o sistema ou a empresa.
            6. Não afirme que resolveu um problema sem confirmação.
            7. Se não souber solucionar, informe que a equipe
               responsável precisará analisar o caso.
            8. Para problemas críticos, oriente o cliente e
               informe que o caso será encaminhado ao administrador.
            9. Responda sempre em português brasileiro.
            10. Não solicite senhas, códigos de acesso ou dados sigilosos.

            PRIORIDADE IDENTIFICADA: {$prioridade}

            ASSUNTO:
            {$feedback->assunto}

            COMENTÁRIO:
            {$feedback->comentario}
        ";

        try {
            $response = Http::withToken($apiKey)
                ->acceptJson()
                ->timeout(60)
                ->post('https://api.openai.com/v1/responses', [
                    'model' => config('services.openai.model'),
                    'instructions' => $instrucoes,
                    'input' => 'Analise o feedback e oriente o cliente sobre como proceder.',
                    'max_output_tokens' => 700
                ]);

            if ($response->failed()) {
                Log::error('Erro na API OpenAI', [
                    'status' => $response->status(),
                    'resposta' => $response->json()
                ]);

                throw new RuntimeException(
                    'Não foi possível obter uma resposta da inteligência artificial.'
                );
            }

            $dados = $response->json();

            foreach ($dados['output'] ?? [] as $item) {
                if (($item['type'] ?? null) !== 'message') {
                    continue;
                }

                foreach ($item['content'] ?? [] as $conteudo) {
                    if (($conteudo['type'] ?? null) === 'output_text') {
                        $texto = trim($conteudo['text'] ?? '');

                        if ($texto !== '') {
                            return $texto;
                        }
                    }
                }
            }

            throw new RuntimeException(
                'A inteligência artificial não retornou uma resposta válida.'
            );

        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            Log::error('Falha de conexão com a OpenAI', [
                'erro' => $e->getMessage()
            ]);

            throw new RuntimeException(
                'Não foi possível conectar à inteligência artificial.'
            );
        }
    }
}
