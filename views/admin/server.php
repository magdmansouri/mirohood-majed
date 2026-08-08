<?php
// views/admin/server.php - Server health status
$stats = $stats ?? [];

function formatBytes($bytes, $precision = 2) {
    if ($bytes <= 0) return '0 B';
    $units = ['B', 'KB', 'MB', 'GB', 'TB'];
    $base = floor(log($bytes, 1024));
    return round($bytes / pow(1024, $base), $precision) . ' ' . $units[$base];
}

function percentColor($percent) {
    if ($percent < 50) return '#4ade80';
    if ($percent < 75) return '#fbbf24';
    return '#f87171';
}

$memorySource = $stats['memory_source'] ?? 'php-limit';
?>
<style>
    .server-wrap { max-width: 1100px; margin: 0 auto; padding: 2rem 1.5rem; }
    .server-title {
        font-family: 'Cormorant Garamond', Georgia, serif;
        font-size: 2.2rem; font-weight: 300; color: #f4f1ea; margin-bottom: 0.5rem;
    }
    .server-sub { color: #8a8580; font-size: 0.85rem; margin-bottom: 2rem; }
    .server-note {
        color: #8a8580; font-size: 0.75rem; margin-bottom: 1.5rem;
        padding: 0.75rem 1rem; background: rgba(255,255,255,0.03);
        border: 1px solid rgba(200,168,98,0.08); border-radius: 0.6rem;
    }
    .server-grid {
        display: grid; grid-template-columns: repeat(3, 1fr); gap: 1.25rem; margin-bottom: 2rem;
    }
    @media (max-width: 900px) { .server-grid { grid-template-columns: repeat(2, 1fr); } }
    @media (max-width: 640px) { .server-grid { grid-template-columns: 1fr; } }
    .server-card {
        background: rgba(255,255,255,0.03); border: 1px solid rgba(200,168,98,0.08);
        border-radius: 1rem; padding: 1.5rem;
    }
    .server-card-label {
        font-size: 0.75rem; color: #8a8580; margin-bottom: 0.5rem;
        display: flex; align-items: center; gap: 0.4rem;
    }
    .server-card-label i { color: #c8a862; }
    .server-card-value {
        font-size: 1.5rem; color: #f4f1ea; font-weight: 600; margin-bottom: 0.5rem;
    }
    .server-card-sub { font-size: 0.75rem; color: #8a8580; }
    .progress-bar {
        width: 100%; height: 6px; background: rgba(255,255,255,0.05);
        border-radius: 999px; margin-top: 0.75rem; overflow: hidden;
    }
    .progress-fill {
        height: 100%; border-radius: 999px; transition: width 0.6s ease;
    }
    .server-recs {
        background: rgba(255,255,255,0.03); border: 1px solid rgba(200,168,98,0.08);
        border-radius: 1rem; padding: 1.5rem; margin-bottom: 2rem;
    }
    .server-recs h3 {
        font-family: 'Cormorant Garamond', Georgia, serif;
        font-size: 1.3rem; font-weight: 300; color: #f4f1ea; margin-bottom: 1.25rem;
    }
    .rec-item {
        display: flex; gap: 0.75rem; align-items: flex-start;
        padding: 0.9rem 1rem; border-radius: 0.75rem; margin-bottom: 0.75rem;
        border: 1px solid transparent;
    }
    .rec-item.success { background: rgba(74,222,128,0.06); border-color: rgba(74,222,128,0.15); color: #4ade80; }
    .rec-item.warning { background: rgba(251,191,36,0.06); border-color: rgba(251,191,36,0.15); color: #fbbf24; }
    .rec-item.danger { background: rgba(239,68,68,0.06); border-color: rgba(239,68,68,0.15); color: #f87171; }
    .rec-item i { margin-top: 0.15rem; font-size: 0.9rem; }
    .rec-item p { font-size: 0.85rem; line-height: 1.6; }
    .server-info-list {
        display: grid; grid-template-columns: repeat(2, 1fr); gap: 1rem; margin-bottom: 2rem;
    }
    @media (max-width: 640px) { .server-info-list { grid-template-columns: 1fr; } }
    .info-row {
        display: flex; justify-content: space-between; align-items: center;
        padding: 0.75rem 1rem; background: rgba(255,255,255,0.03);
        border: 1px solid rgba(200,168,98,0.08); border-radius: 0.6rem;
    }
    .info-row span:first-child { color: #8a8580; font-size: 0.8rem; }
    .info-row span:last-child { color: #f4f1ea; font-size: 0.85rem; }
    .ram-detail {
        display: grid; grid-template-columns: repeat(3, 1fr); gap: 0.75rem;
        margin-top: 0.75rem;
    }
    @media (max-width: 640px) { .ram-detail { grid-template-columns: 1fr; } }
    .ram-detail-item {
        text-align: center; padding: 0.5rem; background: rgba(0,0,0,0.15);
        border-radius: 0.5rem;
    }
    .ram-detail-item div:first-child { color: #8a8580; font-size: 0.65rem; margin-bottom: 0.2rem; }
    .ram-detail-item div:last-child { color: #f4f1ea; font-size: 0.85rem; font-weight: 600; }
</style>

<div class="server-wrap">
    <h1 class="server-title">وضعیت سرور و هاست</h1>
    <p class="server-sub">نمایش کلی منابع مصرفی و پیشنهادات برای ارتقا یا نگهداری</p>

    <?php if ($memorySource === 'php-limit'): ?>
        <div class="server-note">
            <i class="fas fa-info-circle" style="color:#c8a862;margin-left:0.4rem;"></i>
            امکان خواندن RAM واقعی سرور وجود ندارد. مقادیر حافظه بر اساس محدوده PHP نمایش داده می‌شوند. برای دریافت اطلاعات دقیق‌تر، هاستینگ باید دسترسی به <code>/proc/meminfo</code> یا دستور <code>free</code> را فعال کند. همچنین می‌توانید مقدار دقیق RAM و دیسک را در <code>config/config.php</code> تنظیم کنید (SERVER_TOTAL_RAM و SERVER_TOTAL_DISK).
        </div>
    <?php elseif (strpos($memorySource, 'configured') === 0): ?>
        <div class="server-note">
            <i class="fas fa-check-circle" style="color:#4ade80;margin-left:0.4rem;"></i>
            مقدار کل RAM و دیسک از تنظیمات <code>config/config.php</code> خوانده شده است. مصرف لحظه‌ای از تشخیص خودکار سیستم محاسبه می‌شود.
        </div>
    <?php endif; ?>

    <div class="server-grid">
        <div class="server-card">
            <div class="server-card-label"><i class="fas fa-hdd"></i> فضای دیسک</div>
            <div class="server-card-value"><?php echo formatBytes($stats['disk_used']); ?> / <?php echo formatBytes($stats['disk_total']); ?></div>
            <div class="server-card-sub"><?php echo $stats['disk_percent']; ?>٪ مصرف شده</div>
            <div class="progress-bar">
                <div class="progress-fill" style="width:<?php echo $stats['disk_percent']; ?>%;background:<?php echo percentColor($stats['disk_percent']); ?>"></div>
            </div>
        </div>

        <div class="server-card">
            <div class="server-card-label"><i class="fas fa-memory"></i> RAM سرور</div>
            <div class="server-card-value"><?php echo formatBytes($stats['memory_used']); ?> / <?php echo formatBytes($stats['memory_total']); ?></div>
            <div class="server-card-sub"><?php echo $stats['memory_percent']; ?>٪ مصرف شده</div>
            <div class="progress-bar">
                <div class="progress-fill" style="width:<?php echo $stats['memory_percent']; ?>%;background:<?php echo percentColor($stats['memory_percent']); ?>"></div>
            </div>
            <div class="ram-detail">
                <div class="ram-detail-item">
                    <div>کل</div>
                    <div><?php echo formatBytes($stats['memory_total']); ?></div>
                </div>
                <div class="ram-detail-item">
                    <div>آزاد</div>
                    <div><?php echo formatBytes($stats['memory_free']); ?></div>
                </div>
                <div class="ram-detail-item">
                    <div>مصرف‌شده</div>
                    <div><?php echo formatBytes($stats['memory_used']); ?></div>
                </div>
            </div>
        </div>

        <div class="server-card">
            <div class="server-card-label"><i class="fas fa-microchip"></i> CPU</div>
            <div class="server-card-value">
                <?php echo isset($stats['cpu_usage']) ? $stats['cpu_usage'] . '٪' : 'نامشخص'; ?>
            </div>
            <div class="server-card-sub">
                Load: <?php echo is_array($stats['cpu_load']) ? implode(' / ', array_map(function($v) { return round($v, 2); }, $stats['cpu_load'])) : 'نامشخص'; ?>
            </div>
        </div>

        <div class="server-card">
            <div class="server-card-label"><i class="fas fa-database"></i> حجم دیتابیس</div>
            <div class="server-card-value"><?php echo number_format($stats['db_size'], 2); ?> MB</div>
            <div class="server-card-sub">حجم جداول MySQL</div>
        </div>

        <div class="server-card">
            <div class="server-card-label"><i class="fas fa-cloud-upload-alt"></i> حجم آپلودها</div>
            <div class="server-card-value"><?php echo formatBytes($stats['uploads_size']); ?></div>
            <div class="server-card-sub">پوشه uploads</div>
        </div>

        <div class="server-card">
            <div class="server-card-label"><i class="fas fa-code"></i> نسخه PHP</div>
            <div class="server-card-value"><?php echo h($stats['php_version']); ?></div>
            <div class="server-card-sub">MySQL: <?php echo h($stats['mysql_version']); ?></div>
        </div>
    </div>

    <div class="server-info-list">
        <div class="info-row">
            <span>memory_limit PHP</span>
            <span><?php echo h($stats['memory_limit'] ?? 'نامشخص'); ?></span>
        </div>
        <div class="info-row">
            <span>ZipArchive</span>
            <span><?php echo class_exists('ZipArchive') ? '✅ فعال' : '❌ غیرفعال'; ?></span>
        </div>
        <div class="info-row">
            <span>حجم کش</span>
            <span><?php echo formatBytes($stats['cache_size']); ?></span>
        </div>
        <div class="info-row">
            <span>فضای خالی دیسک</span>
            <span><?php echo formatBytes($stats['disk_free']); ?></span>
        </div>
    </div>

    <div class="server-recs">
        <h3>پیشنهادات و وضعیت</h3>
        <?php foreach ($stats['recommendations'] as $rec): ?>
            <div class="rec-item <?php echo $rec['type']; ?>">
                <i class="fas <?php echo $rec['type'] === 'success' ? 'fa-check-circle' : ($rec['type'] === 'warning' ? 'fa-exclamation-triangle' : 'fa-times-circle'); ?>"></i>
                <p><?php echo h($rec['message']); ?></p>
            </div>
        <?php endforeach; ?>
    </div>
</div>
