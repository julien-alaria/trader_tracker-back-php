<?php

namespace TraderTracker\Php\Controllers;

use TraderTracker\Php\Models\AssetTypeModel;

class AssetTypeController {
    public static function index(): void {
        header('Content-Type: application/json');
        echo json_encode(AssetTypeModel::getAll());
    }
}