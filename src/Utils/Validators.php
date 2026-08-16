<?php

namespace TraderTracker\Php\Utils;

class Validators
{
    public static function validateName(?string $name): string
    {
        if (!$name) {
            throw new AppError("Name required");
        }

        $clean = trim($name);

        if (!preg_match('/^[a-zA-ZÀ-ÿ0-9 _\'-]{2,50}$/u', $clean)) {
            throw new AppError("Invalid name");
        }

        return $clean;
    }

    public static function validateEmail(?string $email): string
    {
        if (!$email) {
            throw new AppError("Email required");
        }

        $clean = strtolower(trim($email));

        if (!preg_match('/^[^\s@]+@[^\s@]+\.[^\s@]+$/', $clean)) {
            throw new AppError("Invalid email");
        }

        return $clean;
    }

    public static function validatePassword(?string $password): string
    {
        if (!$password) {
            throw new AppError("Password required");
        }

        $clean = trim($password);

        if (!preg_match('/^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[^\da-zA-Z])\S{6,20}$/', $clean)) {
            throw new AppError("Invalid password");
        }

        return $clean;
    }

    public static function validateBio(?string $bio): ?string {
        $clean = $bio ? trim($bio) : null;

        if ($clean && strlen($clean) > 1000) {
            throw new AppError("Bio too long");
        }

        return $clean;
    }

    public static function validateCompany(?string $company): ?string {
        $clean = $company ? trim($company) : null;

        if ($clean && strlen($clean) > 100) {
            throw new AppError("Company name too long");
        }

        return $clean;
    }

    public static function safePublicRole(?string $role): string
    {
        $allowed = ["user", "analyst"];
        return in_array($role, $allowed, true) ? $role : "user";
    }

    public static function validateAnalystType(?string $role, mixed $analystTypeId): ?int
    {
        if ($role !== "analyst") {
            return null;
        }

        if ($analystTypeId === null || $analystTypeId === '') {
            throw new AppError("Analyst type required");
        }

        $id = filter_var($analystTypeId, FILTER_VALIDATE_INT);

        if ($id === false || $id <= 0) {
            throw new AppError("Invalid asset type");
        }

        return $id;
    }

    public static function safeRole(?string $role): string {
        $allowed = ['user', 'analyst', 'admin'];
        return in_array($role, $allowed, true) ? $role : "user";
    }
}