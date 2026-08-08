<?php
// views/auth/notifications.php - All user notifications
$user = $user ?? [];
$notifications = $notifications ?? [];
$unread_count = $unread_count ?? 0;
$typeIcons = [
    'BOOKING_STATUS' => 'fa-calendar-check',
    'GALLERY_SHARED' => 'fa-images',
    'SELECTION_CONFIRMED' => 'fa-check-double',
    'BOOKING_REQUEST' => 'fa-clipboard-check'
];
?>
<style>
    .notif-wrap { max-width: 800px; margin: 0 auto; padding: 4rem 1.25rem 6rem; }
    .notif-header {
        display: flex; justify-content: space-between; align-items: flex-end; flex-wrap: wrap; gap: 1rem;
        margin-bottom: 2rem; padding-bottom: 1.5rem; border-bottom: 1px solid rgba(200,168,98,0.08);
    }
    .notif-header h1 {
        font-family: 'Cormorant Garamond', Georgia, serif; font-size: 2rem; font-weight: 300; color: #f4f1ea; margin-bottom: 0.3rem;
    }
    .notif-header p { color: #8a8580; font-size: 0.85rem; }
    .notif-actions { display: flex; gap: 0.5rem; }
    .notif-actions button, .notif-actions a {
        padding: 0.5rem 1.2rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 600;
        background: transparent; border: 1px solid rgba(200,168,98,0.2); color: #f4f1ea;
        cursor: pointer; text-decoration: none; transition: all 0.2s ease;
    }
    .notif-actions button:hover, .notif-actions a:hover { border-color: #c8a862; color: #c8a862; }
    .notif-actions .primary { background: linear-gradient(135deg, #c8a862, #a8893a); color: #0a0908; border: none; }
    .notif-actions .primary:hover { transform: translateY(-1px); box-shadow: 0 6px 20px rgba(200,168,98,0.2); }

    .notif-list { display: grid; gap: 0.75rem; }
    .notif-item {
        display: flex; align-items: flex-start; gap: 1rem; padding: 1.1rem 1.25rem;
        background: rgba(255,255,255,0.03); border: 1px solid rgba(200,168,98,0.08);
        border-radius: 0.85rem; transition: all 0.2s ease; cursor: pointer; position: relative;
    }
    .notif-item:hover { background: rgba(255,255,255,0.05); border-color: rgba(200,168,98,0.15); transform: translateY(-1px); }
    .notif-item.unread { background: rgba(200,168,98,0.04); border-color: rgba(200,168,98,0.12); }
    .notif-item.unread::before {
        content: ''; position: absolute; top: 50%; right: 0.75rem; transform: translateY(-50%);
        width: 7px; height: 7px; border-radius: 50%; background: #c8a862;
    }
    .notif-icon {
        width: 42px; height: 42px; border-radius: 50%; flex-shrink: 0;
        background: rgba(200,168,98,0.08); border: 1px solid rgba(200,168,98,0.12);
        display: flex; align-items: center; justify-content: center; color: #c8a862; font-size: 0.95rem;
    }
    .notif-body { flex: 1; padding-left: 1rem; }
    .notif-title { font-size: 0.9rem; color: #f4f1ea; font-weight: 500; margin-bottom: 0.2rem; }
    .notif-text { font-size: 0.78rem; color: #8a8580; line-height: 1.6; }
    .notif-time { font-size: 0.7rem; color: #6b6660; margin-top: 0.4rem; }
    .notif-delete {
        width: 32px; height: 32px; border-radius: 50%; flex-shrink: 0;
        background: transparent; border: 1px solid rgba(200,168,98,0.1); color: #8a8580;
        display: flex; align-items: center; justify-content: center; cursor: pointer;
        transition: all 0.2s ease; margin-top: 0.3rem;
    }
    .notif-delete:hover { background: rgba(239,68,68,0.1); border-color: #f87171; color: #f87171; }

    .empty-state {
        text-align: center; padding: 4rem 1.5rem;
        background: rgba(255,255,255,0.03); border: 1px solid rgba(200,168,98,0.08);
        border-radius: 1rem;
    }
    .empty-state i { font-size: 3rem; color: #c8a862; margin-bottom: 1rem; opacity: 0.6; }
    .empty-state h3 { font-family: 'Cormorant Garamond', Georgia, serif; font-size: 1.5rem; color: #f4f1ea; margin-bottom: 0.5rem; font-weight: 300; }
    .empty-state p { color: #8a8580; font-size: 0.9rem; }
</style>

<div class="notif-wrap">
    <div class="notif-header">
        <div>
            <h1>اعلانات</h1>
            <p><?php echo $unread_count; ?> اعلان خوانده‌نشده دارید</p>
        </div>
        <div class="notif-actions">
            <?php if ($unread_count > 0): ?>
                <button type="button" onclick="markAllRead()">همه خواندم</button>
            <?php endif; ?>
            <a href="<?php echo url('dashboard'); ?>">بازگشت</a>
        </div>
    </div>

    <?php if (!empty($notifications)): ?>
        <div class="notif-list" id="notifList">
            <?php foreach ($notifications as $n): ?>
                <?php $icon = $typeIcons[$n['type']] ?? 'fa-bell'; ?>
                <div class="notif-item <?php echo $n['is_read'] ? '' : 'unread'; ?>" data-id="<?php echo $n['id']; ?>" onclick="markRead(<?php echo $n['id']; ?>, this)">
                    <div class="notif-icon"><i class="fas <?php echo $icon; ?>"></i></div>
                    <div class="notif-body">
                        <div class="notif-title"><?php echo h($n['title']); ?></div>
                        <div class="notif-text"><?php echo h($n['message']); ?></div>
                        <div class="notif-time"><i class="far fa-clock" style="margin-left:0.3rem;"></i><?php echo timeAgo($n['created_at']); ?> — <?php echo date('Y/m/d H:i', strtotime($n['created_at'])); ?></div>
                    </div>
                    <button type="button" class="notif-delete" onclick="deleteNotif(event, <?php echo $n['id']; ?>, this)" title="حذف">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="empty-state">
            <i class="fas fa-bell-slash"></i>
            <h3>اعلانی وجود ندارد</h3>
            <p>وقتی رویدادی رخ دهد، در اینجا نمایش داده می‌شود.</p>
        </div>
    <?php endif; ?>
</div>

<script>
(function() {
    var csrfToken = '<?php echo h($_SESSION['csrf_token'] ?? generate_csrf_token()); ?>';

    window.markRead = function(id, el) {
        var formData = new FormData();
        formData.append('id', id);
        formData.append('csrf_token', csrfToken);
        fetch('<?php echo url('notifications/mark-read'); ?>', { method: 'POST', body: formData })
            .then(r => r.json())
            .then(function(data) {
                if (data.success) el.classList.remove('unread');
            });
    };

    window.markAllRead = function() {
        var formData = new FormData();
        formData.append('csrf_token', csrfToken);
        fetch('<?php echo url('notifications/mark-all-read'); ?>', { method: 'POST', body: formData })
            .then(function() {
                document.querySelectorAll('.notif-item.unread').forEach(function(el) { el.classList.remove('unread'); });
                showToast('همه اعلانات خوانده شدند', 'success');
            });
    };

    window.deleteNotif = function(e, id, btn) {
        e.stopPropagation();
        if (!confirm('این اعلان حذف شود؟')) return;
        var formData = new FormData();
        formData.append('id', id);
        formData.append('csrf_token', csrfToken);
        fetch('<?php echo url('notifications/delete'); ?>', { method: 'POST', body: formData })
            .then(r => r.json())
            .then(function(data) {
                if (data.success) {
                    btn.closest('.notif-item').remove();
                    showToast('اعلان حذف شد', 'success');
                }
            });
    };
})();
</script>
