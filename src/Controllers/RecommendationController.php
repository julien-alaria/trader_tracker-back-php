<?php

namespace TraderTracker\Php\Controllers;

use TraderTracker\Php\Models\RecommendationModel;
use TraderTracker\Php\Models\AssetModel;
use TraderTracker\Php\Utils\Sanitizer;
use TraderTracker\Php\Utils\AppError;
use TraderTracker\Php\Utils\RequestBody;

class RecommendationController {

    public static function getRecommendationPagin(array $params): void {
        header('Content-Type: application/json');

        $ticker = $_GET['ticker'] ?? null;
        $limit = max(1, (int) ($_GET['limit'] ?? 2));
        $offset = max(0, (int) ($_GET['offset'] ?? 0));

        if ($ticker) {
            $asset = AssetModel::getByTicker($ticker);

            if (!$asset) {
                throw new AppError("Asset not found", 404);
            }

            $results = RecommendationModel::getRecommendationsByAssetId($asset['id'], $limit + 1, $offset);
            $hasNext = count($results) > $limit;
            if ($hasNext) array_pop($results);

            echo json_encode(['results' => $results, 'hasNext' => $hasNext]);
            return;
        }

        $results = RecommendationModel::getAllRecommendationsPaginated($limit, $offset);
        $hasNext = count($results) > $limit;
        if ($hasNext) array_pop($results);

        echo json_encode(['results' => $results, 'hasNext' => $hasNext]);
    }

    public static function getMyRecommendation(array $params): void {
        header('Content-Type: application/json');

        $userId = $params['user']['id'];
        $limit = max(1, (int) ($_GET['limit'] ?? 10));
        $offset = max(0, (int) ($_GET['offset'] ?? 0));

        $results = RecommendationModel::getMyRecommendationsPaginated($userId, $limit + 1, $offset);
        $hasNext = count($results) > $limit;
        if ($hasNext) array_pop($results);

        echo json_encode(['results' => $results, 'hasNext' => $hasNext]);
    }

    public static function createRecommendation(array $params): void {
        header('Content-Type: application/json');

        if (!isset($params['asset'])) {
            throw new AppError("Asset not resolved", 400);
        }

        $user = $params['user'];
        $asset = $params['asset'];

        if ($user['role'] === "analyst" && (int) $user['analyst_verified'] !== 1) {
            throw new AppError("Your analyst account is pending validation. You cannot publish any recommendations.", 403);
        }

        if ($user['role'] === "analyst" && $user['analyst_type_id'] !== $asset['asset_type_id']) {
            throw new AppError("You are not authorized to psot on this type of asset.", 403);
        }

        $body = RequestBody::parse();

        $sanitizedData = Sanitizer::sanitizeRecommendation([
            'status' => $body['status'] ?? null,
            'comment' => $body['comment'] ?? null,
            'asset_id' => $asset['id'],
        ]);

        $sanitizedData['user_id'] = $user['id'];

        $recommendation = RecommendationModel::createRecommendations($sanitizedData);

        http_response_code(201);
        echo json_encode(['recommendation' => $recommendation]);
    }

    public static function updateRecommendation(array $params): void {
        header('Content-Type: application/json');

        $id = (int) $params['id'];

        if ($id <= 0) {
            throw new AppError("Invalid ID", 400);
        }

        $body = RequestBody::parse();
        $sanitizedData = Sanitizer::sanitizeRecommendationUpdate($body);

        if (empty($sanitizedData)) {
            throw new AppError("Invalid Data", 400);
        }

        $existing = RecommendationModel::getRecommendationsById($id);

        if (!$existing) {
            throw new AppError("Recommendation not found", 404);
        }

        if ($params['user']['role'] !== "admin" && (int) $existing['user_id'] !== (int) $params['user']['id']) {
            throw new AppError("Update denied", 403);
        }

        $recommendation = RecommendationModel::updateRecommendations($id, $sanitizedData);

        echo json_encode(['recommendation' => $recommendation]);
    }

    public static function deleteRecommendation(array $params): void {
        header('Content-Type: application/json');

        $id = (int) $params['id'];

        if ($id <= 0) {
            throw new AppError("Invalid ID", 400);
        }

        $existing = RecommendationModel::getRecommendationsById($id);

        if (!$existing) {
            throw new AppError("Recommendation not found", 404);
        }

        if ($params['user']['role'] !== "admin" && (int) $existing['user_id'] !== (int) $params['user']['id']) {
            throw new AppError("Delete denied", 403);
        }

        RecommendationModel::deleteRecommendation($id);

        echo json_encode(['message' => 'delete ok']);
    }

    public static function getRecommendationsByAnalyst(array $params): void {
        header('Content-Type: application/json');

        $analystId = (int) $params['analystId'];
        $limit = max(1, (int) ($_GET['limit'] ?? 10));
        $offset = max(0, (int) ($_GET['offset'] ?? 0));

        $results = RecommendationModel::getAnalystRecommendationsById($analystId, $limit + 1, $offset);
        $hasNext = count($results) > $limit;
        if ($hasNext) array_pop($results);

        echo json_encode(['results' => $results, 'hasNext' => $hasNext]);
    }
}