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

$router->get('/users/me', AuthMiddleware::forRoles(),[UserController::class, 'getMe']);
$router->put('/users/me', AuthMiddleware::forRoles(), [UserController::class, 'updateMe']);
$router->delete('/users/me', AuthMiddleware::forRoles(), [UserController::class, 'deleteMe']);

$router->get('/users/me/watchlist', AuthMiddleware::forRoles(), [UserController::class, 'getWatchlist']);
$router->get('/users/me/watchlist-paginated', AuthMiddleware::forRoles(), [UserController::class, 'getWatchlistPagin']);

$router->post('/users/me/follows', AuthMiddleware::forRoles(), [AssetMiddleware::class, 'handle'], [UserController::class, 'followAsset']);
$router->delete('/users/me/follows/:ticker', AuthMiddleware::forRoles(), [AssetMiddleware::class, 'handle'], [UserController::class, 'unfollowAsset']);

$router->get('/users/me/follows/users', AuthMiddleware::forRoles(), [UserController::class, 'getFollowedUser']);
$router->get('/users/me/follows/users/:id/check', AuthMiddleware::forRoles(), [UserController::class, 'checkIfFollowing']);
$router->post('/users/me/follows/users/:id', AuthMiddleware::forRoles(), [UserController::class, 'followUser']);
$router->delete("/users/me/follows/users/:id", AuthMiddleware::forRoles(), [UserController::class, 'unfollowUser']);

$router->get('/users/analysts/by-type', [UserController::class, 'getAnalystsByType']);
$router->get('/users/analysts/:id', [UserController::class, 'getAnalystsById']);
$router->get('/users/analysts', [UserController::class, 'getAnalysts']);

$router->get('/users/pending-analysts', AuthMiddleware::forRoles(['admin']), [UserController::class, 'getPendingAnalyst']);
$router->get('/users', AuthMiddleware::forRoles(['admin']), [UserController::class, 'getUserPagin']);
$router->get('/users/:id', AuthMiddleware::forRoles(['admin']), [UserController::class, 'getUserById']);
$router->put('/users/:id', AuthMiddleware::forRoles(['admin']), [UserController::class, 'updateUser']);
$router->delete('/users/:id', AuthMiddleware::forRoles(['admin']), [UserController::class, 'deleteUser']);

$router->get('/assets-types', [AssetTypeController::class, 'index']);

$router->get('/assets', [AssetController::class, 'index']);
$router->get('/assets/:ticker', [AssetController::class, 'show']);