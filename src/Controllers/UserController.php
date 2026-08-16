<?php

namespace TraderTracker\Php\Controllers;

use TraderTracker\Php\Models\UserModel;
use TraderTracker\Php\Utils\AppError;
use TraderTracker\Php\Utils\Sanitizer;

class UserController {

    public static function getMe(array $params): void {
        header('Content-Type: application/json');
        $userId = $params['user']['id'] ?? null;

        if (!$userId) {
            throw new AppError("Unauthorized", 401);
        }

        $result = UserModel::getUsersById($userId);

        if (!$result) {
            throw new AppError("User not found", 404);
        }

        echo json_encode(['result' => $result]);
    }

    public static function updateMe(array $params): void {
        header('Content-Type: application/json');
        $userId = $params['user']['id'];

        $body = json_decode(file_get_contents('php://input'), true) ?? [];
        $sanitizedData = Sanitizer::sanitizeUserUpdate($body);

        unset($sanitizedData['role']);
        unset($sanitizedData['analyst_type_id']);

        if (empty($sanitizedData)) {
            throw new AppError("No valid data", 400);
        }

        $result = UserModel::updateUsers($userId, $sanitizedData);

        echo json_encode(['message' => 'Profile successfully updated', 'result' => $result]);
    }

    public static function deleteMe(array $params): void {
        header('Content-Type: application/json');

        UserModel::deleteUsers($params['user']['id']);
        echo json_encode(['message' => 'Account deleted successfully']);

    }

    public static function getWatchlist(array $params): void {
        header('Content-Type: application/json');
        $result = UserModel::getUserWatchlist($params['user']['id']);
        echo json_encode(['result' => $result]);
    }

    public static function getWatchlistPagin(array $params): void {
        header('Content-Type: application/json');
        $limit = max(1, (int) ($_GET['limit'] ?? 10));
        $offset = max(0, (int) ($_GET['offset'] ?? 0));

        $results = UserModel::getUserWatchlistPaginated($params['user']['id'], $limit + 1, $offset);
        $hasNext = count($results) > $limit;
        if ($hasNext) array_pop($results);

        echo json_encode(['results' => $results, 'hasNext' => $hasNext]);
    }

    public static function followAsset(array $params): void {
        header('Content-Type: application/json');
        try {
            UserModel::userFollowAsset($params['user']['id'], $params['asset']['id']);
            http_response_code(201);
            echo json_encode(['message' => 'asset added to favorites']);
        } catch (\PDOException $e) {
            if ($e->getCode() === '23000') {
                throw new AppError("Asset already followed", 409);
            }
            throw $e;
        }
    }

    public static function unfollowAsset(array $params): void {
        header('Content-Type: application/json');
        UserModel::userUnfollowAsset($params['user']['id'], $params['asset']['id']);
        echo json_encode(['message'  => 'asset removed from favorites (or was not followed)']);
    }

    public static function getFollowedUser(array $params): void {
        header('Content-Type: application/json');
        $limit = max(1, (int) ($_GET['limit'] ?? 10));
        $offset = max(0, (int) ($_GET['offset'] ?? 0));

        $data = UserModel::getFollowedUsers($params['user']['id'], $limit, $offset);
        echo json_encode($data);
    }

    public static function followUser(array $params): void {
        header('Content-Type: application/json');
        $userId = $params['user']['id'];
        $followUserId = (int) $params['id'];

        if ($userId === $followUserId) {
            throw new AppError("Cannot follow yourself", 400);
        }

        try {
            UserModel::userFollowUser($userId, $followUserId);
            http_response_code(201);
            echo json_encode(['message' => 'user folowed successfully']);
        } catch (\PDOException $e) {
            if ($e->getCode() === '23000') {
                throw new AppError("User already followed", 409);
            }
            throw $e;
        }
    }

    public static function unfollowUser(array $params): void {
        header('Content-Type: application/json');
        UserModel::userUnfollowUser($params['user']['id'], (int) $params['id']);
        echo json_encode(['message' => 'user unfollowed successfully']);
    }

    public static function checkIfFollowing(array $params): void {
        header('Content-Type: application/json');
        $isFollowing = UserModel::isFollowing($params['user']['id'], (int) $params['id']);
        echo json_encode(['isFollowing' => $isFollowing]);
    }
}