<?php

require __DIR__ . '/../../vendor/autoload.php';

use TraderTracker\Php\Services\EmbeddingService;
use TraderTracker\Php\Models\DocumentationChunkModel;

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../..');
$dotenv->load();

$markdown = file_get_contents(__DIR__ . '/../data/help_docs.md');

$sections = preg_split('/^# /m', $markdown, -1, PREG_SPLIT_NO_EMPTY);

DocumentationChunkModel::deleteAll();

foreach ($sections as $section) {
    $lines = explode("\n", trim($section), 2);
    $title = trim($lines[0]);
    $content = trim($lines[1] ?? '');

    if (empty($content)) {
        continue;
    }

    echo "Embedding: $title...\n";
    $embedding = EmbeddingService::embed($title . "\n" . $content);

    DocumentationChunkModel::insert($title, $content, $embedding);
}

echo "Terminé.\n";