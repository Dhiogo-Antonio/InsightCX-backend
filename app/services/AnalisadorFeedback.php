<?php

namespace App\Services;

use Illuminate\Support\Str;

class AnalisadorFeedback
{
    public function analisar($feedback)
    {
        $texto = Str::ascii(
            Str::lower($feedback->assunto . ' ' . $feedback->comentario)
        );

        $palavrasChave = [];

        $termosAlta = [
            'sistema parado',
            'sistema fora do ar',
            'perda de dados',
            'dados perdidos',
            'acesso bloqueado',
            'nao consigo acessar',
            'falha critica',
            'impossibilitado de trabalhar',
            'servico indisponivel'
        ];

        $termosMedia = [
            'lentidao',
            'muito lento',
            'demora',
            'erro',
            'falhando',
            'dificuldade',
            'problema',
            'nao funciona corretamente',
            'travando'
        ];

        $termosElogio = [
            'excelente',
            'otimo',
            'muito bom',
            'parabens',
            'satisfeito',
            'gostei',
            'eficiente',
            'atendimento rapido',
            'funcionando perfeitamente'
        ];

        $termosSugestao = [
            'sugestao',
            'poderia ter',
            'seria interessante',
            'seria bom',
            'melhoria',
            'adicionar uma funcao'
        ];

        foreach ([
            $termosAlta,
            $termosMedia,
            $termosElogio,
            $termosSugestao
        ] as $grupo) {
            foreach ($grupo as $termo) {
                if (Str::contains($texto, $termo)) {
                    $palavrasChave[] = $termo;
                }
            }
        }

        // Define a prioridade
        if ($this->contemTermo($texto, $termosAlta)) {
            $prioridade = 'alta';
            $tipoAtendimento = 'manual';

            $analise = 'O feedback apresenta indícios de uma falha crítica e precisa de avaliação do administrador.';
        } elseif ($this->contemTermo($texto, $termosMedia)) {
            $prioridade = 'media';
            $tipoAtendimento = 'manual';

            $analise = 'O feedback apresenta indícios de problemas no serviço e precisa de avaliação do administrador.';
        } elseif ($feedback->categoria === 'elogio') {
            $prioridade = 'baixa';
            $tipoAtendimento = 'automatico';

            $analise = 'O feedback apresenta características positivas e pode receber uma resposta automática.';
        } elseif ($this->contemTermo($texto, $termosSugestao)) {
            $prioridade = 'baixa';
            $tipoAtendimento = 'manual';

            $analise = 'O feedback apresenta uma sugestão de melhoria que pode ser avaliada pelo administrador.';
        } else {
            // Reclamações sem palavras-chave reconhecidas
            // não devem ser respondidas automaticamente.
            $prioridade = 'media';
            $tipoAtendimento = 'manual';

            $analise = 'O feedback não pôde ser classificado com segurança e precisa de avaliação humana.';
        }

        // Prepara a resposta
        if ($tipoAtendimento === 'automatico') {
            $resposta = 'Agradecemos pelo seu feedback! Ficamos felizes em saber que sua experiência foi positiva. Sua opinião é muito importante para continuarmos melhorando nossos serviços.';
            $statusAtendimento = 'respondido_automaticamente';
        } else {
            $resposta = null;
            $statusAtendimento = 'aguardando_administrador';
        }

        return [
            'analise' => $analise,
            'palavras_chave' => array_values(array_unique($palavrasChave)),
            'prioridade_identificada' => $prioridade,
            'tipo_atendimento' => $tipoAtendimento,
            'resposta_gerada' => $resposta,
            'status_atendimento' => $statusAtendimento
        ];
    }

    private function contemTermo($texto, $termos)
    {
        foreach ($termos as $termo) {
            if (Str::contains($texto, $termo)) {
                return true;
            }
        }

        return false;
    }
}
