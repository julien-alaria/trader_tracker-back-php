<?php

namespace TraderTracker\Php\Utils;

class Sanitizer
{
    public static function sanitizeUser(array $data): array
    {
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

    public static function sanitizeLogin(array $data): array
    {
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
}