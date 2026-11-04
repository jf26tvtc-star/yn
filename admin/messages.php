<?php
require __DIR__ . '/partials/init.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $id = (int)($_POST['id'] ?? 0);
    if (($_POST['action'] ?? '') === 'delete') {
        $stmt = $conn->prepare('DELETE FROM messages WHERE id = ?');
        $stmt->bind_param('i', $id);
        $stmt->execute();
        flash('تم حذف الرسالة ✓');
    } else {
        $stmt = $conn->prepare('UPDATE messages SET is_read = 1 WHERE id = ?');
        $stmt->bind_param('i', $id);
        $stmt->execute();
    }
    redirect('messages.php');
}

$messages = $conn->query('SELECT * FROM messages ORDER BY created_at DESC, id DESC')->fetch_all(MYSQLI_ASSOC);

$pageTitle = 'رسائل الزوار';
$activeNav = 'messages';
require __DIR__ . '/partials/header.php';
?>
            <div class="dash-page-heading">
                <div><p class="dash-eyebrow">التواصل</p><h1>رسائل الزوار</h1><p>الرسائل المرسلة من صفحة "تواصل معنا".</p></div>
            </div>

            <section class="dash-panel">
                <?php if ($messages): ?>
                    <div class="dash-table-wrap">
                        <table class="dash-table messages-table">
                            <thead><tr><th scope="col">المرسل</th><th scope="col">الموضوع والرسالة</th><th scope="col">التاريخ</th><th scope="col">الإجراءات</th></tr></thead>
                            <tbody>
                                <?php foreach ($messages as $m): ?>
                                    <tr class="<?= $m['is_read'] ? '' : 'is-unread' ?>">
                                        <td>
                                            <strong><?= e($m['name']) ?></strong>
                                            <?php if (!$m['is_read']): ?><span class="dash-unread">جديدة</span><?php endif; ?>
                                            <small><a href="mailto:<?= e($m['email']) ?>"><?= e($m['email']) ?></a></small>
                                            <?php if (!empty($m['phone'])): ?><small dir="ltr"><?= e($m['phone']) ?></small><?php endif; ?>
                                        </td>
                                        <td>
                                            <strong><?= e($m['subject'] !== '' ? $m['subject'] : 'بدون عنوان') ?></strong>
                                            <p><?= nl2br(e($m['message'])) ?></p>
                                        </td>
                                        <td><time datetime="<?= e(date('c', strtotime($m['created_at']))) ?>"><?= e(date('Y/m/d H:i', strtotime($m['created_at']))) ?></time></td>
                                        <td><div class="panel-row-actions">
                                            <?php if (!$m['is_read']): ?>
                                                <form class="inline-form" method="post"><?= csrf_field() ?><input type="hidden" name="id" value="<?= (int)$m['id'] ?>"><button type="submit" class="panel-link">تحديد كمقروءة</button></form>
                                            <?php endif; ?>
                                            <?= delete_button('messages.php', ['id' => $m['id'], 'action' => 'delete'], 'حذف هذه الرسالة نهائيًا؟') ?>
                                        </div></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="dash-empty"><span><?= admin_icon('mail') ?></span><h3>لا توجد رسائل بعد</h3><p>ستظهر رسائل الزوار هنا عند وصولها.</p></div>
                <?php endif; ?>
            </section>
<?php require __DIR__ . '/partials/footer.php'; ?>
