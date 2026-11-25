<?php

namespace App\Services\AI;

use Illuminate\Support\Facades\Http;
use Exception;

class AIService
{
    public function gerarTexto(string $prompt): string
    {
        // Recupera a chave configurada no .env.
        $apiKey = config('services.gemini.key');

        // Recupera o modelo configurado.
        $model = config('services.gemini.model');

        // Verifica se a chave existe.
        if (empty($apiKey)) {
            throw new Exception('A chave da API Gemini não foi configurada.');
        }

        // Monta o endereço da API.
        $url = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent";

        // Envia a solicitação para o Gemini.
        $response = Http::timeout(60)
            ->withHeaders([
                'x-goog-api-key' => $apiKey,
            ])
            ->post($url, [
                'contents' => [
                    [
                        'parts' => [
                            [
                                'text' => $prompt
                            ]
                        ]
                    ]
                ],
                'generationConfig' => [
                    'temperature' => 0.3,
                    'maxOutputTokens' => 1000,
                ]
            ]);

        // Verifica se a API retornou algum erro.
        if ($response->failed()) {
            throw new Exception(
                'Erro na API Gemini: ' . $response->body()
            );
        }

        // Recupera o texto gerado pela IA.
        $texto = $response->json('candidates.0.content.parts.0.text');

        if (empty($texto)) {
            throw new Exception('O Gemini não retornou um texto válido.');
        }

        return $texto;
    }
}


