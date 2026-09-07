<?php

namespace TraderTracker\Php\Controllers;

use TraderTracker\Php\Services\EmbeddingService;
use TraderTracker\Php\Services\GenerationService;
use TraderTracker\Php\Models\DocumentationChunkModel;
use TraderTracker\Php\Utils\RequestBody;
use TraderTracker\Php\Utils\AppError;

class AssistantController {
    public static function ask(array $params): void {

        set_time_limit(120);
        header('Content-Type: application/json');

        $body = RequestBody::parse();
        $question = trim($body['question'] ?? '');

        if ($question === '') {
            throw new AppError('Question is required', 400);
        }

        $questionEmbedding = EmbeddingService::embed($question);
        $relevantChunks = DocumentationChunkModel::findMostRelevant($questionEmbedding, 3);

        if (empty($relevantChunks)) {
            throw new AppError('No documentation available', 500);
        }

        $answer = GenerationService::generate($question, $relevantChunks);

        echo json_encode([
            'answer' => $answer,
            'sources' => array_map(fn($c) => $c['section_title'], $relevantChunks),
        ]);
    }
}