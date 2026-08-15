<?php

namespace TraderTracker\Php\Controllers;

class UserController {

    public static function me(array $params): void {
        header('Content-Type: application/json');
        echo json_encode($params['user']);
    }
}