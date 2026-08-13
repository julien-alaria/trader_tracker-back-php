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
            false, $company, $bio, $picture, $document
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
}