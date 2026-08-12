<?php

use TraderTracker\Php\Models\AssetModel;

$router->get('/', function () {
    header('Content-Type: application/json');
    echo json_encode(["message" => "API running"]);
});

$router->get('/assets-types', function () {
    header('Content-Type: application/json');
    echo json_encode(AssetModel::getAllTypes());
});