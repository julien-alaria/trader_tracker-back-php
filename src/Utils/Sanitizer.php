<?php

namespace TraderTracker\Php\Utils;

class Sanitizer
{
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
}