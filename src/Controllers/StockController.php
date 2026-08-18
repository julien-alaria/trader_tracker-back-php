<?php

namespace TraderTracker\Php\Controllers;

use TraderTracker\Php\Services\StockService;

class StockController {

    public static function getAllStocks(): void {
        header('Content-Type: application/json');
        echo json_encode(['message' => StockService::getMultipleAggregateJson()]);
    }

    public static function getForex(): void {
        header('Content-Type: application/json');
        echo json_encode(['message' => StockService::aggregateForexJson()]);
    }

    public static function getCommodities(): void {
        header('Content-Type: application/json');
        echo json_encode(['message' => StockService::aggregateMetalsJson()]);
    }

    public static function getHomeStocks(): void {
        header('Content-Type: application/json');
        echo json_encode(['message' => StockService::getMultipleAggregateJsonLight()]);
    }

    public static function getHomeForex(): void {
        header('Content-Type: application/json');
        echo json_encode(['message' => StockService::aggregateForexJsonLight()]);
    }

    public static function getHomeCommodities(): void {
        header('Content-Type: application/json');
        echo json_encode(['message' => StockService::aggregateMetalsJsonLight()]);
    }

    public static function getCombinedBriefAssets(): void {
        header('Content-Type: application/json');

        $limit = max(1, (int) ($_GET['limit'] ?? 10));
        $offset = max(0, (int) ($_GET['offset'] ?? 0));

        $allAssets = array_merge(
            StockService::getBriefStocksJson(),
            StockService::getBriefForexJson(),
            StockService::getBriefCommoditiesJson()
        );

        usort($allAssets, function ($a, $b) {
            $normalize = fn($s) => strtolower(iconv('UTF-8', 'ASCII//TRANSLIT', $s ?? '') ?: ($s ?? ''));
            return strcmp($normalize($a['name']  ?? ''), $normalize($b['name'] ?? ''));
        });

        $results = array_slice($allAssets, $offset, $limit + 1);
        $hasNext = count($results) > $limit;
        if ($hasNext) array_pop($results);

        echo json_encode(['results' => $results, 'hasNext' => $hasNext]);
    }
} 