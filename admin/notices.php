<?php
require_once __DIR__ . '/config.php';
require_admin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $action = $_POST['action'] ?? '';
    if ($action === 'save') {
        $id = (int) ($_POST['id'] ?? 0);
        $title = trim($_POST['title'] ?? '');
        $content = trim($_POST['content'] ?? '');
        $publishedAt = $_POST['published_at'] ?: date('Y-m-d');
        $isPinned = isset($_POST['is_pinned']) ? 1 : 0;

        if ($id > 0) {
            $stmt = db()->prepare('UPDATE notices SET title=?, content=?, published_at=?, is_pinned=? WHERE id=?');
            $stmt->execute([$title, $content, $publishedAt, $isPinned, $id]);
        } else {
            $stmt = db()->prepare('INSERT INTO notices (title, content, published_at, is_pinned) VALUES (?, ?, ?, ?)');
            $stmt->execute([$title, $content, $publishedAt, $isPinned]);
        }
    }
    if ($action === 'delete') {
        $stmt = db()->prepare('DELETE FROM notices WHERE id=?');
        $stmt->execute([(int) ($_POST['id'] ?? 0)]);
    }
    redirect_to('./notices.php');
}

$edit = null;
if (!empty($_GET['edit'])) {
    $stmt = db()->prepare('SELECT * FROM notices WHERE id=?');
    $stmt->execute([(int) $_GET['edit']]);
    $edit = $stmt->fetch();
}

$rows = db()->query('SELECT * FROM notices ORDER BY is_pinned DESC, published_at DESC, id DESC')->fetchAll();
admin_header('공지사항 관리');
?>
<section class="admin_panel">
  <h2><?= $edit ? '공지 수정' : '공지 등록' ?></h2>
  <form class="admin_form" method="post">
    <input type="hidden" name="csrf_token" value="<?= h(csrf_token()) ?>">
    <input type="hidden" name="action" value="save">
    <input type="hidden" name="id" value="<?= h((string) ($edit['id'] ?? 0)) ?>">
    <div class="admin_row">
      <label><span>제목</span><input name="title" value="<?= h($edit['title'] ?? '') ?>" required></label>
      <label><span>게시일</span><input name="published_at" type="date" value="<?= h($edit['published_at'] ?? date('Y-m-d')) ?>" required></label>
    </div>
    <label><span>내용</span><textarea name="content"><?= h($edit['content'] ?? '') ?></textarea></label>
    <label><span><input type="checkbox" name="is_pinned" <?= !empty($edit['is_pinned']) ? 'checked' : '' ?>> 상단 공지로 고정</span></label>
    <div class="admin_actions">
      <button class="btn" type="submit">저장</button>
      <?php if ($edit): ?><a class="btn ghost" href="./notices.php">새 글 등록</a><?php endif; ?>
    </div>
  </form>
</section>

<section class="admin_panel">
  <h2>공지 목록</h2>
  <?php if (!$rows): ?>
    <div class="empty">등록된 공지가 없습니다.</div>
  <?php else: ?>
    <table class="admin_table">
      <thead><tr><th>상태</th><th>제목</th><th>게시일</th><th>관리</th></tr></thead>
      <tbody>
        <?php foreach ($rows as $row): ?>
          <tr>
            <td><?= $row['is_pinned'] ? '<span class="status done">공지</span>' : '<span class="status">일반</span>' ?></td>
            <td class="title"><?= h($row['title']) ?></td>
            <td><?= h($row['published_at']) ?></td>
            <td>
              <a class="btn ghost" href="./notices.php?edit=<?= (int) $row['id'] ?>">수정</a>
              <form method="post" style="display:inline" onsubmit="return confirm('삭제할까요?');">
                <input type="hidden" name="csrf_token" value="<?= h(csrf_token()) ?>">
                <input type="hidden" name="action" value="delete">
                <input type="hidden" name="id" value="<?= (int) $row['id'] ?>">
                <button class="btn danger" type="submit">삭제</button>
              </form>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  <?php endif; ?>
</section>
<?php admin_footer(); ?>

