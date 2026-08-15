<?php

namespace TraderTracker\Php\Middlewares;

use TraderTracker\Php\Models\AssetModel;
use TraderTracker\Php\Utils\AppError;

class AssetMiddleware {

    public static function handle(array $params): array {
        $ticker = $params['ticker'] ?? null;

        if (!$ticker) {
            $body = json_decode(file_get_contents('php://input'), true) ?? [];
            $ticker = $body['ticker'] ?? null;
        }

        if (!$ticker) {
            throw new AppError("ticker required", 400);
        }

        $asset = AssetModel::getByTicker($ticker);

        if (!$asset) {
            throw new AppError("Asset not found", 404);
        }

        return ['asset' => $asset];
    }
}