<?php
// views/auth/dashboard.php - Premium user dashboard (Phase 1 + 2)
$user = $user ?? [];
$bookings = $bookings ?? [];
$all_bookings = $all_bookings ?? $bookings;
$upcoming_bookings = $upcoming_bookings ?? [];
$gallery_stats = $gallery_stats ?? ['total_galleries' => 0, 'total_views' => 0, 'total_downloads' => 0];
$recent_galleries = $recent_galleries ?? [];
$unread_count = $unread_count ?? 0;
$recent_notifications = $recent_notifications ?? [];
$download_count = $download_count ?? 0;

$statusMap = [
    'PENDING' => ['label' => 'در انتظار', 'color' => '#fbbf24', 'bg' => 'rgba(251,191,36,0.08)', 'icon' => 'fa-clock'],
    'CONFIRMED' => ['label' => 'تایید شده', 'color' => '#4ade80', 'bg' => 'rgba(74,222,128,0.08)', 'icon' => 'fa-check-circle'],
    'CANCELLED' => ['label' => 'لغو شده', 'color' => '#f87171', 'bg' => 'rgba(239,68,68,0.08)', 'icon' => 'fa-times-circle'],
    'COMPLETED' => ['label' => 'انجام شده', 'color' => '#8a8580', 'bg' => 'rgba(138,133,128,0.08)', 'icon' => 'fa-flag-checkered']
];

$requestStatusMap = [
    'PENDING' => ['label' => 'درخواست ثبت‌شده', 'color' => '#fbbf24'],
    'APPROVED' => ['label' => 'درخواست تاییدشده', 'color' => '#4ade80'],
    'REJECTED' => ['label' => 'درخواست ردشده', 'color' => '#f87171'],
    'NONE' => ['label' => '', 'color' => '']
];

$total = count($all_bookings);
$confirmed = 0; $pending = 0; $cancelled = 0; $completed = 0;
foreach ($all_bookings as $b) {
    if ($b['status'] === 'CONFIRMED') $confirmed++;
    elseif ($b['status'] === 'PENDING') $pending++;
    elseif ($b['status'] === 'CANCELLED') $cancelled++;
    elseif ($b['status'] === 'COMPLETED') $completed++;
}

$packageLabels = [
    'portrait' => ['name' => 'جلسه پرتره حرفه‌ای', 'icon' => 'fa-camera-retro'],
    'brand' => ['name' => 'جلسه فیلم‌برداری برند', 'icon' => 'fa-film'],
    'editorial' => ['name' => 'جلسه فیلم و عکس', 'icon' => 'fa-video']
];

