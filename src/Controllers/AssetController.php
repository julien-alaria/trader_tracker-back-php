<?php

namespace TraderTracker\Php\Controllers;

use TraderTracker\Php\Models\AssetModel;

class AssetController {
    public static function index(): void {
        header('Content-Type: application/json');
        echo json_encode(AssetModel::getAll());
    }

    public static function show(array $params): void {
        header('Content-Type: application/json');

        $asset = AssetModel::getByTicker($params['ticker']);

        if ($asset === null) {
            http_response_code(404);
            echo json_encode(["message" => "Asset not found"]);
            return;
        }

        echo json_encode($asset);
    }
}
