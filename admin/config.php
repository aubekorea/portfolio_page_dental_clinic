<?php
declare(strict_types=1);

session_start();
date_default_timezone_set('Asia/Seoul');

const AUBE_DB_HOST = 'localhost';
const AUBE_DB_NAME = 'cafe24_db_name';
const AUBE_DB_USER = 'cafe24_user';
const AUBE_DB_PASS = 'cafe24_password';
const AUBE_DB_CHARSET = 'utf8mb4';

// Change this before uploading setup.php to Cafe24.
const AUBE_SETUP_KEY = 'change-this-setup-key';

function db(): PDO
{
    static $pdo = null;
    if ($pdo instanceof PDO) {
        return $pdo;
    }

    $dsn = 'mysql:host=' . AUBE_DB_HOST . ';dbname=' . AUBE_DB_NAME . ';charset=' . AUBE_DB_CHARSET;
    $pdo = new PDO($dsn, AUBE_DB_USER, AUBE_DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);

    return $pdo;
}

function h(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function redirect_to(string $path): void
{
    header('Location: ' . $path);
    exit;
}

function is_admin(): bool
{
    return !empty($_SESSION['admin_id']);
}

function require_admin(): void
{
    if (!is_admin()) {
        redirect_to('./login.php');
    }
}

function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(24));
    }
    return $_SESSION['csrf_token'];
}

function verify_csrf(): void
{
    $token = $_POST['csrf_token'] ?? '';
    if (!hash_equals($_SESSION['csrf_token'] ?? '', $token)) {
        http_response_code(403);
        exit('잘못된 요청입니다.');
    }
}

function admin_header(string $title): void
{
    require_admin();
    $active = basename($_SERVER['SCRIPT_NAME']);
    ?>
<!doctype html>
<html lang="ko">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>오브치과 관리자 | <?= h($title) ?></title>
  <link rel="stylesheet" href="./admin.css">
</head>
<body>
  <aside class="admin_side">
    <a class="admin_logo" href="./index.php"><img src="../assets/images/ove_logo.png" alt="오브치과"><strong>관리자</strong></a>
    <nav>
      <a class="<?= $active === 'index.php' ? 'active' : '' ?>" href="./index.php">대시보드</a>
      <a class="<?= $active === 'notices.php' ? 'active' : '' ?>" href="./notices.php">공지사항 관리</a>
      <a class="<?= $active === 'counsels.php' ? 'active' : '' ?>" href="./counsels.php">온라인상담 관리</a>
      <a class="<?= $active === 'reviews.php' ? 'active' : '' ?>" href="./reviews.php">치료후기 관리</a>
      <a href="../index.html" target="_blank">홈페이지 보기</a>
      <a href="./logout.php">로그아웃</a>
    </nav>
  </aside>
  <main class="admin_main">
    <header class="admin_top">
      <p>AUBE DENTAL ADMIN</p>
      <h1><?= h($title) ?></h1>
    </header>
    <?php
}

function admin_footer(): void
{
    ?>
  </main>
</body>
</html>
    <?php
}
