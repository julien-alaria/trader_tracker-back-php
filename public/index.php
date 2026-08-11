<?php

require __DIR__ . '/../vendor/autoload.php';

use TraderTracker\Php\Router;

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

$router = new Router();

$router->get('/', function () {
    header('Content-Type: application/json');
    echo json_encode(["message" => "API running"]);
});

$router->dispatch(
    $_SERVER['REQUEST_METHOD'],
    parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH)
);