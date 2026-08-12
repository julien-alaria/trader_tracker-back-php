<?php

use TraderTracker\Php\Controllers\AssetController;
use TraderTracker\Php\Controllers\AssetTypeController;

$router->get('/', function () {
    header('Content-Type: application/json');
    echo json_encode(["message" => "API running"]);
});

$router->get('/assets-types', [AssetTypeController::class, 'index']);

$router->get('/assets', [AssetController::class, 'index']);
$router->get('/assets/:ticker', [AssetController::class, 'show']);