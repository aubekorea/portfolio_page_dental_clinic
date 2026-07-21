<?php
require_once __DIR__ . '/config.php';

if (is_admin()) {
    redirect_to('./index.php');
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = (string) ($_POST['password'] ?? '');

    try {
        $stmt = db()->prepare('SELECT * FROM admins WHERE username = ? LIMIT 1');
        $stmt->execute([$username]);
        $admin = $stmt->fetch();
        if ($admin && password_verify($password, $admin['password_hash'])) {
            session_regenerate_id(true);
            $_SESSION['admin_id'] = (int) $admin['id'];
            $_SESSION['admin_name'] = $admin['username'];
            redirect_to('./index.php');
        }
        $error = '아이디 또는 비밀번호를 확인해주세요.';
    } catch (Throwable $e) {
        $error = 'DB 연결을 확인해주세요. Cafe24 DB 정보 설정 또는 setup.php 실행이 필요합니다.';
    }
}
?>
<!doctype html>
<html lang="ko">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>오브치과 관리자 로그인</title>
  <link rel="stylesheet" href="./admin.css">
</head>
<body class="login_page">
  <section class="login_box">
    <h1>관리자 로그인</h1>
    <?php if ($error): ?><div class="alert"><?= h($error) ?></div><?php endif; ?>
    <form class="admin_form" method="post">
      <label><span>아이디</span><input name="username" type="text" autocomplete="username" required></label>
      <label><span>비밀번호</span><input name="password" type="password" autocomplete="current-password" required></label>
      <button class="btn" type="submit">로그인</button>
    </form>
  </section>
</body>
</html>

