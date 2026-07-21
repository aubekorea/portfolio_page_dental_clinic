<?php
require_once __DIR__ . '/config.php';
admin_header('대시보드');

$counts = ['notices' => 0, 'wait' => 0, 'done' => 0, 'reviews' => 0];
try {
    $counts['notices'] = (int) db()->query('SELECT COUNT(*) FROM notices')->fetchColumn();
    $counts['wait'] = (int) db()->query("SELECT COUNT(*) FROM counsels WHERE status = 'wait'")->fetchColumn();
    $counts['done'] = (int) db()->query("SELECT COUNT(*) FROM counsels WHERE status = 'done'")->fetchColumn();
    $counts['reviews'] = (int) db()->query('SELECT COUNT(*) FROM reviews')->fetchColumn();
} catch (Throwable $e) {
    echo '<div class="alert">DB 연결 전입니다. config.php를 Cafe24 DB 정보로 수정하고 setup.php를 실행해주세요.</div>';
}
?>
<section class="admin_grid">
  <a class="admin_card" href="./notices.php"><p>공지사항</p><strong><?= $counts['notices'] ?></strong></a>
  <a class="admin_card" href="./counsels.php"><p>답변대기 상담</p><strong><?= $counts['wait'] ?></strong></a>
  <a class="admin_card" href="./reviews.php"><p>치료후기</p><strong><?= $counts['reviews'] ?></strong></a>
</section>
<section class="admin_panel">
  <h2>간단 운영 순서</h2>
  <p>공지사항은 저장 즉시 홈페이지 공지사항에 노출됩니다.</p>
  <p>온라인상담은 홈페이지에서 접수되며, 관리자에서 답변 상태를 변경할 수 있습니다.</p>
  <p>치료후기 이미지는 FTP로 assets/images에 올린 뒤 이미지 경로를 입력하는 방식입니다.</p>
</section>
<?php admin_footer(); ?>

