<?php

namespace TraderTracker\Php\Middlewares;

use TraderTracker\Php\Utils\AppError;

class SpecializationMiddleware {

    public static function handle(array $params): array {

        $role = $params['user']['role'];
        $analystTypeId = $params['user']['analyst_type_id'];
        $assetTypeid = $params['asset']['asset_type_id'];

        if ($role === "admin") {
            return [];
        }

        if ($role === "analyst") {
            if ($analystTypeId !== $assetTypeid) {
                throw new AppError("Access denied: You can only recommend assets that match your specialization.", 403);
            }
            return [];
        }

        throw new AppError('Access denied: Role not authorized.', 403);
    }
}