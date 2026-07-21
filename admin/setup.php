<?php
require_once __DIR__ . '/config.php';

$key = $_GET['key'] ?? '';
if (!hash_equals(AUBE_SETUP_KEY, $key)) {
    http_response_code(403);
    exit('setup key가 필요합니다. config.php의 AUBE_SETUP_KEY를 변경한 뒤 ?key=값 으로 접속하세요.');
}

$pdo = db();

$pdo->exec("
CREATE TABLE IF NOT EXISTS admins (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(50) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
");

$pdo->exec("
CREATE TABLE IF NOT EXISTS notices (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(255) NOT NULL,
  content TEXT NULL,
  is_pinned TINYINT(1) NOT NULL DEFAULT 0,
  published_at DATE NOT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_notice_order (is_pinned, published_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
");

$pdo->exec("
CREATE TABLE IF NOT EXISTS counsels (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(80) NOT NULL,
  phone VARCHAR(40) NOT NULL,
  category VARCHAR(80) NOT NULL,
  title VARCHAR(255) NOT NULL,
  content TEXT NOT NULL,
  status ENUM('wait','done') NOT NULL DEFAULT 'wait',
  answer TEXT NULL,
  is_secret TINYINT(1) NOT NULL DEFAULT 1,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  answered_at DATETIME NULL,
  INDEX idx_counsel_order (status, created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
");

$pdo->exec("
CREATE TABLE IF NOT EXISTS reviews (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  category VARCHAR(100) NOT NULL,
  title VARCHAR(255) NOT NULL,
  before_image VARCHAR(255) NOT NULL,
  after_image VARCHAR(255) NOT NULL,
  before_date DATE NULL,
  after_date DATE NULL,
  is_locked TINYINT(1) NOT NULL DEFAULT 1,
  is_published TINYINT(1) NOT NULL DEFAULT 1,
  sort_order INT NOT NULL DEFAULT 0,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_review_order (is_published, sort_order, id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
");

$stmt = $pdo->prepare("SELECT COUNT(*) FROM admins WHERE username = 'admin'");
$stmt->execute();
if ((int) $stmt->fetchColumn() === 0) {
    $passwordHash = password_hash('admin1234', PASSWORD_DEFAULT);
    $insert = $pdo->prepare('INSERT INTO admins (username, password_hash) VALUES (?, ?)');
    $insert->execute(['admin', $passwordHash]);
}

echo '설치가 완료되었습니다. 기본 계정은 admin / admin1234 입니다. 로그인 후 setup.php는 서버에서 삭제하세요.';

