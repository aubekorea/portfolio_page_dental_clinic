<?php
require_once __DIR__ . '/config.php';
require_admin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $action = $_POST['action'] ?? '';
    if ($action === 'answer') {
        $id = (int) ($_POST['id'] ?? 0);
        $status = $_POST['status'] === 'done' ? 'done' : 'wait';
        $answer = trim($_POST['answer'] ?? '');
        $stmt = db()->prepare("UPDATE counsels SET status=?, answer=?, answered_at=IF(?='done', NOW(), NULL) WHERE id=?");
        $stmt->execute([$status, $answer, $status, $id]);
    }
    if ($action === 'delete') {
        $stmt = db()->prepare('DELETE FROM counsels WHERE id=?');
        $stmt->execute([(int) ($_POST['id'] ?? 0)]);
    }
    redirect_to('./counsels.php');
}

$selected = null;
if (!empty($_GET['id'])) {
    $stmt = db()->prepare('SELECT * FROM counsels WHERE id=?');
    $stmt->execute([(int) $_GET['id']]);
    $selected = $stmt->fetch();
}

$rows = db()->query('SELECT * FROM counsels ORDER BY created_at DESC, id DESC')->fetchAll();
admin_header('온라인상담 관리');
?>
<section class="admin_panel">
  <h2>상담 목록</h2>
  <?php if (!$rows): ?>
    <div class="empty">접수된 상담이 없습니다.</div>
  <?php else: ?>
    <table class="admin_table">
      <thead><tr><th>상태</th><th>제목</th><th>이름</th><th>연락처</th><th>접수일</th><th>관리</th></tr></thead>
      <tbody>
        <?php foreach ($rows as $row): ?>
          <tr>
            <td><span class="status <?= $row['status'] === 'done' ? 'done' : '' ?>"><?= $row['status'] === 'done' ? '답변완료' : '답변대기' ?></span></td>
            <td class="title"><?= h($row['title']) ?></td>
            <td><?= h($row['name']) ?></td>
            <td><?= h($row['phone']) ?></td>
            <td><?= h(substr($row['created_at'], 0, 10)) ?></td>
            <td><a class="btn ghost" href="./counsels.php?id=<?= (int) $row['id'] ?>">보기</a></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  <?php endif; ?>
</section>

<?php if ($selected): ?>
<section class="admin_panel">
  <h2>상담 답변</h2>
  <form class="admin_form" method="post">
    <input type="hidden" name="csrf_token" value="<?= h(csrf_token()) ?>">
    <input type="hidden" name="action" value="answer">
    <input type="hidden" name="id" value="<?= (int) $selected['id'] ?>">
    <div class="admin_row">
      <label><span>상태</span><select name="status"><option value="wait" <?= $selected['status'] === 'wait' ? 'selected' : '' ?>>답변대기</option><option value="done" <?= $selected['status'] === 'done' ? 'selected' : '' ?>>답변완료</option></select></label>
      <label><span>분야</span><input value="<?= h($selected['category']) ?>" readonly></label>
    </div>
    <label><span>문의 제목</span><input value="<?= h($selected['title']) ?>" readonly></label>
    <label><span>문의 내용</span><textarea readonly><?= h($selected['content']) ?></textarea></label>
    <label><span>답변</span><textarea name="answer"><?= h($selected['answer']) ?></textarea></label>
    <div class="admin_actions">
      <button class="btn" type="submit">답변 저장</button>
    </div>
  </form>
  <form method="post" onsubmit="return confirm('상담글을 삭제할까요?');">
    <input type="hidden" name="csrf_token" value="<?= h(csrf_token()) ?>">
    <input type="hidden" name="action" value="delete">
    <input type="hidden" name="id" value="<?= (int) $selected['id'] ?>">
    <button class="btn danger" type="submit">삭제</button>
  </form>
</section>
<?php endif; ?>
<?php admin_footer(); ?>
