<?php
// views/admin/index.php - Advanced admin dashboard with statistics
$stats = $stats ?? [];
$recent_bookings = $recent_bookings ?? [];
$users = $users ?? [];
$bookings_by_month = $bookings_by_month ?? [];
$bookings_by_status = $bookings_by_status ?? [];
$bookings_by_package = $bookings_by_package ?? [];
$users_by_month = $users_by_month ?? [];
$upcoming_bookings = $upcoming_bookings ?? [];
$gallery_stats = $gallery_stats ?? ['total_galleries' => 0, 'total_views' => 0, 'total_downloads' => 0];
$top_galleries = $top_galleries ?? [];
$recent_activity = $recent_activity ?? [];
$conversion_rate = $conversion_rate ?? 0;

$statusColors = [
    'PENDING' => '#fbbf24',
    'CONFIRMED' => '#4ade80',
    'CANCELLED' => '#f87171',
    'COMPLETED' => '#60a5fa'
];
$statusLabels = [
    'PENDING' => 'در انتظار',
    'CONFIRMED' => 'تایید شده',
    'CANCELLED' => 'لغو شده',
    'COMPLETED' => 'انجام شده'
];

$packageLabels = [
    'portrait' => 'پرتره',
    'brand' => 'برند',
    'editorial' => 'فیلم و عکس'
];

function monthLabel($month) {
    $parts = explode('-', $month);
    if (count($parts) !== 2) return $month;
    $m = (int) $parts[1];
    $labels = ['', 'فروردین', 'اردیبهشت', 'خرداد', 'تیر', 'مرداد', 'شهریور', 'مهر', 'آبان', 'آذر', 'دی', 'بهمن', 'اسفند'];
    return $labels[$m] ?? $month;
}

