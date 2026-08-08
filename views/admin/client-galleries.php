<?php
// views/admin/client-galleries.php - لیست گالری‌های مشتریان
$galleries = $galleries ?? [];
$csrf_token = generate_csrf_token();
$search = trim($_GET['search'] ?? '');

// Filter galleries by search term
if (!empty($search)) {
    $term = mb_strtolower($search);
    $galleries = array_filter($galleries, function($g) use ($term) {
        return mb_strpos(mb_strtolower($g['title'] ?? ''), $term) !== false
            || mb_strpos(mb_strtolower($g['user_name'] ?? ''), $term) !== false
            || mb_strpos(mb_strtolower($g['user_phone'] ?? ''), $term) !== false;
    });
    $galleries = array_values($galleries);
}
?>
<style>
    .cg-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 1rem;
        margin-bottom: 2rem;
    }
    .cg-header h1 {
        font-family: 'Cormorant Garamond', Georgia, serif;
        font-size: 2.5rem;
        font-weight: 300;
        color: #f4f1ea;
        margin: 0;
    }
    .cg-search {
        display: flex;
        gap: 0.5rem;
        align-items: center;
    }
    .cg-search input {
        padding: 0.6rem 1rem;
        background: rgba(255,255,255,0.04);
        border: 1px solid rgba(200,168,98,0.12);
        border-radius: 9999px;
        color: #f4f1ea;
        font-size: 0.85rem;
        font-family: 'Vazirmatn', sans-serif;
        min-width: 220px;
    }
    .cg-search input:focus {
        outline: none;
        border-color: #c8a862;
    }
    .cg-search button {
        padding: 0.6rem 1.2rem;
        background: linear-gradient(135deg, #c8a862, #a8893a);
        border: none;
        border-radius: 9999px;
        color: #0a0908;
        font-size: 0.8rem;
        font-weight: 600;
        cursor: pointer;
    }
    .cg-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
        gap: 1.25rem;
    }
    .cg-card {
        background: rgba(255,255,255,0.03);
        border: 1px solid rgba(200,168,98,0.08);
        border-radius: 1rem;
        padding: 1.5rem;
        transition: all 0.3s ease;
        display: flex;
        flex-direction: column;
    }
    .cg-card:hover {
        border-color: rgba(200,168,98,0.18);
        transform: translateY(-4px);
        box-shadow: 0 12px 40px rgba(0,0,0,0.25);
    }
    .cg-card-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 0.5rem;
        margin-bottom: 1rem;
    }
    .cg-card-title {
        font-size: 1.15rem;
        color: #f4f1ea;
        margin-bottom: 0.3rem;
        font-weight: 500;
    }
    .cg-card-user {
        font-size: 0.8rem;
        color: #8a8580;
    }
    .cg-badges {
        display: flex;
        flex-direction: column;
        gap: 0.35rem;
        align-items: flex-end;
    }
    .cg-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.3rem;
        padding: 0.25rem 0.7rem;
        border-radius: 9999px;
        font-size: 0.65rem;
        font-weight: 600;
    }
    .cg-badge.active { background: rgba(74,222,128,0.08); color: #4ade80; border: 1px solid rgba(74,222,128,0.15); }
    .cg-badge.inactive { background: rgba(239,68,68,0.08); color: #f87171; border: 1px solid rgba(239,68,68,0.15); }
    .cg-badge.share { background: rgba(200,168,98,0.08); color: #c8a862; border: 1px solid rgba(200,168,98,0.15); }
    .cg-badge.protected { background: rgba(59,130,246,0.08); color: #60a5fa; border: 1px solid rgba(59,130,246,0.15); }
    .cg-card-desc {
        font-size: 0.85rem;
        color: #8a8580;
        margin-bottom: 1rem;
        line-height: 1.6;
        flex: 1;
    }
    .cg-card-meta {
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 0.75rem;
        color: #8a8580;
        margin-bottom: 1rem;
        padding-bottom: 1rem;
        border-bottom: 1px solid rgba(200,168,98,0.06);
    }
    .cg-card-actions {
        display: flex;
        gap: 0.5rem;
        flex-wrap: wrap;
    }
    .cg-card-actions a,
    .cg-card-actions button {
        display: inline-flex;
        align-items: center;
        gap: 0.3rem;
        padding: 0.45rem 1rem;
        border-radius: 9999px;
        font-size: 0.75rem;
        text-decoration: none;
        cursor: pointer;
        transition: all 0.3s ease;
    }
    .cg-action-primary {
        border: 1px solid rgba(200,168,98,0.2);
        color: #c8a862;
        background: transparent;
    }
    .cg-action-primary:hover {
        background: rgba(200,168,98,0.08);
    }
    .cg-action-danger {
        border: 1px solid rgba(239,68,68,0.2);
        color: #f87171;
        background: transparent;
    }
    .cg-action-danger:hover {
        background: rgba(239,68,68,0.08);
    }
    .cg-empty {
        text-align: center;
        padding: 4rem 1.5rem;
        background: rgba(255,255,255,0.03);
        border: 1px solid rgba(200,168,98,0.08);
        border-radius: 1rem;
    }
    .cg-empty i { font-size: 3rem; color: #c8a862; margin-bottom: 1rem; opacity: 0.6; }
    .cg-empty h3 { font-family: 'Cormorant Garamond', Georgia, serif; font-size: 1.5rem; color: #f4f1ea; margin-bottom: 0.5rem; font-weight: 300; }
    .cg-empty p { color: #8a8580; font-size: 0.9rem; margin-bottom: 1.5rem; }
    .cg-stats-bar {
        display: flex;
        gap: 1rem;
        margin-bottom: 1.5rem;
        font-size: 0.8rem;
        color: #8a8580;
    }
    .cg-stats-bar span { color: #c8a862; font-weight: 600; }
</style>

<div style="max-width:1200px;margin:0 auto;padding:2rem 1.5rem;">
    <div class="cg-header">
        <h1>گالری مشتریان</h1>
        <div style="display:flex;gap:0.75rem;flex-wrap:wrap;align-items:center;">
            <form method="GET" class="cg-search">
                <input type="text" name="search" value="<?php echo h($search); ?>" placeholder="جستجو بر اساس نام، موبایل یا عنوان...">
                <button type="submit"><i class="fas fa-search"></i></button>
            </form>
            <a href="<?php echo url('admin/client-galleries/create'); ?>" style="display:inline-flex;align-items:center;gap:0.5rem;padding:0.7rem 1.6rem;background:linear-gradient(135deg,#c8a862,#a8893a);color:#0a0908;border-radius:9999px;font-size:0.8rem;font-weight:600;text-decoration:none;transition:all 0.3s ease;">
                <i class="fas fa-plus"></i> گالری جدید
            </a>
        </div>
    </div>

    <div class="cg-stats-bar">
        <span><?php echo count($galleries); ?></span> گالری یافت شد
        <?php if (!empty($search)): ?>
            — فیلتر شده برای «<?php echo h($search); ?>»
        <?php endif; ?>
    </div>

    <?php if (!empty($galleries)): ?>
        <div class="cg-grid">
            <?php foreach ($galleries as $g): ?>
                <div class="cg-card">
                    <div class="cg-card-header">
                        <div>
                            <div class="cg-card-title"><?php echo h($g['title']); ?></div>
                            <div class="cg-card-user"><?php echo h($g['user_name']); ?> • <?php echo h($g['user_phone']); ?></div>
                        </div>
                        <div class="cg-badges">
                            <span class="cg-badge <?php echo $g['status'] ? 'active' : 'inactive'; ?>">
                                <?php echo $g['status'] ? '<i class="fas fa-check"></i> فعال' : '<i class="fas fa-ban"></i> غیرفعال'; ?>
                            </span>
                            <?php if (!empty($g['share_token'])): ?>
                                <span class="cg-badge share"><i class="fas fa-link"></i> لینک</span>
                            <?php endif; ?>
                            <?php if (!empty($g['share_password'])): ?>
                                <span class="cg-badge protected"><i class="fas fa-lock"></i> رمز</span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php if (!empty($g['description'])): ?>
                        <p class="cg-card-desc"><?php echo h($g['description']); ?></p>
                    <?php endif; ?>
                    <div class="cg-card-meta">
                        <span><i class="fas fa-eye" style="margin-left:0.3rem;color:#c8a862;"></i> <?php echo number_format($g['view_count'] ?? 0); ?> بازدید</span>
                        <span><i class="far fa-clock" style="margin-left:0.3rem;"></i> <?php echo date('Y/m/d', strtotime($g['created_at'])); ?></span>
                        <?php if (!empty($g['share_expires_at'])): ?>
                            <span><i class="fas fa-hourglass-half" style="margin-left:0.3rem;color:#f87171;"></i> انقضا: <?php echo date('Y/m/d', strtotime($g['share_expires_at'])); ?></span>
                        <?php endif; ?>
                    </div>
                    <div class="cg-card-actions">
                        <a href="<?php echo url('admin/client-galleries/photos?id=' . $g['id']); ?>" class="cg-action-primary">
                            <i class="fas fa-images"></i> تصاویر
                        </a>
                        <a href="<?php echo url('admin/client-galleries/edit?id=' . $g['id']); ?>" class="cg-action-primary">
                            <i class="fas fa-edit"></i> ویرایش
                        </a>
                        <button type="button" class="delete-gallery-btn cg-action-danger" data-id="<?php echo $g['id']; ?>">
                            <i class="fas fa-trash"></i> حذف
                        </button>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="cg-empty">
            <i class="fas fa-images"></i>
            <h3>گالری ثبت نشده</h3>
            <p>اولین گالری خصوصی مشتری را بسازید.</p>
            <a href="<?php echo url('admin/client-galleries/create'); ?>" style="display:inline-flex;align-items:center;gap:0.5rem;padding:0.7rem 1.6rem;background:linear-gradient(135deg,#c8a862,#a8893a);color:#0a0908;border-radius:9999px;font-size:0.8rem;font-weight:600;text-decoration:none;">
                <i class="fas fa-plus"></i> گالری جدید
            </a>
        </div>
    <?php endif; ?>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var csrfToken = '<?php echo h($csrf_token); ?>';
    document.querySelectorAll('.delete-gallery-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            if (!confirm('آیا از حذف این گالری و تمام تصاویر آن مطمئن هستید؟')) return;
            var id = this.dataset.id;
            var formData = new FormData();
            formData.append('id', id);
            formData.append('csrf_token', csrfToken);
            fetch('<?php echo url('admin/api/client-gallery-delete'); ?>', {
                method: 'POST',
                body: formData
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    showToast('گالری حذف شد', 'success');
                    setTimeout(function() { location.reload(); }, 1200);
                } else {
                    showToast(data.message || 'خطا در حذف', 'error');
                }
            })
            .catch(() => showToast('خطا در ارتباط', 'error'));
        });
    });
});
</script>
