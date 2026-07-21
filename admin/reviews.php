<?php
require_once __DIR__ . '/config.php';
require_admin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $action = $_POST['action'] ?? '';
    if ($action === 'save') {
        $id = (int) ($_POST['id'] ?? 0);
        $values = [
            trim($_POST['category'] ?? ''),
            trim($_POST['title'] ?? ''),
            trim($_POST['before_image'] ?? ''),
            trim($_POST['after_image'] ?? ''),
            $_POST['before_date'] ?: null,
            $_POST['after_date'] ?: null,
            isset($_POST['is_locked']) ? 1 : 0,
            isset($_POST['is_published']) ? 1 : 0,
            (int) ($_POST['sort_order'] ?? 0),
        ];
        if ($id > 0) {
            $stmt = db()->prepare('UPDATE reviews SET category=?, title=?, before_image=?, after_image=?, before_date=?, after_date=?, is_locked=?, is_published=?, sort_order=? WHERE id=?');
            $values[] = $id;
            $stmt->execute($values);
        } else {
            $stmt = db()->prepare('INSERT INTO reviews (category, title, before_image, after_image, before_date, after_date, is_locked, is_published, sort_order) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)');
            $stmt->execute($values);
        }
    }
    if ($action === 'delete') {
        $stmt = db()->prepare('DELETE FROM reviews WHERE id=?');
        $stmt->execute([(int) ($_POST['id'] ?? 0)]);
    }
    redirect_to('./reviews.php');
}

$edit = null;
if (!empty($_GET['edit'])) {
    $stmt = db()->prepare('SELECT * FROM reviews WHERE id=?');
    $stmt->execute([(int) $_GET['edit']]);
    $edit = $stmt->fetch();
}

$rows = db()->query('SELECT * FROM reviews ORDER BY sort_order ASC, id DESC')->fetchAll();
admin_header('치료후기 관리');
?>
<section class="admin_panel">
  <h2><?= $edit ? '후기 수정' : '후기 등록' ?></h2>
  <form class="admin_form" method="post">
    <input type="hidden" name="csrf_token" value="<?= h(csrf_token()) ?>">
    <input type="hidden" name="action" value="save">
    <input type="hidden" name="id" value="<?= h((string) ($edit['id'] ?? 0)) ?>">
    <div class="admin_row">
      <label><span>카테고리</span><input name="category" value="<?= h($edit['category'] ?? '임플란트') ?>" required></label>
      <label><span>제목</span><input name="title" value="<?= h($edit['title'] ?? '') ?>" required></label>
    </div>
    <div class="admin_row">
      <label><span>BEFORE 이미지 경로</span><input name="before_image" placeholder="./assets/images/example-before.jpg" value="<?= h($edit['before_image'] ?? '') ?>" required></label>
      <label><span>AFTER 이미지 경로</span><input name="after_image" placeholder="./assets/images/example-after.jpg" value="<?= h($edit['after_image'] ?? '') ?>" required></label>
    </div>
    <div class="admin_row">
      <label><span>BEFORE 날짜</span><input name="before_date" type="date" value="<?= h($edit['before_date'] ?? '') ?>"></label>
      <label><span>AFTER 날짜</span><input name="after_date" type="date" value="<?= h($edit['after_date'] ?? '') ?>"></label>
    </div>
    <div class="admin_row">
      <label><span>정렬</span><input name="sort_order" type="number" value="<?= h((string) ($edit['sort_order'] ?? 0)) ?>"></label>
      <label><span>옵션</span><span><input type="checkbox" name="is_locked" <?= !isset($edit) || !empty($edit['is_locked']) ? 'checked' : '' ?>> 로그인 잠금 <input type="checkbox" name="is_published" <?= !isset($edit) || !empty($edit['is_published']) ? 'checked' : '' ?>> 노출</span></label>
    </div>
    <div class="admin_actions">
      <button class="btn" type="submit">저장</button>
      <?php if ($edit): ?><a class="btn ghost" href="./reviews.php">새 후기 등록</a><?php endif; ?>
    </div>
  </form>
</section>

<section class="admin_panel">
  <h2>후기 목록</h2>
  <?php if (!$rows): ?>
    <div class="empty">등록된 치료후기가 없습니다.</div>
  <?php else: ?>
    <table class="admin_table">
      <thead><tr><th>노출</th><th>카테고리</th><th>제목</th><th>정렬</th><th>관리</th></tr></thead>
      <tbody>
        <?php foreach ($rows as $row): ?>
          <tr>
            <td><span class="status <?= $row['is_published'] ? 'done' : '' ?>"><?= $row['is_published'] ? '노출' : '숨김' ?></span></td>
            <td><?= h($row['category']) ?></td>
            <td class="title"><?= h($row['title']) ?></td>
            <td><?= (int) $row['sort_order'] ?></td>
            <td>
              <a class="btn ghost" href="./reviews.php?edit=<?= (int) $row['id'] ?>">수정</a>
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