function maxValue($items, $key) {
    $max = 0;
    foreach ($items as $item) { if ((int) ($item[$key] ?? 0) > $max) $max = (int) $item[$key]; }
    return $max ?: 1;
}
?>
<style>
    .admin-dash-header { display:flex; justify-content:space-between; align-items:flex-start; flex-wrap:wrap; gap:1rem; margin-bottom:2rem; }
    .admin-dash-header h1 { font-family:'Cormorant Garamond',Georgia,serif; font-size:2.2rem; font-weight:300; color:#f4f1ea; margin-bottom:0.3rem; }
    .admin-dash-header p { color:#8a8580; font-size:0.85rem; margin:0; }
    .admin-time-badge { display:inline-flex; align-items:center; gap:0.4rem; font-size:0.7rem; color:#8a8580; background:rgba(255,255,255,0.03); padding:0.35rem 1rem; border-radius:9999px; border:1px solid rgba(200,168,98,0.06); }

    .admin-stat-grid { display:grid; grid-template-columns:repeat(auto-fit, minmax(200px, 1fr)); gap:1rem; margin-bottom:1.5rem; }
    .admin-stat-card { background:rgba(255,255,255,0.03); border:1px solid rgba(200,168,98,0.08); border-radius:1rem; padding:1.25rem; display:flex; align-items:center; gap:1rem; transition:all 0.3s ease; }
    .admin-stat-card:hover { background:rgba(255,255,255,0.05); border-color:rgba(200,168,98,0.15); transform:translateY(-2px); }
    .admin-stat-card .icon-bg { width:48px; height:48px; border-radius:0.9rem; background:rgba(200,168,98,0.08); border:1px solid rgba(200,168,98,0.12); display:flex; align-items:center; justify-content:center; color:#c8a862; font-size:1.2rem; flex-shrink:0; }
    .admin-stat-card .info { flex:1; }
    .admin-stat-card .num { font-size:1.6rem; font-weight:600; color:#f4f1ea; margin-bottom:0.1rem; }
    .admin-stat-card .label { font-size:0.75rem; color:#8a8580; }

    .admin-charts-grid { display:grid; grid-template-columns:repeat(2, 1fr); gap:1.5rem; margin-bottom:1.5rem; }
    @media (max-width: 900px) { .admin-charts-grid { grid-template-columns:1fr; } }
    .admin-chart-card { background:rgba(255,255,255,0.03); border:1px solid rgba(200,168,98,0.08); border-radius:1rem; padding:1.5rem; }
    .admin-chart-card h3 { font-family:'Cormorant Garamond',Georgia,serif; font-size:1.2rem; font-weight:300; color:#f4f1ea; margin-bottom:1.25rem; display:flex; align-items:center; gap:0.5rem; }
    .admin-chart-card h3 i { color:#c8a862; font-size:1rem; }

    .bar-chart { display:flex; align-items:flex-end; gap:0.6rem; height:160px; padding-bottom:1.5rem; border-bottom:1px solid rgba(200,168,98,0.1); position:relative; }
    .bar-group { flex:1; display:flex; flex-direction:column; align-items:center; gap:0.4rem; }
    .bar { width:100%; max-width:36px; background:linear-gradient(180deg, #c8a862, #a8893a); border-radius:0.4rem 0.4rem 0 0; transition:all 0.5s ease; position:relative; min-height:4px; }
    .bar:hover { filter:brightness(1.1); }
    .bar-label { font-size:0.65rem; color:#8a8580; text-align:center; }
    .bar-value { position:absolute; top:-1.2rem; left:50%; transform:translateX(-50%); font-size:0.65rem; color:#f4f1ea; opacity:0; transition:opacity 0.3s ease; }
    .bar:hover .bar-value { opacity:1; }

    .donut-chart { display:flex; align-items:center; gap:1.5rem; flex-wrap:wrap; }
    .donut { width:140px; height:140px; border-radius:50%; position:relative; flex-shrink:0; }
    .donut::after { content:''; position:absolute; inset:28px; border-radius:50%; background:#151210; }
    .donut-center { position:absolute; inset:0; display:flex; align-items:center; justify-content:center; z-index:1; }
    .donut-center span { font-size:1.2rem; font-weight:600; color:#f4f1ea; }
    .donut-legend { display:flex; flex-direction:column; gap:0.5rem; }
    .legend-item { display:flex; align-items:center; gap:0.5rem; font-size:0.75rem; color:#8a8580; }
    .legend-dot { width:10px; height:10px; border-radius:50%; }

    .list-card { background:rgba(255,255,255,0.03); border:1px solid rgba(200,168,98,0.08); border-radius:1rem; padding:1.5rem; margin-bottom:1.5rem; }
    .list-card h3 { font-family:'Cormorant Garamond',Georgia,serif; font-size:1.2rem; font-weight:300; color:#f4f1ea; margin-bottom:1.25rem; display:flex; align-items:center; gap:0.5rem; }
    .list-card h3 i { color:#c8a862; font-size:1rem; }
    .list-item { display:flex; align-items:center; justify-content:space-between; gap:1rem; padding:0.85rem 0; border-bottom:1px solid rgba(200,168,98,0.06); }
    .list-item:last-child { border-bottom:none; }
    .list-item-main { display:flex; align-items:center; gap:0.75rem; }
    .list-item-icon { width:34px; height:34px; border-radius:50%; background:rgba(200,168,98,0.08); border:1px solid rgba(200,168,98,0.12); display:flex; align-items:center; justify-content:center; color:#c8a862; font-size:0.75rem; flex-shrink:0; }
    .list-item-title { font-size:0.85rem; color:#f4f1ea; margin-bottom:0.15rem; }
    .list-item-meta { font-size:0.7rem; color:#8a8580; }
    .list-item-badge { padding:0.25rem 0.7rem; border-radius:9999px; font-size:0.65rem; font-weight:600; border:1px solid; }

    .quick-links { display:grid; grid-template-columns:repeat(auto-fit, minmax(130px, 1fr)); gap:0.75rem; margin-bottom:2rem; }
    .quick-link-card { display:flex; flex-direction:column; align-items:center; justify-content:center; gap:0.5rem; padding:1rem; background:rgba(255,255,255,0.03); border:1px solid rgba(200,168,98,0.08); border-radius:0.8rem; color:#f4f1ea; text-decoration:none; transition:all 0.3s ease; }
    .quick-link-card:hover { background:rgba(200,168,98,0.08); border-color:rgba(200,168,98,0.15); transform:translateY(-2px); }
    .quick-link-card i { font-size:1.3rem; color:#c8a862; }
    .quick-link-card p { font-size:0.75rem; margin:0; }

    .activity-icon { color:#c8a862; }
    .activity-icon.booking { color:#4ade80; }
    .activity-icon.gallery { color:#60a5fa; }
    .activity-icon.user { color:#fbbf24; }

    .empty-chart { text-align:center; padding:2rem 0; color:#8a8580; font-size:0.85rem; }
    .empty-chart i { font-size:2rem; color:#c8a862; margin-bottom:0.75rem; opacity:0.6; }
</style>

<div class="admin-dash-header">
    <div>
        <h1>داشبورد</h1>
        <p>نمای کلی و آمار پیشرفته استودیو</p>
    </div>
    <span class="admin-time-badge"><i class="fas fa-sync-alt"></i> <?php echo date('Y/m/d H:i'); ?></span>
</div>

<div class="quick-links">
    <a href="<?php echo url('admin/bookings'); ?>" class="quick-link-card"><i class="fas fa-calendar-check"></i><p>رزروها</p></a>
    <a href="<?php echo url('admin/gallery'); ?>" class="quick-link-card"><i class="fas fa-images"></i><p>گالری</p></a>
    <a href="<?php echo url('admin/client-galleries'); ?>" class="quick-link-card"><i class="fas fa-user-lock"></i><p>گالری مشتریان</p></a>
    <a href="<?php echo url('admin/content'); ?>" class="quick-link-card"><i class="fas fa-pen-to-square"></i><p>محتوا</p></a>
    <a href="<?php echo url('admin/media'); ?>" class="quick-link-card"><i class="fas fa-film"></i><p>رسانه</p></a>
    <a href="<?php echo url('admin/hours'); ?>" class="quick-link-card"><i class="fas fa-clock"></i><p>ساعت کاری</p></a>
</div>

<div class="admin-stat-grid">
    <div class="admin-stat-card">
        <div class="icon-bg"><i class="fas fa-calendar-check"></i></div>
        <div class="info"><p class="num" style="color:#c8a862;"><?php echo $stats['total_bookings'] ?? 0; ?></p><p class="label">کل رزروها</p></div>
    </div>
    <div class="admin-stat-card">
        <div class="icon-bg"><i class="fas fa-hourglass-half"></i></div>
        <div class="info"><p class="num" style="color:#fbbf24;"><?php echo $stats['pending_bookings'] ?? 0; ?></p><p class="label">در انتظار</p></div>
    </div>
    <div class="admin-stat-card">
        <div class="icon-bg"><i class="fas fa-check-circle"></i></div>
        <div class="info"><p class="num" style="color:#4ade80;"><?php echo $stats['confirmed_bookings'] ?? 0; ?></p><p class="label">تایید شده</p></div>
    </div>
    <div class="admin-stat-card">
        <div class="icon-bg"><i class="fas fa-flag-checkered"></i></div>
        <div class="info"><p class="num" style="color:#60a5fa;"><?php echo $stats['completed_bookings'] ?? 0; ?></p><p class="label">انجام شده</p></div>
    </div>
    <div class="admin-stat-card">
        <div class="icon-bg"><i class="fas fa-user-check"></i></div>
        <div class="info"><p class="num" style="color:#4ade80;"><?php echo $stats['total_users'] ?? 0; ?></p><p class="label">کاربران</p></div>
    </div>
    <div class="admin-stat-card">
        <div class="icon-bg"><i class="fas fa-percentage"></i></div>
        <div class="info"><p class="num" style="color:#c8a862;"><?php echo $conversion_rate; ?>%</p><p class="label">نرخ تبدیل</p></div>
    </div>
</div>

<div class="admin-charts-grid">
    <div class="admin-chart-card">
        <h3><i class="fas fa-chart-bar"></i> رزروهای ماهانه</h3>
        <?php if (!empty($bookings_by_month)): ?>
            <?php $max = maxValue($bookings_by_month, 'total'); ?>
            <div class="bar-chart">
                <?php foreach ($bookings_by_month as $m): ?>
                    <?php $height = min(100, ((int) $m['total'] / $max) * 100); ?>
                    <div class="bar-group">
                        <div class="bar" style="height:<?php echo $height; ?>%;"><span class="bar-value"><?php echo $m['total']; ?></span></div>
                        <span class="bar-label"><?php echo monthLabel($m['month']); ?></span>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="empty-chart"><i class="fas fa-chart-bar"></i><p>داده‌ای موجود نیست</p></div>
        <?php endif; ?>
    </div>

    <div class="admin-chart-card">
        <h3><i class="fas fa-chart-pie"></i> وضعیت رزروها</h3>
        <?php if (!empty($bookings_by_status)): ?>
            <?php
            $total = array_sum(array_column($bookings_by_status, 'total'));
            $gradient = [];
            $current = 0;
            foreach ($bookings_by_status as $s) {
                $pct = $total ? ((int) $s['total'] / $total) * 100 : 0;
                $color = $statusColors[$s['status']] ?? '#8a8580';
                $gradient[] = $color . ' ' . $current . '% ' . ($current + $pct) . '%';
                $current += $pct;
            }
            ?>
            <div class="donut-chart">
                <div class="donut" style="background:conic-gradient(<?php echo implode(', ', $gradient); ?>);">
                    <div class="donut-center"><span><?php echo $total; ?></span></div>
                </div>
                <div class="donut-legend">
                    <?php foreach ($bookings_by_status as $s): ?>
                        <div class="legend-item">
                            <span class="legend-dot" style="background:<?php echo $statusColors[$s['status']] ?? '#8a8580'; ?>;"></span>
                            <span><?php echo $statusLabels[$s['status']] ?? $s['status']; ?>: <?php echo $s['total']; ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php else: ?>
            <div class="empty-chart"><i class="fas fa-chart-pie"></i><p>داده‌ای موجود نیست</p></div>
        <?php endif; ?>
    </div>
</div>

<div class="admin-charts-grid">
    <div class="admin-chart-card">
        <h3><i class="fas fa-layer-group"></i> محبوب‌ترین پکیج‌ها</h3>
        <?php if (!empty($bookings_by_package)): ?>
            <?php $maxPkg = maxValue($bookings_by_package, 'total'); ?>
            <div class="bar-chart">
                <?php foreach ($bookings_by_package as $p): ?>
                    <?php $height = min(100, ((int) $p['total'] / $maxPkg) * 100); ?>
                    <div class="bar-group">
                        <div class="bar" style="height:<?php echo $height; ?>%;"><span class="bar-value"><?php echo $p['total']; ?></span></div>
                        <span class="bar-label"><?php echo $packageLabels[$p['package_id']] ?? $p['package_id']; ?></span>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="empty-chart"><i class="fas fa-layer-group"></i><p>داده‌ای موجود نیست</p></div>
        <?php endif; ?>
    </div>

    <div class="admin-chart-card">
        <h3><i class="fas fa-user-plus"></i> ثبت‌نام کاربران ماهانه</h3>
        <?php if (!empty($users_by_month)): ?>
            <?php $maxUser = maxValue($users_by_month, 'total'); ?>
            <div class="bar-chart">
                <?php foreach ($users_by_month as $m): ?>
                    <?php $height = min(100, ((int) $m['total'] / $maxUser) * 100); ?>
                    <div class="bar-group">
                        <div class="bar" style="height:<?php echo $height; ?>%;"><span class="bar-value"><?php echo $m['total']; ?></span></div>
                        <span class="bar-label"><?php echo monthLabel($m['month']); ?></span>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="empty-chart"><i class="fas fa-user-plus"></i><p>داده‌ای موجود نیست</p></div>
        <?php endif; ?>
    </div>
</div>

<div class="admin-charts-grid">
    <div class="list-card">
        <h3><i class="fas fa-calendar-day"></i> نوبت‌های پیش‌رو</h3>
        <?php if (!empty($upcoming_bookings)): ?>
            <?php foreach ($upcoming_bookings as $b): ?>
                <div class="list-item">
                    <div class="list-item-main">
                        <div class="list-item-icon"><i class="fas fa-clock"></i></div>
                        <div>
                            <div class="list-item-title"><?php echo h($b['full_name']); ?></div>
                            <div class="list-item-meta"><?php echo h($b['jalali_date']); ?> — <?php echo h($b['time']); ?> — <?php echo $packageLabels[$b['package_id']] ?? $b['package_id']; ?></div>
                        </div>
                    </div>
                    <span class="list-item-badge" style="color:<?php echo $b['status'] === 'CONFIRMED' ? '#4ade80' : '#fbbf24'; ?>;border-color:<?php echo $b['status'] === 'CONFIRMED' ? 'rgba(74,222,128,0.2)' : 'rgba(251,191,36,0.2)'; ?>;background:<?php echo $b['status'] === 'CONFIRMED' ? 'rgba(74,222,128,0.08)' : 'rgba(251,191,36,0.08)'; ?>"><?php echo $b['status'] === 'CONFIRMED' ? 'تایید شده' : 'در انتظار'; ?></span>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="empty-chart"><i class="fas fa-calendar-day"></i><p>نوبت پیش‌رویی وجود ندارد</p></div>
        <?php endif; ?>
    </div>

    <div class="list-card">
        <h3><i class="fas fa-bolt"></i> فعالیت‌های اخیر</h3>
        <?php if (!empty($recent_activity)): ?>
            <?php foreach ($recent_activity as $a): ?>
                <div class="list-item">
                    <div class="list-item-main">
                        <div class="list-item-icon activity-icon <?php echo $a['type']; ?>">
                            <i class="fas <?php echo $a['type'] === 'booking' ? 'fa-calendar-check' : ($a['type'] === 'gallery' ? 'fa-images' : 'fa-user'); ?>"></i>
                        </div>
                        <div>
                            <div class="list-item-title"><?php echo h($a['title']); ?></div>
                            <div class="list-item-meta"><?php echo timeAgo($a['created_at']); ?></div>
                        </div>
                    </div>
                    <?php if (!empty($a['status'])): ?>
                        <span class="list-item-badge" style="color:<?php echo $statusColors[$a['status']] ?? '#8a8580'; ?>;border-color:<?php echo !empty($statusColors[$a['status']]) ? $statusColors[$a['status']] . '33' : 'rgba(138,133,128,0.2)'; ?>;background:<?php echo !empty($statusColors[$a['status']]) ? $statusColors[$a['status']] . '14' : 'rgba(138,133,128,0.08)'; ?>"><?php echo $statusLabels[$a['status']] ?? $a['status']; ?></span>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="empty-chart"><i class="fas fa-bolt"></i><p>فعالیتی ثبت نشده</p></div>
        <?php endif; ?>
    </div>
</div>

<div class="admin-charts-grid">
    <div class="list-card">
        <h3><i class="fas fa-images"></i> آمار گالری‌های مشتری</h3>
        <div class="admin-stat-grid" style="margin-bottom:0;">
            <div class="admin-stat-card">
                <div class="icon-bg"><i class="fas fa-folder-open"></i></div>
                <div class="info"><p class="num" style="color:#c8a862;"><?php echo $gallery_stats['total_galleries'] ?? 0; ?></p><p class="label">گالری‌ها</p></div>
            </div>
            <div class="admin-stat-card">
                <div class="icon-bg"><i class="fas fa-eye"></i></div>
                <div class="info"><p class="num" style="color:#60a5fa;"><?php echo $gallery_stats['total_views'] ?? 0; ?></p><p class="label">بازدیدها</p></div>
            </div>
            <div class="admin-stat-card">
                <div class="icon-bg"><i class="fas fa-download"></i></div>
                <div class="info"><p class="num" style="color:#4ade80;"><?php echo $gallery_stats['total_downloads'] ?? 0; ?></p><p class="label">دانلودها</p></div>
            </div>
        </div>
    </div>

    <div class="list-card">
        <h3><i class="fas fa-star"></i> محبوب‌ترین گالری‌ها</h3>
        <?php if (!empty($top_galleries)): ?>
            <?php foreach ($top_galleries as $g): ?>
                <div class="list-item">
                    <div class="list-item-main">
                        <div class="list-item-icon"><i class="fas fa-images"></i></div>
                        <div>
                            <div class="list-item-title"><?php echo h($g['title']); ?></div>
                            <div class="list-item-meta"><?php echo h($g['user_name']); ?> — <?php echo (int) ($g['view_count'] ?? 0); ?> بازدید / <?php echo (int) ($g['download_count'] ?? 0); ?> دانلود</div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="empty-chart"><i class="fas fa-star"></i><p>گالری فعالی وجود ندارد</p></div>
        <?php endif; ?>
    </div>
</div>

<div class="list-card">
    <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:1rem; margin-bottom:1rem;">
        <h3 style="margin-bottom:0;"><i class="fas fa-users"></i> کاربران ثبت‌نام‌شده</h3>
        <span style="font-size:0.8rem;color:#8a8580;">تعداد: <?php echo count($users); ?></span>
    </div>
    <?php if (!empty($users)): ?>
        <div class="admin-table-wrap">
            <table class="admin-table">
                <thead>
                    <tr><th>نام</th><th>موبایل</th><th>ایمیل</th><th>تعداد رزرو</th><th>تاریخ ثبت‌نام</th></tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $u): ?>
                        <tr>
                            <td><?php echo h($u['name']); ?></td>
                            <td style="color:#8a8580;font-size:0.8rem;direction:ltr;text-align:right;"><?php echo h($u['phone']); ?></td>
                            <td style="color:#8a8580;font-size:0.8rem;"><?php echo h($u['email'] ?: '-'); ?></td>
                            <td style="color:#8a8580;font-size:0.8rem;"><?php echo (int)($u['booking_count'] ?? 0); ?></td>
                            <td style="color:#8a8580;font-size:0.8rem;"><?php echo h(date('Y/m/d', strtotime($u['created_at']))); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <p style="color:#8a8580;text-align:center;padding:2rem 0;">هیچ کاربر ثبت‌نام‌شده‌ای وجود ندارد.</p>
    <?php endif; ?>
</div>
