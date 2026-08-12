<?php

require __DIR__ . '/../vendor/autoload.php';

use TraderTracker\Php\Router;
use TraderTracker\Php\Database;

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

header("Access-Control-Allow-Origin: http://127.0.0.1:5501");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

$router = new Router();

$router->get('/', function () {
    header('Content-Type: application/json');
    echo json_encode(["message" => "API running"]);
});

$router->get('/assets-types', function () {
    header('Content-Type: application/json');

    $db = Database::getConnection();
    $stmt = $db->query("SELECT id, asset_type FROM assets_types");

    echo json_encode($stmt->fetchAll());
});

$router->dispatch(
    $_SERVER['REQUEST_METHOD'],
    parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH)
);