$typeIcons = [
    'BOOKING_STATUS' => 'fa-calendar-check',
    'GALLERY_SHARED' => 'fa-images',
    'SELECTION_CONFIRMED' => 'fa-check-double',
    'BOOKING_REQUEST' => 'fa-clipboard-check'
];
?>
<style>
    .dashboard-wrap { max-width: 1200px; margin: 0 auto; padding: 4rem 1.25rem 6rem; contain: layout style; }
    .dashboard-header {
        display: flex; align-items: center; gap: 1.25rem;
        margin-bottom: 2.5rem; padding-bottom: 2rem;
        border-bottom: 1px solid rgba(200,168,98,0.08);
    }
    .user-avatar {
        width: 74px; height: 74px; border-radius: 50%;
        background: linear-gradient(135deg, #c8a862, #a8893a);
        display: flex; align-items: center; justify-content: center;
        color: #0a0908; font-size: 1.8rem; font-family: 'Cormorant Garamond', serif;
        box-shadow: 0 8px 30px rgba(200,168,98,0.15);
        flex-shrink: 0; overflow: hidden; position: relative;
    }
    .user-avatar img { width: 100%; height: 100%; object-fit: cover; }
    .user-info h1 {
        font-family: 'Cormorant Garamond', Georgia, serif;
        font-size: 2rem; font-weight: 300; color: #f4f1ea; margin-bottom: 0.3rem;
    }
    .user-info p { color: #8a8580; font-size: 0.85rem; }
    .dashboard-actions { margin-right: auto; display: flex; gap: 0.75rem; flex-wrap: wrap; }
    .dashboard-actions a {
        display: inline-flex; align-items: center; gap: 0.5rem;
        padding: 0.7rem 1.4rem; border-radius: 9999px; font-size: 0.78rem; font-weight: 600;
        transition: all 0.25s cubic-bezier(0.34,1.56,0.64,1); text-decoration: none; border: 1px solid transparent;
    }
    .btn-gold {
        background: linear-gradient(135deg, #c8a862, #a8893a); color: #0a0908; border: none;
    }
    .btn-gold:hover { transform: translateY(-2px); box-shadow: 0 8px 25px rgba(200,168,98,0.25); }
    .btn-outline {
        background: transparent; color: #f4f1ea; border: 1px solid rgba(200,168,98,0.2);
    }
    .btn-outline:hover { border-color: #c8a862; color: #c8a862; }
    .btn-outline .badge {
        display: inline-flex; align-items: center; justify-content: center;
        min-width: 18px; height: 18px; padding: 0 5px; border-radius: 9px;
        background: #c8a862; color: #0a0908; font-size: 0.65rem; margin-right: -4px;
    }

    .stats-grid {
        display: grid; grid-template-columns: repeat(4, 1fr); gap: 1.25rem; margin-bottom: 2.5rem;
    }
    .stat-card {
        background: rgba(255,255,255,0.03); border: 1px solid rgba(200,168,98,0.08);
        border-radius: 1rem; padding: 1.5rem; position: relative; overflow: hidden;
        transition: all 0.3s ease; contain: layout style;
    }
    .stat-card::before {
        content: ''; position: absolute; top: 0; left: 0; right: 0; height: 2px;
        background: linear-gradient(90deg, transparent, var(--accent, #c8a862), transparent);
        opacity: 0.5;
    }
    .stat-card:hover { background: rgba(255,255,255,0.05); border-color: rgba(200,168,98,0.15); transform: translateY(-2px); }
    .stat-card .icon { font-size: 1.2rem; color: var(--accent, #c8a862); margin-bottom: 0.8rem; }
    .stat-card .value { font-size: 1.8rem; font-weight: 600; color: #f4f1ea; margin-bottom: 0.2rem; }
    .stat-card .label { font-size: 0.75rem; color: #8a8580; }

    .section-card {
        background: rgba(255,255,255,0.03); border: 1px solid rgba(200,168,98,0.08);
        border-radius: 1rem; padding: 1.5rem; margin-bottom: 1.5rem; contain: layout style;
    }
    .section-card-header {
        display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;
        margin-bottom: 1.25rem;
    }
    .section-title {
        font-family: 'Cormorant Garamond', Georgia, serif;
        font-size: 1.3rem; font-weight: 300; color: #f4f1ea;
        display: flex; align-items: center; gap: 0.6rem;
    }
    .section-title i { color: #c8a862; font-size: 1.1rem; }
    .section-link {
        font-size: 0.75rem; color: #8a8580; display: inline-flex; align-items: center; gap: 0.35rem;
        transition: all 0.25s ease;
    }
    .section-link:hover { color: #c8a862; gap: 0.5rem; }

    .two-col { display: grid; grid-template-columns: 1.2fr 0.8fr; gap: 1.5rem; }
    @media (max-width: 900px) { .two-col { grid-template-columns: 1fr; } }

    .bookings-grid { display: grid; gap: 0.75rem; }
    .booking-card {
        background: rgba(255,255,255,0.02); border: 1px solid rgba(200,168,98,0.06);
        border-radius: 0.85rem; padding: 1.1rem 1.25rem;
        display: flex; flex-wrap: wrap; justify-content: space-between; align-items: center; gap: 1rem;
        transition: all 0.25s ease;
    }
    .booking-card:hover { background: rgba(255,255,255,0.04); border-color: rgba(200,168,98,0.12); transform: translateY(-1px); }
    .booking-main { display: flex; align-items: center; gap: 1rem; flex: 1; min-width: 220px; }
    .booking-icon {
        width: 48px; height: 48px; border-radius: 0.85rem;
        background: rgba(200,168,98,0.08); border: 1px solid rgba(200,168,98,0.12);
        display: flex; align-items: center; justify-content: center;
        color: #c8a862; font-size: 1.2rem; flex-shrink: 0;
    }
    .booking-title { font-size: 0.95rem; color: #f4f1ea; font-weight: 500; margin-bottom: 0.2rem; }
    .booking-meta { font-size: 0.75rem; color: #8a8580; display: flex; flex-wrap: wrap; gap: 0.6rem; }
    .booking-meta span { display: inline-flex; align-items: center; gap: 0.3rem; }
    .booking-meta i { color: #c8a862; font-size: 0.7rem; }
    .booking-status {
        display: inline-flex; align-items: center; gap: 0.4rem;
        padding: 0.3rem 0.8rem; border-radius: 9999px; font-size: 0.72rem; font-weight: 600;
        border: 1px solid;
    }
    .booking-request-badge {
        display: inline-flex; align-items: center; gap: 0.3rem;
        font-size: 0.7rem; padding: 0.2rem 0.6rem; border-radius: 9999px; margin-right: 0.5rem;
        background: rgba(255,255,255,0.05); border: 1px solid;
    }
    .booking-note {
        width: 100%; margin-top: 0.5rem; padding-top: 0.5rem;
        border-top: 1px solid rgba(200,168,98,0.06); font-size: 0.75rem; color: #8a8580;
    }
    .booking-note i { color: #c8a862; margin-left: 0.4rem; }
    .booking-actions { display: flex; gap: 0.4rem; flex-wrap: wrap; }
    .booking-actions button {
        padding: 0.35rem 0.8rem; border-radius: 9999px; font-size: 0.7rem; font-weight: 500;
        background: transparent; border: 1px solid rgba(200,168,98,0.15); color: #8a8580;
        cursor: pointer; transition: all 0.2s ease;
    }
    .booking-actions button:hover { border-color: #c8a862; color: #c8a862; }
    .booking-actions button:disabled { opacity: 0.4; cursor: not-allowed; }

    .filter-pills { display: flex; flex-wrap: wrap; gap: 0.4rem; }
    .filter-pill {
        display: inline-block; padding: 0.3rem 0.85rem; border-radius: 9999px; font-size: 0.72rem; font-weight: 500;
        transition: all 0.2s ease; text-decoration: none; border: 1px solid rgba(200,168,98,0.15);
        background: transparent; color: #8a8580;
    }
    .filter-pill:hover { border-color: #c8a862; color: #c8a862; }
    .filter-pill.active { background: rgba(200,168,98,0.1); border-color: #c8a862; color: #c8a862; }

    .gallery-list { display: grid; gap: 0.75rem; }
    .gallery-item {
        display: flex; align-items: center; gap: 1rem; padding: 1rem;
        background: rgba(255,255,255,0.02); border: 1px solid rgba(200,168,98,0.06);
        border-radius: 0.85rem; transition: all 0.25s ease; text-decoration: none;
    }
    .gallery-item:hover { background: rgba(255,255,255,0.04); border-color: rgba(200,168,98,0.12); transform: translateY(-1px); }
    .gallery-thumb {
        width: 60px; height: 60px; border-radius: 0.6rem; object-fit: cover;
        background: rgba(200,168,98,0.08); flex-shrink: 0;
    }
    .gallery-info { flex: 1; min-width: 160px; }
    .gallery-title { font-size: 0.9rem; color: #f4f1ea; font-weight: 500; margin-bottom: 0.2rem; }
    .gallery-stats { font-size: 0.7rem; color: #8a8580; display: flex; gap: 0.75rem; }
    .gallery-stats span { display: inline-flex; align-items: center; gap: 0.25rem; }
    .gallery-stats i { color: #c8a862; font-size: 0.65rem; }
    .gallery-new {
        display: inline-flex; align-items: center; gap: 0.3rem;
        padding: 0.2rem 0.55rem; border-radius: 9999px; font-size: 0.65rem; font-weight: 600;
        background: rgba(200,168,98,0.12); color: #c8a862; border: 1px solid rgba(200,168,98,0.2);
    }

    .notification-list { display: grid; gap: 0.6rem; }
    .notification-item {
        display: flex; align-items: flex-start; gap: 0.9rem; padding: 0.85rem 1rem;
        background: rgba(255,255,255,0.02); border: 1px solid rgba(200,168,98,0.06);
        border-radius: 0.75rem; transition: all 0.2s ease; cursor: pointer; position: relative;
    }
    .notification-item:hover { background: rgba(255,255,255,0.04); border-color: rgba(200,168,98,0.12); }
    .notification-item.unread { background: rgba(200,168,98,0.04); border-color: rgba(200,168,98,0.12); }
    .notification-item.unread::before {
        content: ''; position: absolute; top: 50%; right: 0.5rem; transform: translateY(-50%);
        width: 6px; height: 6px; border-radius: 50%; background: #c8a862;
    }
    .notification-icon {
        width: 38px; height: 38px; border-radius: 50%; flex-shrink: 0;
        background: rgba(200,168,98,0.08); border: 1px solid rgba(200,168,98,0.12);
        display: flex; align-items: center; justify-content: center; color: #c8a862; font-size: 0.85rem;
    }
    .notification-body { flex: 1; }
    .notification-title { font-size: 0.82rem; color: #f4f1ea; font-weight: 500; margin-bottom: 0.15rem; }
    .notification-text { font-size: 0.72rem; color: #8a8580; line-height: 1.5; }
    .notification-time { font-size: 0.65rem; color: #6b6660; margin-top: 0.3rem; }

    .empty-state {
        text-align: center; padding: 3rem 1.5rem;
        background: rgba(255,255,255,0.02); border: 1px solid rgba(200,168,98,0.06);
        border-radius: 0.85rem;
    }
    .empty-state i { font-size: 2.5rem; color: #c8a862; margin-bottom: 0.75rem; opacity: 0.6; }
    .empty-state h3 { font-family: 'Cormorant Garamond', Georgia, serif; font-size: 1.3rem; color: #f4f1ea; margin-bottom: 0.3rem; font-weight: 300; }
    .empty-state p { color: #8a8580; font-size: 0.8rem; }

    .modal-overlay {
        position: fixed; inset: 0; z-index: 2000;
        background: rgba(10,9,8,0.85); backdrop-filter: blur(8px);
        display: none; align-items: center; justify-content: center; padding: 1rem;
    }
    .modal-overlay.active { display: flex; }
    .modal {
        background: #151210; border: 1px solid rgba(200,168,98,0.15);
        border-radius: 1rem; padding: 1.5rem; width: 100%; max-width: 420px;
        box-shadow: 0 24px 70px rgba(0,0,0,0.6); transform: scale(0.95); opacity: 0;
        transition: all 0.3s cubic-bezier(0.34,1.56,0.64,1);
    }
    .modal-overlay.active .modal { transform: scale(1); opacity: 1; }
    .modal h3 { font-family: 'Cormorant Garamond', Georgia, serif; font-size: 1.3rem; color: #f4f1ea; margin-bottom: 0.5rem; font-weight: 300; }
    .modal p { color: #8a8580; font-size: 0.8rem; margin-bottom: 1rem; }
    .modal .form-group { margin-bottom: 1rem; }
    .modal .form-group label { display: block; font-size: 0.75rem; color: #8a8580; margin-bottom: 0.3rem; }
    .modal .form-group input, .modal .form-group textarea, .modal .form-group select {
        width: 100%; padding: 0.7rem 0.9rem; background: rgba(255,255,255,0.04);
        border: 1px solid rgba(200,168,98,0.1); border-radius: 0.6rem; color: #f4f1ea;
        font-size: 0.85rem; font-family: 'Vazirmatn', sans-serif; outline: none;
    }
    .modal .form-group input:focus, .modal .form-group textarea:focus, .modal .form-group select:focus { border-color: #c8a862; }
    .modal-actions { display: flex; justify-content: flex-end; gap: 0.5rem; margin-top: 1.25rem; }
    .modal-actions button {
        padding: 0.55rem 1.2rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 600;
        cursor: pointer; border: 1px solid transparent; transition: all 0.2s ease;
    }
    .modal-actions .btn-confirm { background: linear-gradient(135deg, #c8a862, #a8893a); color: #0a0908; }
    .modal-actions .btn-confirm:hover { transform: translateY(-1px); box-shadow: 0 6px 20px rgba(200,168,98,0.2); }
    .modal-actions .btn-cancel { background: transparent; color: #8a8580; border-color: rgba(200,168,98,0.15); }
    .modal-actions .btn-cancel:hover { color: #f4f1ea; border-color: #c8a862; }

    .skeleton { background: linear-gradient(90deg, rgba(255,255,255,0.03) 25%, rgba(255,255,255,0.07) 50%, rgba(255,255,255,0.03) 75%); background-size: 200% 100%; animation: shimmer 1.2s infinite; border-radius: 0.5rem; }
    @keyframes shimmer { 0% { background-position: 200% 0; } 100% { background-position: -200% 0; } }

    @media (max-width: 768px) {
        .dashboard-header { flex-direction: column; align-items: flex-start; }
        .dashboard-actions { margin-right: 0; width: 100%; }
        .dashboard-actions a { flex: 1; justify-content: center; }
        .stats-grid { grid-template-columns: repeat(2, 1fr); }
        .booking-card { flex-direction: column; align-items: flex-start; }
        .booking-main { width: 100%; }
    }
    @media (max-width: 480px) {
        .stats-grid { grid-template-columns: 1fr; }
    }
</style>

<div class="dashboard-wrap">
    <div class="dashboard-header">
        <div class="user-avatar">
            <?php if (!empty($user['avatar'])): ?>
                <img src="<?php echo h($user['avatar']); ?>" alt="" loading="lazy">
            <?php else: ?>
                <?php echo mb_substr($user['name'], 0, 1, 'UTF-8'); ?>
            <?php endif; ?>
        </div>
        <div class="user-info">
            <h1><?php echo h($user['name']); ?></h1>
            <p><?php echo h($user['phone']); ?> <?php echo !empty($user['email']) ? '• ' . h($user['email']) : ''; ?></p>
        </div>
        <div class="dashboard-actions">
            <a href="<?php echo url('notifications'); ?>" class="btn-outline">
                <i class="fas fa-bell"></i> اعلانات
                <?php if ($unread_count > 0): ?><span class="badge"><?php echo $unread_count; ?></span><?php endif; ?>
            </a>
            <a href="<?php echo url('booking'); ?>" class="btn-gold"><i class="fas fa-plus"></i> رزرو جدید</a>
        </div>
    </div>

    <div class="stats-grid">
        <div class="stat-card" style="--accent:#c8a862;">
            <div class="icon"><i class="fas fa-calendar-check"></i></div>
            <div class="value"><?php echo $total; ?></div>
            <div class="label">کل رزروها</div>
        </div>
        <div class="stat-card" style="--accent:#4ade80;">
            <div class="icon"><i class="fas fa-images"></i></div>
            <div class="value" style="color:#4ade80;"><?php echo (int) ($gallery_stats['total_galleries'] ?? 0); ?></div>
            <div class="label">گالری‌های من</div>
        </div>
        <div class="stat-card" style="--accent:#60a5fa;">
            <div class="icon"><i class="fas fa-download"></i></div>
            <div class="value" style="color:#60a5fa;"><?php echo $download_count; ?></div>
            <div class="label">دانلودها</div>
        </div>
        <div class="stat-card" style="--accent:#f472b6;">
            <div class="icon"><i class="fas fa-eye"></i></div>
            <div class="value" style="color:#f472b6;"><?php echo (int) ($gallery_stats['total_views'] ?? 0); ?></div>
            <div class="label">بازدید گالری‌ها</div>
        </div>
    </div>

    <div class="two-col">
        <div class="left-col">
            <div class="section-card">
                <div class="section-card-header">
                    <h2 class="section-title"><i class="fas fa-list-alt"></i> رزروهای شما</h2>
                    <div class="filter-pills">
                        <?php
                        $filterLabels = [null => 'همه', 'PENDING' => 'در انتظار', 'CONFIRMED' => 'تایید شده', 'CANCELLED' => 'لغو شده', 'COMPLETED' => 'انجام شده'];
                        foreach ($filterLabels as $value => $label):
                            $isActive = $status_filter === $value || ($status_filter === null && $value === null);
                            $url = $value ? url('dashboard?status=' . $value) : url('dashboard');
                        ?>
                            <a href="<?php echo $url; ?>" class="filter-pill <?php echo $isActive ? 'active' : ''; ?>"><?php echo $label; ?></a>
                        <?php endforeach; ?>
                    </div>
                </div>

                <?php if (!empty($bookings)): ?>
                    <div class="bookings-grid" id="bookingsGrid">
                        <?php foreach ($bookings as $booking): ?>
                            <?php
                            $status = $statusMap[$booking['status']] ?? ['label' => $booking['status'], 'color' => '#8a8580', 'bg' => 'rgba(138,133,128,0.08)', 'icon' => 'fa-info-circle'];
                            $pkg = $packageLabels[$booking['package_id']] ?? ['name' => $booking['package_id'], 'icon' => 'fa-camera'];
                            $req = $requestStatusMap[$booking['request_status'] ?? 'NONE'] ?? $requestStatusMap['NONE'];
                            $canRequest = !in_array($booking['status'], ['CANCELLED', 'COMPLETED'], true) && ($booking['request_status'] ?? 'NONE') !== 'PENDING';
                            ?>
                            <div class="booking-card" data-booking-id="<?php echo $booking['id']; ?>">
                                <div class="booking-main">
                                    <div class="booking-icon"><i class="fas <?php echo $pkg['icon']; ?>"></i></div>
                                    <div>
                                        <div class="booking-title">
                                            <?php echo h($pkg['name']); ?>
                                            <?php if ($req['label']): ?>
                                                <span class="booking-request-badge" style="color:<?php echo $req['color']; ?>;border-color:<?php echo $req['color']; ?>;"><?php echo $req['label']; ?></span>
                                            <?php endif; ?>
                                        </div>
                                        <div class="booking-meta">
                                            <span><i class="fas fa-calendar"></i> <?php echo h($booking['jalali_date']); ?></span>
                                            <span><i class="fas fa-clock"></i> <?php echo h($booking['time']); ?></span>
                                            <span><i class="fas fa-hashtag"></i> <?php echo h($booking['id']); ?></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="booking-actions">
                                    <div class="booking-status" style="background:<?php echo $status['bg']; ?>;color:<?php echo $status['color']; ?>;border-color:<?php echo $status['color']; ?>;">
                                        <i class="fas <?php echo $status['icon']; ?>"></i> <?php echo $status['label']; ?>
                                    </div>
                                    <?php if ($canRequest): ?>
                                        <button type="button" onclick="openRequestModal(<?php echo $booking['id']; ?>, 'cancel')">لغو</button>
                                        <button type="button" onclick="openRequestModal(<?php echo $booking['id']; ?>, 'reschedule')">تغییر زمان</button>
                                    <?php endif; ?>
                                </div>
                                <?php if (!empty($booking['note'])): ?>
                                    <div class="booking-note"><i class="fas fa-comment-alt"></i> <?php echo h($booking['note']); ?></div>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="empty-state">
                        <i class="fas fa-calendar-plus"></i>
                        <h3>هنوز رزروی ندارید</h3>
                        <p>اولین جلسه عکاسی خود را با Mirohood رزرو کنید.</p>
                    </div>
                <?php endif; ?>
            </div>

            <?php if (!empty($upcoming_bookings)): ?>
            <div class="section-card">
                <div class="section-card-header">
                    <h2 class="section-title"><i class="fas fa-hourglass-half"></i> نوبت‌های پیش‌رو</h2>
                </div>
                <div class="bookings-grid">
                    <?php foreach (array_slice($upcoming_bookings, 0, 3) as $booking): ?>
                        <?php $pkg = $packageLabels[$booking['package_id']] ?? ['name' => $booking['package_id'], 'icon' => 'fa-camera']; ?>
                        <div class="booking-card">
                            <div class="booking-main">
                                <div class="booking-icon"><i class="fas <?php echo $pkg['icon']; ?>"></i></div>
                                <div>
                                    <div class="booking-title"><?php echo h($pkg['name']); ?></div>
                                    <div class="booking-meta">
                                        <span><i class="fas fa-calendar"></i> <?php echo h($booking['jalali_date']); ?></span>
                                        <span><i class="fas fa-clock"></i> <?php echo h($booking['time']); ?></span>
                                    </div>
                                </div>
                            </div>
                            <div class="booking-status" style="background:rgba(200,168,98,0.08);color:#c8a862;border-color:#c8a862;">
                                <i class="fas fa-clock"></i> <?php echo $booking['status'] === 'CONFIRMED' ? 'تایید شده' : 'در انتظار'; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>
        </div>

        <div class="right-col">
            <div class="section-card">
                <div class="section-card-header">
                    <h2 class="section-title"><i class="fas fa-images"></i> گالری‌های اخیر</h2>
                    <a href="<?php echo url('my-gallery'); ?>" class="section-link">همه <i class="fas fa-arrow-left"></i></a>
                </div>
                <?php if (!empty($recent_galleries)): ?>
                    <div class="gallery-list">
                        <?php foreach ($recent_galleries as $gallery): ?>
                            <?php $first = (new ClientGalleryImage())->getByGallery($gallery['id']); $thumb = !empty($first) ? ($first[0]['thumbnail'] ?: $first[0]['image']) : ''; $isNew = strtotime($gallery['created_at'] ?? 'now') > strtotime('-7 days'); ?>
                            <a href="<?php echo url('my-gallery'); ?>" class="gallery-item">
                                <?php if ($thumb): ?>
                                    <img src="<?php echo h($thumb); ?>" alt="" class="gallery-thumb" loading="lazy">
                                <?php else: ?>
                                    <div class="gallery-thumb" style="display:flex;align-items:center;justify-content:center;color:#c8a862;"><i class="fas fa-images"></i></div>
                                <?php endif; ?>
                                <div class="gallery-info">
                                    <div class="gallery-title"><?php echo h($gallery['title']); ?></div>
                                    <div class="gallery-stats">
                                        <span><i class="fas fa-images"></i> <?php echo count($first); ?> عکس</span>
                                        <span><i class="fas fa-eye"></i> <?php echo (int) ($gallery['view_count'] ?? 0); ?></span>
                                        <span><i class="fas fa-download"></i> <?php echo (int) ($gallery['download_count'] ?? 0); ?></span>
                                    </div>
                                </div>
                                <?php if ($isNew): ?><span class="gallery-new">جدید</span><?php endif; ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="empty-state" style="padding:2rem;">
                        <i class="fas fa-images"></i>
                        <p>گالری فعالی ندارید</p>
                    </div>
                <?php endif; ?>
            </div>

            <div class="section-card">
                <div class="section-card-header">
                    <h2 class="section-title"><i class="fas fa-bell"></i> اعلانات</h2>
                    <?php if ($unread_count > 0): ?>
                        <button type="button" class="section-link" onclick="markAllRead()" style="background:none;border:none;cursor:pointer;">همه خواندم</button>
                    <?php endif; ?>
                </div>
                <?php if (!empty($recent_notifications)): ?>
                    <div class="notification-list" id="notificationList">
                        <?php foreach ($recent_notifications as $n): ?>
                            <?php $icon = $typeIcons[$n['type']] ?? 'fa-bell'; ?>
                            <div class="notification-item <?php echo $n['is_read'] ? '' : 'unread'; ?>" data-id="<?php echo $n['id']; ?>" onclick="markRead(<?php echo $n['id']; ?>, this)">
                                <div class="notification-icon"><i class="fas <?php echo $icon; ?>"></i></div>
                                <div class="notification-body">
                                    <div class="notification-title"><?php echo h($n['title']); ?></div>
                                    <div class="notification-text"><?php echo h($n['message']); ?></div>
                                    <div class="notification-time"><i class="far fa-clock" style="margin-left:0.3rem;"></i><?php echo timeAgo($n['created_at']); ?></div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="empty-state" style="padding:2rem;">
                        <i class="fas fa-bell-slash"></i>
                        <p>اعلانی وجود ندارد</p>
                    </div>
                <?php endif; ?>
                <div style="text-align:center;margin-top:1rem;">
                    <a href="<?php echo url('notifications'); ?>" class="section-link">مشاهده همه</a>
                </div>
            </div>

            <div class="section-card">
                <div class="section-card-header">
                    <h2 class="section-title"><i class="fas fa-download"></i> تاریخچه دانلود</h2>
                    <a href="<?php echo url('downloads'); ?>" class="section-link">همه <i class="fas fa-arrow-left"></i></a>
                </div>
                <div class="empty-state" style="padding:2rem;">
                    <i class="fas fa-history"></i>
                    <p><?php echo $download_count; ?> دانلود ثبت شده</p>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal-overlay" id="requestModal">
    <div class="modal">
        <h3 id="modalTitle">درخواست نوبت</h3>
        <p id="modalDesc">توضیحات درخواست خود را وارد کنید.</p>
        <form id="requestForm">
            <input type="hidden" name="csrf_token" value="<?php echo h($csrf_token ?? generate_csrf_token()); ?>">
            <input type="hidden" name="booking_id" id="modalBookingId">
            <input type="hidden" name="request_action" id="modalAction">
            <div class="form-group" id="rescheduleFields" style="display:none;">
                <label>تاریخ جدید</label>
                <input type="text" name="new_date" id="newDate" placeholder="YYYY-MM-DD" pattern="\d{4}-\d{2}-\d{2}">
            </div>
            <div class="form-group" id="timeField" style="display:none;">
                <label>ساعت جدید</label>
                <select name="new_time" id="newTime">
                    <?php for ($i = 9; $i <= 20; $i++): ?>
                        <option value="<?php echo sprintf('%02d:00', $i); ?>"><?php echo sprintf('%02d:00', $i); ?></option>
                        <option value="<?php echo sprintf('%02d:30', $i); ?>"><?php echo sprintf('%02d:30', $i); ?></option>
                    <?php endfor; ?>
                </select>
            </div>
            <div class="form-group">
                <label>توضیحات (اختیاری)</label>
                <textarea name="note" id="modalNote" rows="3" placeholder="توضیحات خود را بنویسید..."></textarea>
            </div>
            <div class="modal-actions">
                <button type="button" class="btn-cancel" onclick="closeModal()">انصراف</button>
                <button type="submit" class="btn-confirm">ثبت درخواست</button>
            </div>
        </form>
    </div>
</div>

<script>
(function() {
    var csrfToken = '<?php echo h($_SESSION['csrf_token'] ?? generate_csrf_token()); ?>';
    var modal = document.getElementById('requestModal');
    var form = document.getElementById('requestForm');
    var modalTitle = document.getElementById('modalTitle');
    var modalDesc = document.getElementById('modalDesc');
    var rescheduleFields = document.getElementById('rescheduleFields');
    var timeField = document.getElementById('timeField');

    window.openRequestModal = function(bookingId, action) {
        document.getElementById('modalBookingId').value = bookingId;
        document.getElementById('modalAction').value = action;
        if (action === 'cancel') {
            modalTitle.textContent = 'درخواست لغو نوبت';
            modalDesc.textContent = 'درخواست لغو شما برای ادمین ارسال می‌شود و پس از بررسی، وضعیت نوبت تغییر می‌کند.';
            rescheduleFields.style.display = 'none';
            timeField.style.display = 'none';
        } else {
            modalTitle.textContent = 'درخواست تغییر زمان';
            modalDesc.textContent = 'تاریخ و ساعت جدید پیشنهادی را وارد کنید.';
            rescheduleFields.style.display = 'block';
            timeField.style.display = 'block';
        }
        modal.classList.add('active');
    };

    window.closeModal = function() {
        modal.classList.remove('active');
        form.reset();
    };

    modal.addEventListener('click', function(e) {
        if (e.target === modal) closeModal();
    });

    form.addEventListener('submit', function(e) {
        e.preventDefault();
        var submitBtn = form.querySelector('button[type="submit"]');
        submitBtn.disabled = true;
        var formData = new FormData(form);
        fetch('<?php echo url('booking/request'); ?>', { method: 'POST', body: formData })
            .then(r => r.json())
            .then(function(data) {
                submitBtn.disabled = false;
                if (data.success) {
                    showToast(data.message, 'success');
                    closeModal();
                    setTimeout(function() { location.reload(); }, 1200);
                } else {
                    showToast(data.message || 'خطا', 'error');
                }
            })
            .catch(function() {
                submitBtn.disabled = false;
                showToast('خطا در ارتباط', 'error');
            });
    });

    window.markRead = function(id, el) {
        var formData = new FormData();
        formData.append('id', id);
        formData.append('csrf_token', csrfToken);
        fetch('<?php echo url('notifications/mark-read'); ?>', { method: 'POST', body: formData })
            .then(r => r.json())
            .then(function(data) {
                if (data.success) {
                    el.classList.remove('unread');
                    if (data.unread_count <= 0) {
                        var badge = document.querySelector('.dashboard-actions .badge');
                        if (badge) badge.remove();
                    }
                }
            });
    };

    window.markAllRead = function() {
        var formData = new FormData();
        formData.append('csrf_token', csrfToken);
        fetch('<?php echo url('notifications/mark-all-read'); ?>', { method: 'POST', body: formData })
            .then(r => r.json())
            .then(function() {
                document.querySelectorAll('.notification-item.unread').forEach(function(el) { el.classList.remove('unread'); });
                var badge = document.querySelector('.dashboard-actions .badge');
                if (badge) badge.remove();
                showToast('همه اعلانات خوانده شدند', 'success');
            });
    };
})();
</script>
