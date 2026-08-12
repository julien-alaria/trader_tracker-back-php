<?php

namespace TraderTracker\Php\Models;

use TraderTracker\Php\Database;

class AssetTypeModel {

    public static function getAll(): array {

        $db = Database::getConnection();
        $stmt = $db->query("SELECT id, asset_type FROM assets_types");
        return $stmt->fetchAll();
    }
}