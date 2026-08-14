<?php

use TraderTracker\Php\Controllers\AuthController;
use TraderTracker\Php\Controllers\AssetController;
use TraderTracker\Php\Controllers\AssetTypeController;

$router->get('/', function () {
    header('Content-Type: application/json');
    echo json_encode(["message" => "API running"]);
});


$router->post('/login', [AuthController::class, 'login']);
$router->post('/register', [AuthController::class, 'register']);

$router->get('/assets-types', [AssetTypeController::class, 'index']);

$router->get('/assets', [AssetController::class, 'index']);
$router->get('/assets/:ticker', [AssetController::class, 'show']);