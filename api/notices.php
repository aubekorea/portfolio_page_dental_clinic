<?php
require_once __DIR__ . '/../admin/config.php';

header('Content-Type: application/json; charset=utf-8');

try {
    $stmt = db()->query('SELECT id, title, content, is_pinned, published_at FROM notices ORDER BY is_pinned DESC, published_at DESC, id DESC LIMIT 30');
    echo json_encode(['ok' => true, 'items' => $stmt->fetchAll()], JSON_UNESCAPED_UNICODE);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['ok' => false, 'items' => []], JSON_UNESCAPED_UNICODE);
}

