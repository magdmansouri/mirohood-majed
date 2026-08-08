<?php
// views/auth/downloads.php - Download history
$user = $user ?? [];
$downloads = $downloads ?? [];
?>
<style>
    .dl-wrap { max-width: 800px; margin: 0 auto; padding: 4rem 1.25rem 6rem; }
    .dl-header {
        display: flex; justify-content: space-between; align-items: flex-end; flex-wrap: wrap; gap: 1rem;
        margin-bottom: 2rem; padding-bottom: 1.5rem; border-bottom: 1px solid rgba(200,168,98,0.08);
    }
    .dl-header h1 {
        font-family: 'Cormorant Garamond', Georgia, serif; font-size: 2rem; font-weight: 300; color: #f4f1ea; margin-bottom: 0.3rem;
    }
    .dl-header p { color: #8a8580; font-size: 0.85rem; }
    .dl-back {
        padding: 0.5rem 1.2rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 600;
        background: transparent; border: 1px solid rgba(200,168,98,0.2); color: #f4f1ea; text-decoration: none;
        transition: all 0.2s ease;
    }
    .dl-back:hover { border-color: #c8a862; color: #c8a862; }

    .dl-list { display: grid; gap: 0.75rem; }
    .dl-item {
        display: flex; align-items: center; gap: 1rem; padding: 1.1rem 1.25rem;
        background: rgba(255,255,255,0.03); border: 1px solid rgba(200,168,98,0.08);
        border-radius: 0.85rem; transition: all 0.2s ease;
    }
    .dl-item:hover { background: rgba(255,255,255,0.05); border-color: rgba(200,168,98,0.15); transform: translateY(-1px); }
    .dl-icon {
        width: 46px; height: 46px; border-radius: 50%; flex-shrink: 0;
        background: rgba(200,168,98,0.08); border: 1px solid rgba(200,168,98,0.12);
        display: flex; align-items: center; justify-content: center; color: #c8a862; font-size: 1rem;
    }
    .dl-body { flex: 1; }
    .dl-title { font-size: 0.9rem; color: #f4f1ea; font-weight: 500; margin-bottom: 0.2rem; }
    .dl-meta { font-size: 0.72rem; color: #8a8580; }
    .dl-time { font-size: 0.7rem; color: #6b6660; margin-top: 0.3rem; }
    .dl-action {
        padding: 0.45rem 1rem; border-radius: 9999px; font-size: 0.72rem; font-weight: 600;
        background: linear-gradient(135deg, #c8a862, #a8893a); color: #0a0908; text-decoration: none;
        transition: all 0.2s ease; white-space: nowrap;
    }
    .dl-action:hover { transform: translateY(-1px); box-shadow: 0 6px 20px rgba(200,168,98,0.2); }

    .empty-state {
        text-align: center; padding: 4rem 1.5rem;
        background: rgba(255,255,255,0.03); border: 1px solid rgba(200,168,98,0.08);
        border-radius: 1rem;
    }
    .empty-state i { font-size: 3rem; color: #c8a862; margin-bottom: 1rem; opacity: 0.6; }
    .empty-state h3 { font-family: 'Cormorant Garamond', Georgia, serif; font-size: 1.5rem; color: #f4f1ea; margin-bottom: 0.5rem; font-weight: 300; }
    .empty-state p { color: #8a8580; font-size: 0.9rem; }
</style>

<div class="dl-wrap">
    <div class="dl-header">
        <div>
            <h1>تاریخچه دانلودها</h1>
            <p>لیست دانلودهای گالری‌های شما</p>
        </div>
        <a href="<?php echo url('dashboard'); ?>" class="dl-back"><i class="fas fa-arrow-left"></i> بازگشت</a>
    </div>

    <?php if (!empty($downloads)): ?>
        <div class="dl-list">
            <?php foreach ($downloads as $dl): ?>
                <div class="dl-item">
                    <div class="dl-icon"><i class="fas fa-file-archive"></i></div>
                    <div class="dl-body">
                        <div class="dl-title"><?php echo h($dl['gallery_title'] ?? 'گالری بدون عنوان'); ?></div>
                        <div class="dl-meta">ZIP Archive</div>
                        <div class="dl-time"><i class="far fa-clock" style="margin-left:0.3rem;"></i><?php echo date('Y/m/d H:i', strtotime($dl['created_at'])); ?></div>
                    </div>
                    <a href="<?php echo url('my-gallery/download?id=' . $dl['gallery_id']); ?>" class="dl-action"><i class="fas fa-download" style="margin-left:0.3rem;"></i> دانلود مجدد</a>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="empty-state">
            <i class="fas fa-history"></i>
            <h3>تاریخچه دانلود خالی است</h3>
            <p>پس از اولین دانلود گالری، اینجا ثبت می‌شود.</p>
        </div>
    <?php endif; ?>
</div>
