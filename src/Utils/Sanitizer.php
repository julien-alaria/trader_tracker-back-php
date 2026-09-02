<?php

namespace TraderTracker\Php\Utils;

class Sanitizer {
    public static function sanitizeUser(array $data): array {
        $name = $data['name'] ?? null;
        $email = $data['email'] ?? null;
        $password = $data['password'] ?? null;
        $role = $data['role'] ?? null;
        $analystTypeId = $data['analyst_type_id'] ?? null;
        $company = $data['company'] ?? null;
        $bio = $data['bio'] ?? null;

        if (!$name || !$email || !$password) {
            throw new AppError("Missing required fields");
        }

        $cleanName = Validators::validateName($name);
        $cleanEmail = Validators::validateEmail($email);
        $cleanPassword = Validators::validatePassword($password);
        $safeRoleValue = Validators::safePublicRole($role);
        $analystId = Validators::validateAnalystType($safeRoleValue, $analystTypeId);
        $cleanCompany = Validators::validateCompany($company);
        $cleanBio = Validators::validateBio($bio);

        return [
            'name' => $cleanName,
            'email' => $cleanEmail,
            'password' => $cleanPassword,
            'role' => $safeRoleValue,
            'analyst_type_id' => $analystId,
            'company' => $cleanCompany,
            'bio' => $cleanBio,
        ];
    }

    public static function sanitizeLogin(array $data): array {
        $email = $data['email'] ?? null;
        $password = $data['password'] ?? null;

        if (!$email || !$password) {
            throw new AppError("Missing credentials");
        }

        return [
            'email' => Validators::validateEmail($email),
            'password' => trim($password),
        ];
    }

    public static function sanitizeUserUpdate(array $data): array {

        $sanitized = [];

        if (array_key_exists('name', $data)) {
            $sanitized['name'] = Validators::validateName($data['name']);
        }

        if (array_key_exists('email', $data)) {
            $sanitized['email'] = Validators::validateEmail($data['email']);
        }

        if (array_key_exists('password', $data)) {
            $sanitized['password'] = Validators::validatePassword($data['password']);
        }

        if (array_key_exists('role', $data)) {
            $sanitized['role'] = Validators::safeRole($data['role']);
        }

        if (array_key_exists('analyst_type_id', $data)) {
            $sanitized['analyst_type_id'] = Validators::validateAnalystType($sanitized['role'] ?? $data['role'] ?? null,
            $data['analyst_type_id']);
        }

        if (array_key_exists('company', $data)) {
            $sanitized['company'] = Validators::validateCompany($data['company']);
        }

        if (array_key_exists('bio', $data)) {
            $sanitized['bio'] = Validators::validateBio($data['bio']);
        }

        return $sanitized;
    }

    public static function sanitizeRecommendation(array $data): array {

        $status = $data['status'] ?? null;
        $comment = $data['comment'] ?? null;
        $assetId = $data['asset_id'] ?? null;

        if (!$status || !$assetId) {
            throw new AppError("Missing required fields");
        }

        $assetId = filter_var($assetId, FILTER_VALIDATE_INT);

        if ($assetId === false || $assetId <= 0) {
            throw new AppError("Invalid asset id");
        }

        return [
            'status' => Validators::validateRecommendationsStatus($status),
            'comment' => Validators::validateComment($comment),
            'asset_id' => $assetId,
        ];
    }

    public static function sanitizeRecommendationUpdate(array $data): array {
        $sanitized = [];

        if (array_key_exists('status', $data)) {
            $sanitized['status'] = Validators::validateRecommendationsStatus($data['status']);
        }

        if (array_key_exists('comment', $data)) {
            $sanitized['comment'] = Validators::validateComment($data['comment']);
        }

        return $sanitized;
    }
}