<?php

namespace TraderTracker\Php\Models;

use TraderTracker\Php\Database;

class DocumentationChunkModel
{
    public static function insert(string $sectionTitle, string $content, array $embedding): void
    {
        $db = Database::getConnection();
        $stmt = $db->prepare(
            "INSERT INTO documentation_chunks (section_title, content, embedding) VALUES (?, ?, ?)"
        );
        $stmt->execute([$sectionTitle, $content, json_encode($embedding)]);
    }

    public static function deleteAll(): void
    {
        $db = Database::getConnection();
        $db->exec("TRUNCATE TABLE documentation_chunks");
    }

    public static function findMostRelevant(array $questionEmbedding, int $topN = 3): array
    {
        $db = Database::getConnection();
        $stmt = $db->query("SELECT section_title, content, embedding FROM documentation_chunks");
        $rows = $stmt->fetchAll();

        foreach ($rows as &$row) {
            $chunkEmbedding = json_decode($row['embedding'], true);
            $row['similarity'] = self::cosineSimilarity($questionEmbedding, $chunkEmbedding);
        }
        unset($row);

        usort($rows, fn($a, $b) => $b['similarity'] <=> $a['similarity']);

        return array_slice($rows, 0, $topN);
    }

    private static function cosineSimilarity(array $a, array $b): float
    {
        $dotProduct = 0.0;
        $normA = 0.0;
        $normB = 0.0;

        foreach ($a as $i => $val) {
            $dotProduct += $val * $b[$i];
            $normA += $val ** 2;
            $normB += $b[$i] ** 2;
        }

        if ($normA == 0 || $normB == 0) {
            return 0.0;
        }

        return $dotProduct / (sqrt($normA) * sqrt($normB));
    }
}