<?php
require_once __DIR__ . '/../admin/config.php';

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['ok' => false, 'message' => 'POST only'], JSON_UNESCAPED_UNICODE);
    exit;
}

$name = trim($_POST['name'] ?? '');
$phone = trim($_POST['phone'] ?? '');
$category = trim($_POST['category'] ?? '기타상담');
$title = trim($_POST['title'] ?? '');
$content = trim($_POST['content'] ?? '');

if ($name === '' || $phone === '' || $title === '' || $content === '') {
    http_response_code(422);
    echo json_encode(['ok' => false, 'message' => '필수 항목을 입력해주세요.'], JSON_UNESCAPED_UNICODE);
    exit;
}

try {
    $stmt = db()->prepare('INSERT INTO counsels (name, phone, category, title, content, status, is_secret) VALUES (?, ?, ?, ?, ?, "wait", 1)');
    $stmt->execute([$name, $phone, $category, $title, $content]);
    echo json_encode(['ok' => true, 'message' => '상담이 접수되었습니다.'], JSON_UNESCAPED_UNICODE);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['ok' => false, 'message' => '상담 접수에 실패했습니다.'], JSON_UNESCAPED_UNICODE);
}

