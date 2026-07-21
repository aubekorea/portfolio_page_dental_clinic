<?php
require_once __DIR__ . '/../admin/config.php';

header('Content-Type: application/json; charset=utf-8');

function mask_name(string $name): string
{
    $first = mb_substr($name, 0, 1, 'UTF-8');
    return $first . 'OO';
}

try {
    $stmt = db()->query('SELECT id, name, category, title, status, is_secret, created_at FROM counsels ORDER BY created_at DESC, id DESC LIMIT 30');
    $items = array_map(static function (array $row): array {
        return [
            'id' => (int) $row['id'],
            'name' => mask_name($row['name']),
            'category' => $row['category'],
            'title' => $row['title'],
            'status' => $row['status'],
            'is_secret' => (bool) $row['is_secret'],
            'created_at' => substr($row['created_at'], 0, 10),
        ];
    }, $stmt->fetchAll());
    echo json_encode(['ok' => true, 'items' => $items], JSON_UNESCAPED_UNICODE);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['ok' => false, 'items' => []], JSON_UNESCAPED_UNICODE);
}

