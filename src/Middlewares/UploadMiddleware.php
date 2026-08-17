<?php

namespace TraderTracker\Php\Middlewares;

use TraderTracker\Php\Utils\AppError;

class UploadMiddleware {

    private const MIME_TO_EXT = [
        'image/jpeg' => '.jpg',
        'image/png' => '.png',
        'image/webp' => '.webp',
        'application/pdf' => '.pdf',
    ];

    private const ALLOWED_EXT = '/^\.(jpe?g|png|webp|pdf)$/i'; 
    private const ALLOWED_MIME = '/^(image\/(jpeg|png|webp)|application\/pdf)$/i';
    private const MAX_SIZE = 5 * 1024 * 1024;

    public static function forFields(array $fieldNames): callable {

        return function (array $params) use ($fieldNames) {
            $uploaded = [];

            foreach ($fieldNames as $field) {
                if (!isset($_FILES[$field]) || $_FILES[$field]['error'] === UPLOAD_ERR_NO_FILE) {
                    continue;
                }

                $uploaded[$field] = self::handleSingleFile($_FILES[$field], $field);
            }

            return $uploaded;

        };
    }

    private static function handleSingleFile(array $file, string $fieldName): string {

        if ($file['error'] !== UPLOAD_ERR_OK) {
            if (in_array($file['error'], [UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE], true)) {
                throw new AppError("File too large. Maximum allowed size is 5 MB.", 400);
            }
            throw new AppError("Upload error", 400);
        }

        if ($file['size'] > self::MAX_SIZE) {
            throw new AppError("File too large. Maximum allowed size is 5 MB.", 400);
        }

        $extOk = preg_match(self::ALLOWED_EXT, '.' . pathinfo($file['name'], PATHINFO_EXTENSION));
        $mimeOk = preg_match(self::ALLOWED_MIME, $file['type']);

        if (!$extOk || !$mimeOk) {
            throw new AppError("Only images (jpeg, jpg, png, webp) and PDF are authorized", 400);
        }

        $ext = self::MIME_TO_EXT[$file['type']] ?? '';
        $userEmail = isset($_POST['email']) ? strtolower(preg_replace('/[@.]/', '-', $_POST['email'])) : 'anonymous';

        $uniqueSuffix = time() . '-' . random_int(0, 999999999);
        $filename = "{$fieldName}-{$userEmail}-{$uniqueSuffix}{$ext}";

        $uploadDir = __DIR__ . '/../../public/uploads/';

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        if (!move_uploaded_file($file['tmp_name'], $uploadDir . $filename)) {
            throw new AppError("Failed to save uploaded file", 500);
        }

        return $filename;
    }
}