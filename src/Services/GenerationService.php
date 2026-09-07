<?php

namespace TraderTracker\Php\Services;

use TraderTracker\Php\Utils\AppError;

class GenerationService
{
    private const SYSTEM_PROMPT = "You are the help assistant for Trader Tracker. Answer the user's question using ONLY the context provided below. If the answer isn't in the context, say so clearly instead of guessing. Keep your answer concise.";

    public static function generate(string $question, array $chunks): string
    {
        $url = $_ENV['GENERATION_URL'] ?? 'http://localhost:11434/api/chat';
        $model = $_ENV['GENERATION_MODEL'] ?? 'qwen2.5-coder:7b';

        $context = implode("\n\n", array_map(
            fn($chunk) => "### {$chunk['section_title']}\n{$chunk['content']}",
            $chunks
        ));

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 120,
            CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
            CURLOPT_POSTFIELDS => json_encode([
                'model' => $model,
                'messages' => [
                    ['role' => 'system', 'content' => self::SYSTEM_PROMPT],
                    ['role' => 'user', 'content' => "Context:\n{$context}\n\nQuestion: {$question}"],
                ],
                'stream' => false,
            ]),
        ]);

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            throw new AppError(
                'AI assistant unavailable: Ollama is not running or not installed. See README section "AI Assistant Prerequisites".',
                503
            );
        }

        $data = json_decode($response, true);

        if (!isset($data['message']['content'])) {
            throw new AppError('Invalid generation response from Ollama', 500);
        }

        return $data['message']['content'];
    }
}