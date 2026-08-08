<?php
// views/admin/bookings.php - مدیریت رزروها

$bookings = $bookings ?? [];
$status_filter = $status_filter ?? null;
$total_bookings = $total_bookings ?? 0;
$csrf_token = generate_csrf_token();
?>
<div style="max-width:1200px;margin:0 auto;padding:2rem 1.5rem;">
    
    <h1 style="font-family:'Cormorant Garamond',Georgia,serif;font-size:2.5rem;font-weight:300;margin-bottom:0.5rem;color:#f4f1ea;">
        📋 مدیریت رزروها
    </h1>
    <p style="color:#8a8580;margin-bottom:2rem;">تعداد کل: <?php echo $total_bookings; ?></p>
    
    <!-- ===== فیلتر وضعیت ===== -->
    <div style="display:flex;flex-wrap:wrap;gap:0.5rem;margin-bottom:1.5rem;">
        <a href="<?php echo url('admin/bookings'); ?>" style="display:inline-block;padding:0.4rem 1.2rem;border:1px solid <?php echo $status_filter === null ? '#c8a862' : 'rgba(200,168,98,0.2)'; ?>;border-radius:9999px;color:<?php echo $status_filter === null ? '#c8a862' : '#8a8580'; ?>;font-size:0.75rem;text-decoration:none;transition:all 0.3s ease;">
            همه
        </a>
        <a href="<?php echo url('admin/bookings?status=PENDING'); ?>" style="display:inline-block;padding:0.4rem 1.2rem;border:1px solid <?php echo $status_filter === 'PENDING' ? '#fbbf24' : 'rgba(200,168,98,0.2)'; ?>;border-radius:9999px;color:<?php echo $status_filter === 'PENDING' ? '#fbbf24' : '#8a8580'; ?>;font-size:0.75rem;text-decoration:none;transition:all 0.3s ease;">
            در انتظار
        </a>
        <a href="<?php echo url('admin/bookings?status=CONFIRMED'); ?>" style="display:inline-block;padding:0.4rem 1.2rem;border:1px solid <?php echo $status_filter === 'CONFIRMED' ? '#4ade80' : 'rgba(200,168,98,0.2)'; ?>;border-radius:9999px;color:<?php echo $status_filter === 'CONFIRMED' ? '#4ade80' : '#8a8580'; ?>;font-size:0.75rem;text-decoration:none;transition:all 0.3s ease;">
            تایید شده
        </a>
        <a href="<?php echo url('admin/bookings?status=CANCELLED'); ?>" style="display:inline-block;padding:0.4rem 1.2rem;border:1px solid <?php echo $status_filter === 'CANCELLED' ? '#f87171' : 'rgba(200,168,98,0.2)'; ?>;border-radius:9999px;color:<?php echo $status_filter === 'CANCELLED' ? '#f87171' : '#8a8580'; ?>;font-size:0.75rem;text-decoration:none;transition:all 0.3s ease;">
            لغو شده
        </a>
    </div>
    
    <!-- ===== لیست رزروها ===== -->
    <div style="background:rgba(255,255,255,0.03);border:1px solid rgba(200,168,98,0.08);border-radius:0.75rem;padding:1.5rem;overflow-x:auto;">
        <table style="width:100%;border-collapse:collapse;font-size:0.9rem;min-width:600px;">
            <thead>
                <tr style="border-bottom:1px solid rgba(200,168,98,0.08);">
                    <th style="text-align:right;padding:0.75rem 0.5rem;color:#8a8580;font-weight:400;font-size:0.75rem;">نام</th>
                    <th style="text-align:right;padding:0.75rem 0.5rem;color:#8a8580;font-weight:400;font-size:0.75rem;">موبایل</th>
                    <th style="text-align:right;padding:0.75rem 0.5rem;color:#8a8580;font-weight:400;font-size:0.75rem;">پکیج</th>
                    <th style="text-align:right;padding:0.75rem 0.5rem;color:#8a8580;font-weight:400;font-size:0.75rem;">تاریخ</th>
                    <th style="text-align:right;padding:0.75rem 0.5rem;color:#8a8580;font-weight:400;font-size:0.75rem;">ساعت</th>
                    <th style="text-align:right;padding:0.75rem 0.5rem;color:#8a8580;font-weight:400;font-size:0.75rem;">وضعیت</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($bookings)): ?>
                    <?php foreach ($bookings as $booking): ?>
                        <tr style="border-bottom:1px solid rgba(200,168,98,0.05);">
                            <td style="padding:0.75rem 0.5rem;"><?php echo h($booking['full_name']); ?></td>
                            <td style="padding:0.75rem 0.5rem;color:#8a8580;"><?php echo h($booking['phone']); ?></td>
                            <td style="padding:0.75rem 0.5rem;color:#8a8580;"><?php echo h($booking['package_id']); ?></td>
                            <td style="padding:0.75rem 0.5rem;color:#8a8580;"><?php echo h($booking['jalali_date']); ?></td>
                            <td style="padding:0.75rem 0.5rem;color:#8a8580;"><?php echo h($booking['time']); ?></td>
                            <td style="padding:0.75rem 0.5rem;">
                                <select class="status-select" data-id="<?php echo h($booking['id']); ?>" style="background:rgba(255,255,255,0.05);border:1px solid rgba(200,168,98,0.15);border-radius:0.5rem;padding:0.3rem 0.5rem;color:#f4f1ea;font-size:0.75rem;cursor:pointer;">
                                    <option value="PENDING" <?php echo $booking['status'] === 'PENDING' ? 'selected' : ''; ?>>در انتظار</option>
                                    <option value="CONFIRMED" <?php echo $booking['status'] === 'CONFIRMED' ? 'selected' : ''; ?>>تایید شده</option>
                                    <option value="CANCELLED" <?php echo $booking['status'] === 'CANCELLED' ? 'selected' : ''; ?>>لغو شده</option>
                                    <option value="COMPLETED" <?php echo $booking['status'] === 'COMPLETED' ? 'selected' : ''; ?>>انجام شده</option>
                                </select>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" style="text-align:center;padding:2rem 0;color:#8a8580;">هیچ رزروی وجود ندارد.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var csrfToken = '<?php echo $csrf_token; ?>';
    
    // ===== تغییر وضعیت رزرو =====
    document.querySelectorAll('.status-select').forEach(function(select) {
        select.addEventListener('change', function() {
            var bookingId = this.getAttribute('data-id');
            var status = this.value;
            
            if (!bookingId) return;
            
            var previousValue = this.dataset.previousValue || this.value;
            
            var formData = new FormData();
            formData.append('booking_id', bookingId);
            formData.append('status', status);
            formData.append('csrf_token', csrfToken);
            
            fetch('/admin/api/update-booking-status', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showToast('وضعیت با موفقیت به‌روز شد', 'success');
                    this.dataset.previousValue = status;
                } else {
                    showToast(data.message || 'خطا در به‌روزرسانی', 'error');
                    this.value = previousValue;
                }
            })
            .catch(function() {
                showToast('خطا در به‌روزرسانی', 'error');
                this.value = previousValue;
            });
        });
        
        select.dataset.previousValue = select.value;
    });
});
</script>