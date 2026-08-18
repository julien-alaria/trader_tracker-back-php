<?php

namespace TraderTracker\Php\Services;

class StockService {

    private static function readJsonFile(string $fileName): array {
        $path = __DIR__ . '/../data/' . $fileName;
        $data = file_get_contents($path);
        return json_decode($data, true) ?? [];
    }

    public static function getMultipleAggregateJson(): array {

        $stocks = self::readJsonFile('nasdaq.json');
        return array_map(fn($stock) => [
            'type' => 'nasdaq',
            'ticker' => $stock['ticker'],
            'name' => $stock['name'],
            'marketCap' => $stock['marketCap'] ?? null,
            'price' => $stock['price'] ?? null,
            'high' => $stock['high'] ?? null,
            'low' => $stock['low'] ?? null,
            'image' => $stock['image'] ?: '/assets/nasdaq_logo.webp',
            'history' => $stock['history'] ?? [],
        ], $stocks);
    }

    public static function aggregateForexJson(): array {

        $forex = self::readJsonFile('forex.json');
        return array_map(fn($agg) => [
            'type' => 'forex',
            'ticker' => $agg['ticker'],
            'open' => $agg['open'] ?? null,
            'high' => $agg['high'] ?? null,
            'low' => $agg['low'] ?? null,
            'close' => $agg['close'] ?? null,
            'volume' => $agg['volume'] ?? null,
            'timestamp' => $agg['timestamp'] ?? null,
        ], $forex);
    }

    public static function aggregateMetalsJson(): array {

        $commodities = self::readJsonFile('commodities.json');
        return array_map(fn($agg) => [
            'type' => 'commodity',
            'ticker' => $agg['ticker'],
            'name' => $agg['name'],
            'price' => $agg['price'] ?? null,
            'high' => $agg['high'] ?? null,
            'low' => $agg['low'] ?? null,
            'open' => $agg['open'] ?? null,
            'close' => $agg['close'] ?? null,
        ], $commodities);
    }

    public static function getMultipleAggregateJsonLight(): array {

        $stocks = self::readJsonFile('nasdaq.json');
        return array_map(fn($stock) => [
            'type' => 'nasdaq',
            'ticker' => $stock['ticker'],
            'name' => $stock['name'],
            'marketCap' => $stock['marketCap'] ?? null,
            'price' => $stock['price'] ?? null,
            'high' => $stock['high'] ?? null,
            'low' => $stock['low'] ?? null,
            'image' => $stock['image'] ?: '/assets/nasdaq_logo.webp',
            'history' => isset($stock['history']) && is_array($stock['history']) ? array_slice($stock['history'], -15) : [],
        ], $stocks);
    }

    public static function aggregateForexJsonLight(): array {

        $forex = self::readJsonFile('forex.json');
        return array_map(fn($agg) => [
            'type' => 'forex',
            'ticker' => $agg['ticker'],
            'high' => $agg['high'] ?? null,
            'low' => $agg['low'] ?? null,
            'close' => $agg['close'] ?? null,
            'history' => isset($agg['history']) && is_array($agg['history']) ? array_slice($agg['history'], -15) : [],
        ], $forex);
    }

    public static function aggregateMetalsJsonLight(): array {

        $commodities = self::readJsonFile('commodities.json');
        return array_map(fn($agg) => [
            'type' => 'commodity',
            'ticker' => $agg['ticker'],
            'name' => $agg['name'],
            'price' => $agg['price'] ?? null,
            'high' => $agg['high'] ?? null,
            'low' => $agg['low'] ?? null,
            'close' => $agg['close'] ?? null,
            'history' => isset($agg['history']) && is_array($agg['history']) ? array_slice($agg['history'], -15) : [],
        ], $commodities);
    }

    public static function getBriefStocksJson(): array {

        $stocks = self::readJsonFile('nasdaq.json');
        return array_map(fn($s) => [
            'type' => 'nasdaq', 
            'ticker' => $s['ticker'], 
            'name' => $s['name']], 
            $stocks
        );
    }

    public static function getBriefForexJson(): array {

        $forex = self::readJsonFile('forex.json');
        return array_map(fn($f) => [
            'type' => 'forex', 
            'ticker' => $f['ticker'], 
            'name' => str_replace('EUR', 'EUR / ', str_replace('C:', '', $f['ticker'])), 
            ], $forex
        );
    }

    public static function getBriefCommoditiesJson(): array {

        $commodities = self::readJsonFile('commodities.json');
        return array_map(fn($c) => [
            'type' => 'commodity',
            'ticker' => $c['ticker'],
            'name' => $c['name']],
            $commodities
        );
    }
}