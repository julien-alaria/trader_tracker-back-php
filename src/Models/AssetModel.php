<?php

namespace TraderTracker\Php\Models;

use TraderTracker\Php\Database;

class AssetModel {
    public static function getAll(): array
    {
        $db = Database::getConnection();
        $stmt = $db->query("SELECT id, name, ticker, asset_type_id FROM assets");
        return $stmt->fetchAll();
    }

    public static function getByTicker(string $ticker): ?array
    {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT id, name, ticker, asset_type_id FROM assets WHERE ticker = ?");
        $stmt->execute([$ticker]);
        $result = $stmt->fetch();
        return $result ?: null;
    }
}