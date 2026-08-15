<?php

use TraderTracker\Php\Controllers\AuthController;
use TraderTracker\Php\Controllers\AssetController;
use TraderTracker\Php\Controllers\AssetTypeController;
use TraderTracker\Php\Controllers\UserController;
use TraderTracker\Php\Middlewares\AuthMiddleware;
use TraderTracker\Php\Middlewares\AssetMiddleware;


$router->get('/', function () {
    header('Content-Type: application/json');
    echo json_encode(["message" => "API running"]);
});

$router->post('/auth/login', [AuthController::class, 'login']);
$router->post('/auth/register', [AuthController::class, 'register']);

$router->get('/users/me', [AuthMiddleware::class, 'handle'], [UserController::class, 'me']);

$router->get('/users/me/watchlist', [AuthMiddleware::class, 'handle'], [UserController::class, 'getWatchlist']);
$router->get('/users/me/watchlist-paginated', [AuthMiddleware::class, 'handle'], [UserController::class, 'getWatchlistPagin']);

$router->post('/users/me/follows', [AuthMiddleware::class, 'handle'], [AssetMiddleware::class, 'handle'], [UserController::class, 'followAsset']);
$router->delete('/users/me/follows/:ticker', [AuthMiddleware::class, 'handle'], [AssetMiddleware::class, 'handle'], [UserController::class, 'unfollowAsset']);

$router->get('/users/me/follows/users', [AuthMiddleware::class, 'handle'], [UserController::class, 'getFollowedUser']);
$router->get('/users/me/follows/users/:id/check', [AuthMiddleware::class, 'handle'], [UserController::class, 'checkIfFollowing']);
$router->post('/users/me/follows/users/:id', [AuthMiddleware::class, 'handle'], [UserController::class, 'followUser']);
$router->delete("/users/me/follows/users/:id", [AuthMiddleware::class, 'handle'], [UserController::class, 'unfollowUser']);


$router->get('/assets-types', [AssetTypeController::class, 'index']);

$router->get('/assets', [AssetController::class, 'index']);
$router->get('/assets/:ticker', [AssetController::class, 'show']);