<?php

namespace TraderTracker\Php\Models;

use TraderTracker\Php\Database;
use TraderTracker\Php\Utils\AppError;
use TraderTracker\Php\Utils\Password;

class UserModel {

    public static function getUsersByEmail(string $email): ?array {
        $db = Database::getConnection();

        $sql = "SELECT id, name, email, password, role, analyst_type_id, analyst_verified, company, bio
                FROM users WHERE email = ?";

        $stmt = $db->prepare($sql);
        $stmt->execute([$email]);
        $result = $stmt->fetch();

        return $result ?: null;
    }

    public static function createUsers(array $data): array {
        $db = Database::getConnection();

        $name = $data['name'];
        $email = $data['email'];
        $password = $data['password'];
        $role = $data['role'];
        $analystTypeId = $data['analyst_type_id'];
        $company = $data['company'];
        $bio = $data['bio'];
        $picture = $data['picture'] ?? null;
        $document = $data['document'] ?? null;

        $checkStmt = $db->prepare("SELECT id FROM users WHERE email = ?");
        $checkStmt->execute([$email]);

        if ($checkStmt->fetch() !== false) {
            throw new AppError("Email already used", 409);
        }

        $hashedPassword = Password::hash($password);

        if ($role === "analyst") {
            $typeStmt = $db->prepare("SELECT id FROM assets_types WHERE id = ?");
            $typeStmt->execute([$analystTypeId]);

            if ($typeStmt->fetch() === false) {
                throw new AppError("Invalid asset type");
            }
        }

        $sql = "INSERT INTO users (name, email, password, role, analyst_type_id, analyst_verified, company, bio, picture, document)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $db->prepare($sql);
        $stmt->execute([
            $name, $email, $hashedPassword, $role, $analystTypeId,
            0, $company, $bio, $picture, $document
        ]);

        return [
            'id' => (int) $db->lastInsertId(),
            'name' => $name,
            'email' => $email,
            'role' => $role,
            'analyst_type_id' => $analystTypeId,
            'picture' => $picture,
            'document' => $document,
        ];
    }

    public static function getUsersById(int $id): ?array {
        $db = Database::getConnection();

        $sql = "SELECT id, name, email, role, analyst_type_id, analyst_verified, company, bio, picture FROM users WHERE id = ?";

        $stmt = $db->prepare($sql);
        $stmt->execute([$id]);
        $result = $stmt->fetch();

        return $result ?: null;


    }

    public static function getUserWatchlist(int $userId): array {
        $db = Database::getConnection();

        $sql = "SELECT assets.id, assets.ticker, assets.name, assets.asset_type_id
        FROM assets
        JOIN users_assets_follow ON users_assets_follow.asset_id = assets.id
        WHERE users_assets_follow.user_id = ?";

        $stmt = $db->prepare($sql);
        $stmt->execute([$userId]);

        return $stmt->fetchAll();
    }

    public static function getUserWatchlistPaginated(int $userId, int $limit, int $offset): array {
        $db = Database::getConnection();

        $sql = "SELECT assets.id, assets.ticker, assets.name, assets.asset_type_id
        FROM assets
        JOIN users_assets_follow ON users_assets_follow.asset_id = assets.id
        WHERE users_assets_follow.user_id = ?
        ORDER BY assets.name ASC
        LIMIT ? OFFSET ?";

        $stmt = $db->prepare($sql);
        $stmt->bindValue(1, $userId, \PDO::PARAM_INT);
        $stmt->bindValue(2, $limit, \PDO::PARAM_INT);
        $stmt->bindValue(3, $offset, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public static function userFollowAsset(int $userId, int $assetId): void {
        $db = Database::getConnection();

        $stmt = $db->prepare("INSERT INTO users_assets_follow (user_id, asset_id) VALUES (?, ?)");
        $stmt->execute([$userId, $assetId]);
    }

    public static function userUnfollowAsset(int $userId, int $assetId): void {
        $db = Database::getConnection();

        $stmt = $db->prepare("DELETE FROM users_assets_follow WHERE user_id = ? AND asset_id = ?");
        $stmt->execute([$userId, $assetId]);
    }

    public static function isFollowing(int $userId, int $followuserId): bool {
        $db = Database::getConnection();

        $stmt = $db->prepare("SELECT 1 FROM user_follows WHERE follower_id = ? AND followed_id = ? LIMIT 1");
        $stmt->execute([$userId, $followuserId]);

        return $stmt->fetch() !== false; 
    }

    public static function getFollowedUsers(int $userId, int $limit, int $offset): array {
        $db = Database::getConnection();

        $parsedLimit = max(1, $limit ?: 5);
        $parsedOffset = max(0, $offset);

        $sql = "SELECT u.id, u.name, u.company, u.bio, u.analyst_verified, u.picture
        FROM user_follows uf
        JOIN users u ON u.id = uf.followed_id
        WHERE uf.follower_id = ?
        LIMIT ? OFFSET ?";

        $stmt = $db->prepare($sql);
        $stmt->bindValue(1, $userId, \PDO::PARAM_INT);
        $stmt->bindValue(2, $parsedLimit + 1, \PDO::PARAM_INT);
        $stmt->bindValue(3, $parsedOffset, \PDO::PARAM_INT);
        $stmt->execute();
        $rows = $stmt->fetchAll();

        $hasNext = count($rows) > $parsedLimit;
        if ($hasNext) array_pop($rows);

        return ['results' => $rows, 'hasNext' => $hasNext];
    }

    public static function userFollowUser(int $userId, int $followuserId): void {
        $db = Database::getConnection();

        $stmt = $db->prepare("INSERT INTO user_follows (follower_id, followed_id) VALUES (?, ?)");
        $stmt->execute([$userId, $followuserId]);
    }

    public static function userUnfollowUser(int $userId, int $followuserId): void {
        $db = Database::getConnection();

        $stmt = $db->prepare("DELETE FROM user_follows WHERE follower_id = ? AND followed_id = ?");
        $stmt->execute([$userId, $followuserId]);
    }
}