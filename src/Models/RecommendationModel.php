<?php

namespace TraderTracker\Php\Models;

use TraderTracker\Php\Database;
use TraderTracker\Php\Utils\AppError;

class RecommendationModel {

    public static function getMyRecommendationsPaginated(int $userId, int $limit = 2, int $offset = 0): array {
        $db = Database::getConnection();

        $parsedLimit = max(1, $limit ?: 2);
        $parsedOffset = max(0, $offset);

        $sql = "SELECT r.id, r.status, r.comment, r.created_at, r.asset_id, r.user_id, 
        a.ticker, a.name AS asset_name, u.name AS analyst_name, u.picture
        FROM recommendations r
        JOIN assets a ON a.id = r.asset_id
        JOIN users u ON u.id = r.user_id
        WHERE r.user_id = ?
        ORDER BY r.created_at DESC
        LIMIT ? OFFSET ?";

        $stmt = $db->prepare($sql);
        $stmt->bindValue(1, $userId, \PDO::PARAM_INT);
        $stmt->bindValue(2, $parsedLimit, \PDO::PARAM_INT);
        $stmt->bindValue(3, $parsedOffset, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public static function getAllRecommendationsPaginated(int $limit = 2, int $offset = 0): array {
        $db = Database::getConnection();

        $parsedLimit = max(1, $limit ?: 2);
        $parsedOffset = max(0, $offset);

        $sql = "SELECT r.id, r.status, r.comment, r.created_at, r.asset_id,r.user_id, a.ticker, a.name AS asset_name, u.name AS analyst_name
        FROM recommendations r
        JOIN assets a ON a.id = r.asset_id
        JOIN users u ON u.id = r.user_id
        ORDER BY r.created_at DESC
        LIMIT ? OFFSET ?";

        $stmt = $db->prepare($sql);
        $stmt->bindValue(1, $parsedLimit, \PDO::PARAM_INT);
        $stmt->bindValue(2, $parsedOffset, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public static function getRecommendationsById(int $id): ?array {
        $db = Database::getConnection();

        $sql = "SELECT r.id, r.status, r.comment, r.asset_id, r.user_id, u.name AS analyst_name
        FROM recommendations r
        JOIN users u ON u.id = r.user_id
        WHERE r.id = ?";

        $stmt = $db->prepare($sql);
        $stmt->execute([$id]);
        $result = $stmt->fetch();

        return $result ?: null;
    }

    public static function getRecommendationsByAssetId(int $assetId, int $limit = 2, int $offset = 0): array {
        $db = Database::getConnection();

        $parsedLimit = max(1, $limit ?: 2);
        $parsedOffset = max(0, $offset);

        $sql = "SELECT r.id, r.status, r.comment, r.asset_id, r.user_id, r.created_at, u.name AS analyst_name, u.picture AS analyst_picture
        FROM recommendations r
        JOIN users u ON u.id = r.user_id
        WHERE r.asset_id = ?
        ORDER BY r.created_at DESC
        LIMIT ? OFFSET ?";

        $stmt = $db->prepare($sql);
        $stmt->bindValue(1, $assetId, \PDO::PARAM_INT);
        $stmt->bindValue(2, $parsedLimit, \PDO::PARAM_INT);
        $stmt->bindValue(3, $parsedOffset, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public static function createRecommendations(array $data): array {
        $db = Database::getConnection();

        $sql = "INSERT INTO recommendations (status, comment, asset_id, user_id) VALUES (?, ?, ?, ?)";
        $stmt = $db->prepare($sql);
        $stmt->execute([$data['status'], $data['comment'], $data['asset_id'], $data['user_id']]);

        return [
            'id' => (int) $db->lastInsertId(),
            'status' => $data['status'],
            'comment' => $data['comment'],
            'asset_id' => $data['asset_id'],
            'user_id' => $data['user_id'],
        ];
    }

    public static function updateRecommendations(int $id, array $data): array {
        $db = Database::getConnection();

        $fields = [];
        $values = [];

        if (array_key_exists('status', $data)) {
            $fields[] = "status = ?";
            $values[] = $data['status'];
        }

        if (array_key_exists('comment', $data)) {
            $fields[] = "comment = ?";
            $values[] = $data['comment'];
        }

        if (empty($fields)) {
            throw new AppError("No fields to update");
        }

        $values[] = $id;

        $sql = "UPDATE recommendations SET " . implode(", ", $fields) . " WHERE id = ?";
        $stmt = $db->prepare($sql);
        $stmt->execute($values);

        if ($stmt->rowCount() === 0) {
            throw new AppError("Recommendation not found");
        }

        return ['affectedRows' => $stmt->rowCount()];
    }

    public static function deleteRecommendation(int $id): void {
        $db = Database::getConnection();

        $stmt = $db->prepare("DELETE FROM recommendations WHERE id = ?");
        $stmt->execute([$id]);

        if ($stmt->rowCount() === 0) {
            throw new AppError("Recommendation not found");
        }
    }

    public static function getAnalystRecommendationsById(int $analystId, int $limit, int $offset): array {
        $db = Database::getConnection();

        $sql = "SELECT r.id, r.status, r.comment, r.asset_id, r.user_id, r.created_at, a.ticker, a.name AS asset_name, u.name AS analyst_name
        FROM recommendations r
        JOIN assets a ON a.id = r.asset_id
        JOIN users u ON u.id = r.user_id
        WHERE r.user_id = ?
        ORDER BY r.created_at DESC
        LIMIT ? OFFSET ?";

        $stmt = $db->prepare($sql);
        $stmt->bindValue(1, $analystId, \PDO::PARAM_INT);
        $stmt->bindValue(2, $limit, \PDO::PARAM_INT);
        $stmt->bindValue(3, $offset, \PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }
}