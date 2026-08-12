<?php

namespace TraderTracker\Php\Controllers;

use TraderTracker\Php\Models\AssetModel;

class AssetController {

    public static function index(): void {

        header('Content-Type: Application/json');
        echo json_encode(AssetModel::getAllTypes());
    }
}

