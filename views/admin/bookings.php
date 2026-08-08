<?php
// views/admin/bookings.php - مدیریت رزروها
$bookings = $bookings ?? [];
$status_filter = $status_filter ?? null;
$total_bookings = $total_bookings ?? 0;
$csrf_token = generate_csrf_token();

$filters = [
    null => 'همه',
    'PENDING' => 'در انتظار',
    'CONFIRMED' => 'تایید شده',
    'CANCELLED' => 'لغو شده',
    'COMPLETED' => 'انجام شده'
];

$statusMap = [
    'PENDING' => ['label' => 'در انتظار', 'class' => 'pending'],
    'CONFIRMED' => ['label' => 'تایید شده', 'class' => 'confirmed'],
    'CANCELLED' => ['label' => 'لغو شده', 'class' => 'cancelled'],
    'COMPLETED' => ['label' => 'انجام شده', 'class' => 'completed']
];

$requestMap = [
    'CANCEL' => 'درخواست لغو',
    'RESCHEDULE' => 'درخواست تغییر زمان',
    'NONE' => ''
];

$requestStatusMap = [
    'PENDING' => ['label' => 'در انتظار بررسی', 'class' => 'pending'],
    'APPROVED' => ['label' => 'تایید شده', 'class' => 'confirmed'],
    'REJECTED' => ['label' => 'رد شده', 'class' => 'cancelled'],
    'NONE' => ['label' => '', 'class' => '']
];
?>
<style>
.request-row {
    background: rgba(200,168,98,0.04) !important;
}
.request-row td {
    border-top: 1px dashed rgba(200,168,98,0.15);
    padding-top: 0.6rem !important;
    padding-bottom: 0.6rem !important;
}
.request-badge {
    display: inline-flex; align-items: center; gap: 0.3rem;
    padding: 0.2rem 0.6rem; border-radius: 9999px; font-size: 0.65rem; font-weight: 600;
}
.request-badge.pending { background: rgba(251,191,36,0.12); color: #fbbf24; border: 1px solid rgba(251,191,36,0.2); }
.request-badge.approved { background: rgba(34,197,94,0.12); color: #4ade80; border: 1px solid rgba(34,197,94,0.2); }
.request-badge.rejected { background: rgba(239,68,68,0.12); color: #f87171; border: 1px solid rgba(239,68,68,0.2); }
</style>
<style>
.action-buttons {
    display: flex;
    align-items: center;
    gap: 0.4rem;
    flex-wrap: wrap;
}
.btn-approve {
    display: inline-flex;
    align-items: center;
    gap: 0.3rem;
    padding: 0.25rem 0.8rem;
    background: rgba(34,197,94,0.08);
    border: 1px solid rgba(34,197,94,0.15);
    border-radius: 9999px;
    color: #4ade80;
    font-size: 0.65rem;
    font-weight: 600;
    font-family: 'Vazirmatn', sans-serif;
    cursor: pointer;
    transition: all 0.3s ease;
    text-decoration: none;
    white-space: nowrap;
}
.btn-approve:hover {
    background: rgba(34,197,94,0.15);
    border-color: rgba(34,197,94,0.3);
    transform: translateY(-1px);
    box-shadow: 0 2px 12px rgba(34,197,94,0.15);
}
.btn-approve:active {
    transform: scale(0.95);
}
.btn-approve i {
    font-size: 0.55rem;
}
.btn-reject {
    display: inline-flex;
    align-items: center;
    gap: 0.3rem;
    padding: 0.25rem 0.8rem;
    background: rgba(239,68,68,0.08);
    border: 1px solid rgba(239,68,68,0.15);
    border-radius: 9999px;
    color: #f87171;
    font-size: 0.65rem;
    font-weight: 600;
    font-family: 'Vazirmatn', sans-serif;
    cursor: pointer;
    transition: all 0.3s ease;
    text-decoration: none;
    white-space: nowrap;
}
.btn-reject:hover {
    background: rgba(239,68,68,0.15);
    border-color: rgba(239,68,68,0.3);
    transform: translateY(-1px);
    box-shadow: 0 2px 12px rgba(239,68,68,0.15);
}
.btn-reject:active {
    transform: scale(0.95);
}
.btn-reject i {
    font-size: 0.55rem;
}
.btn-complete {
    display: inline-flex;
    align-items: center;
    gap: 0.3rem;
    padding: 0.25rem 0.8rem;
    background: rgba(59,130,246,0.08);
    border: 1px solid rgba(59,130,246,0.15);
    border-radius: 9999px;
    color: #60a5fa;
    font-size: 0.65rem;
    font-weight: 600;
    font-family: 'Vazirmatn', sans-serif;
    cursor: pointer;
    transition: all 0.3s ease;
    text-decoration: none;
    white-space: nowrap;
}
.btn-complete:hover {
    background: rgba(59,130,246,0.15);
    border-color: rgba(59,130,246,0.3);
    transform: translateY(-1px);
    box-shadow: 0 2px 12px rgba(59,130,246,0.15);
}
.btn-complete:active {
    transform: scale(0.95);
}
.btn-complete i {
    font-size: 0.55rem;
}
.btn-uncomplete {
    display: inline-flex;
    align-items: center;
    gap: 0.3rem;
    padding: 0.25rem 0.8rem;
    background: rgba(251,191,36,0.08);
    border: 1px solid rgba(251,191,36,0.15);
    border-radius: 9999px;
    color: #fbbf24;
    font-size: 0.65rem;
    font-weight: 600;
    font-family: 'Vazirmatn', sans-serif;
    cursor: pointer;
    transition: all 0.3s ease;
    text-decoration: none;
    white-space: nowrap;
}
.btn-uncomplete:hover {
    background: rgba(251,191,36,0.15);
    border-color: rgba(251,191,36,0.3);
    transform: translateY(-1px);
    box-shadow: 0 2px 12px rgba(251,191,36,0.15);
}
.btn-uncomplete:active {
    transform: scale(0.95);
}
.btn-uncomplete i {
    font-size: 0.55rem;
}
.btn-delete {
    display: inline-flex;
    align-items: center;
    gap: 0.3rem;
    padding: 0.25rem 0.8rem;
    background: rgba(239,68,68,0.08);
    border: 1px solid rgba(239,68,68,0.15);
    border-radius: 9999px;
    color: #f87171;
    font-size: 0.65rem;
    font-weight: 600;
    font-family: 'Vazirmatn', sans-serif;
    cursor: pointer;
    transition: all 0.3s ease;
    text-decoration: none;
    white-space: nowrap;
}
.btn-delete:hover {
    background: rgba(239,68,68,0.15);
    border-color: rgba(239,68,68,0.3);
    transform: translateY(-1px);
    box-shadow: 0 2px 12px rgba(239,68,68,0.15);
}
.btn-delete:active {
    transform: scale(0.95);
}
.btn-delete i {
    font-size: 0.55rem;
}
.btn-approve:disabled,
.btn-reject:disabled,
.btn-complete:disabled,
.btn-uncomplete:disabled,
.btn-delete:disabled {
    opacity: 0.4;
    cursor: not-allowed;
    transform: none !important;
}
.status-badge-current {
    display: inline-block;
    padding: 0.2rem 0.7rem;
    border-radius: 9999px;
    font-size: 0.6rem;
    font-weight: 600;
    letter-spacing: 0.02em;
}
.status-badge-current.pending {
    background: rgba(234,179,8,0.12);
    color: #fbbf24;
    border: 1px solid rgba(234,179,8,0.15);
}
.status-badge-current.confirmed {
    background: rgba(34,197,94,0.12);
    color: #4ade80;
    border: 1px solid rgba(34,197,94,0.15);
}
.status-badge-current.cancelled {
    background: rgba(239,68,68,0.12);
    color: #f87171;
    border: 1px solid rgba(239,68,68,0.15);
}
.status-badge-current.completed {
    background: rgba(59,130,246,0.12);
    color: #60a5fa;
    border: 1px solid rgba(59,130,246,0.15);
}
@media (max-width: 768px) {
    .action-buttons {
        flex-direction: column;
        gap: 0.3rem;
    }
    .btn-approve,
    .btn-reject,
    .btn-complete,
    .btn-uncomplete,
    .btn-delete {
        padding: 0.2rem 0.6rem;
        font-size: 0.6rem;
        width: 100%;
        justify-content: center;
    }
}
</style>

<h1 class="admin-page-title">مدیریت رزروها</h1>
<p class="admin-page-subtitle">تعداد کل: <?php echo (int)$total_bookings; ?></p>

<input type="hidden" id="csrf_token_bookings" value="<?php echo h($csrf_token); ?>">

<!-- ===== Filters ===== -->
<div style="display:flex;flex-wrap:wrap;gap:0.4rem;margin-bottom:1.5rem;">
    <?php foreach ($filters as $value => $label): ?>
        <a href="<?php echo url('admin/bookings'.($value ? '?status='.$value : '')); ?>" class="filter-pill <?php echo $status_filter === $value ? 'active' : ''; ?>">
            <?php echo $label; ?>
        </a>
    <?php endforeach; ?>
</div>

<!-- ===== Table ===== -->
<div class="admin-card">
    <div class="admin-table-wrap">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>نام</th>
                    <th>موبایل</th>
                    <th>پکیج</th>
                    <th>تاریخ</th>
                    <th>ساعت</th>
                    <th>وضعیت</th>
                    <th>عملیات</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($bookings)): ?>
                    <?php foreach ($bookings as $booking): ?>
                        <tr id="booking-row-<?php echo $booking['id']; ?>">
                            <td>
    <?php echo h($booking['full_name']); ?>
    <?php if (!empty($booking['user_id'])): ?>
        <span style="display:inline-block;padding:0.15rem 0.5rem;border-radius:9999px;font-size:0.6rem;background:rgba(200,168,98,0.1);color:#c8a862;border:1px solid rgba(200,168,98,0.2);margin-right:0.3rem;">کاربر ثبت‌نام‌شده</span>
    <?php endif; ?>
</td>
                            <td style="color:var(--color-muted);font-size:0.85rem;"><?php echo h($booking['phone']); ?></td>
                            <td style="color:var(--color-muted);font-size:0.85rem;"><?php echo h($booking['package_id']); ?></td>
                            <td style="color:var(--color-muted);font-size:0.85rem;"><?php echo h($booking['jalali_date']); ?></td>
                            <td style="color:var(--color-muted);font-size:0.85rem;"><?php echo h($booking['time']); ?></td>
                            <td>
                                <span class="status-badge-current <?php echo $statusMap[$booking['status']]['class'] ?? ''; ?>">
                                    <?php echo $statusMap[$booking['status']]['label'] ?? $booking['status']; ?>
                                </span>
                                <?php if (!empty($booking['request_type']) && $booking['request_type'] !== 'NONE'): ?>
                                    <br>
                                    <span class="request-badge <?php echo $requestStatusMap[$booking['request_status']]['class'] ?? ''; ?>" style="margin-top:0.35rem;">
                                        <?php echo $requestMap[$booking['request_type']] ?? $booking['request_type']; ?>
                                        — <?php echo $requestStatusMap[$booking['request_status']]['label'] ?? $booking['request_status']; ?>
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if (!empty($booking['request_type']) && $booking['request_type'] !== 'NONE' && $booking['request_status'] === 'PENDING'): ?>
                                    <div class="action-buttons">
                                        <button class="btn-approve request-btn" data-id="<?php echo $booking['id']; ?>" data-decision="APPROVED">
                                            <i class="fas fa-check-circle"></i> تایید درخواست
                                        </button>
                                        <button class="btn-reject request-btn" data-id="<?php echo $booking['id']; ?>" data-decision="REJECTED">
                                            <i class="fas fa-times-circle"></i> رد درخواست
                                        </button>
                                        <?php if (!empty($booking['request_note'])): ?>
                                            <span style="color:var(--color-muted);font-size:0.65rem;align-self:center;max-width:120px;" title="<?php echo h($booking['request_note']); ?>"><i class="fas fa-comment"></i> <?php echo mb_strimwidth(h($booking['request_note']), 0, 20, '...'); ?></span>
                                        <?php endif; ?>
                                    </div>
                                <?php elseif ($booking['status'] === 'PENDING'): ?>
                                    <div class="action-buttons">
                                        <button class="btn-approve" data-id="<?php echo $booking['id']; ?>" data-status="CONFIRMED">
                                            <i class="fas fa-check-circle"></i> تایید
                                        </button>
                                        <button class="btn-reject" data-id="<?php echo $booking['id']; ?>" data-status="CANCELLED">
                                            <i class="fas fa-times-circle"></i> لغو
                                        </button>
                                        <button class="btn-delete" data-id="<?php echo $booking['id']; ?>" data-action="delete">
                                            <i class="fas fa-trash-alt"></i> حذف
                                        </button>
                                    </div>
                                <?php elseif ($booking['status'] === 'CONFIRMED'): ?>
                                    <div class="action-buttons">
                                        <button class="btn-complete" data-id="<?php echo $booking['id']; ?>" data-status="COMPLETED">
                                            <i class="fas fa-check-double"></i> انجام شد
                                        </button>
                                        <button class="btn-uncomplete" data-id="<?php echo $booking['id']; ?>" data-status="CANCELLED">
                                            <i class="fas fa-undo-alt"></i> انجام نشد
                                        </button>
                                        <button class="btn-delete" data-id="<?php echo $booking['id']; ?>" data-action="delete">
                                            <i class="fas fa-trash-alt"></i> حذف
                                        </button>
                                    </div>
                                <?php else: ?>
                                    <div class="action-buttons">
                                        <span style="color:var(--color-muted);font-size:0.7rem;align-self:center;">
                                            <i class="fas fa-lock" style="margin-left:0.3rem;"></i>
                                            <?php 
                                                $lockedLabels = [
                                                    'CANCELLED' => 'لغو شده',
                                                    'COMPLETED' => 'انجام شده'
                                                ];
                                                echo $lockedLabels[$booking['status']] ?? $booking['status'];
                                            ?>
                                        </span>
                                        <button class="btn-delete" data-id="<?php echo $booking['id']; ?>" data-action="delete">
                                            <i class="fas fa-trash-alt"></i> حذف
                                        </button>
                                    </div>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" style="text-align:center;padding:2rem 0;color:var(--color-muted);">
                            هیچ رزروی ثبت نشده است.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var buttons = document.querySelectorAll('.btn-approve, .btn-reject, .btn-complete, .btn-uncomplete:not(.request-btn)');

    // Request handlers
    document.querySelectorAll('.request-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var id = this.dataset.id;
            var decision = this.dataset.decision;
            if (!confirm('آیا از ' + (decision === 'APPROVED' ? 'تایید' : 'رد') + ' این درخواست مطمئن هستید؟')) return;

            var row = document.getElementById('booking-row-' + id);
            if (!row) return;
            var btns = row.querySelectorAll('button');
            btns.forEach(function(b) { b.disabled = true; b.innerHTML = '<i class="fas fa-spinner fa-spin"></i>'; });

            var formData = new FormData();
            formData.append('booking_id', id);
            formData.append('decision', decision);
            formData.append('csrf_token', document.getElementById('csrf_token_bookings').value);

            fetch('<?php echo url('admin/api/handle-booking-request'); ?>', {
                method: 'POST', headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }, body: formData
            })
            .then(function(r) { return r.json(); })
            .then(function(data) {
                if (data.success) {
                    showToast(data.message || '✅ درخواست بررسی شد', 'success');
                    setTimeout(function() { location.reload(); }, 1200);
                } else {
                    showToast(data.message || '❌ خطا', 'error');
                    btns.forEach(function(b) { b.disabled = false; b.innerHTML = '<i class="fas fa-check-circle"></i> تایید درخواست'; });
                }
            })
            .catch(function(error) {
                showToast('❌ خطا در ارتباط: ' + error.message, 'error');
                btns.forEach(function(b) { b.disabled = false; b.innerHTML = '<i class="fas fa-check-circle"></i> تایید درخواست'; });
            });
        });
    });

    buttons.forEach(function(btn) {
        btn.addEventListener('click', function() {
            var id = this.dataset.id;
            var status = this.dataset.status;
            
            if (!confirm('آیا از تغییر وضعیت این رزرو مطمئن هستید؟')) {
                return;
            }
            
            var formData = new FormData();
            formData.append('booking_id', id);
            formData.append('status', status);
            
            var row = document.getElementById('booking-row-' + id);
            if (!row) return;
            
            var btns = row.querySelectorAll('button');
            btns.forEach(function(b) {
                b.disabled = true;
                b.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
            });
            
            var apiUrl = '<?php echo url("admin/api/update-booking-status"); ?>';
            
            fetch(apiUrl, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                body: formData
            })
            .then(function(response) {
                if (!response.ok) {
                    throw new Error('Server Error: ' + response.status);
                }
                return response.json();
            })
            .then(function(data) {
                if (data.success) {
                    showToast(data.message || '✅ وضعیت با موفقیت تغییر کرد', 'success');
                    setTimeout(function() {
                        location.reload();
                    }, 1500);
                } else {
                    showToast(data.message || '❌ خطا در ذخیره وضعیت', 'error');
                    btns.forEach(function(b) {
                        b.disabled = false;
                        if (b.classList.contains('btn-approve')) {
                            b.innerHTML = '<i class="fas fa-check-circle"></i> تایید';
                        } else if (b.classList.contains('btn-reject')) {
                            b.innerHTML = '<i class="fas fa-times-circle"></i> لغو';
                        } else if (b.classList.contains('btn-complete')) {
                            b.innerHTML = '<i class="fas fa-check-double"></i> انجام شد';
                        } else if (b.classList.contains('btn-uncomplete')) {
                            b.innerHTML = '<i class="fas fa-undo-alt"></i> انجام نشد';
                        }
                    });
                }
            })
            .catch(function(error) {
                console.error('Error:', error);
                showToast('❌ خطا در ارتباط با سرور: ' + error.message, 'error');
                btns.forEach(function(b) {
                    b.disabled = false;
                    if (b.classList.contains('btn-approve')) {
                        b.innerHTML = '<i class="fas fa-check-circle"></i> تایید';
                    } else if (b.classList.contains('btn-reject')) {
                        b.innerHTML = '<i class="fas fa-times-circle"></i> لغو';
                    } else if (b.classList.contains('btn-complete')) {
                        b.innerHTML = '<i class="fas fa-check-double"></i> انجام شد';
                    } else if (b.classList.contains('btn-uncomplete')) {
                        b.innerHTML = '<i class="fas fa-undo-alt"></i> انجام نشد';
                    }
                });
            });
        });
    });

    // Delete booking handler
    var deleteButtons = document.querySelectorAll('.btn-delete');
    deleteButtons.forEach(function(btn) {
        btn.addEventListener('click', function() {
            var id = this.dataset.id;
            if (!confirm('آیا از حذف این رزرو مطمئن هستید؟ این عملیات قابل بازگشت نیست.')) {
                return;
            }

            var row = document.getElementById('booking-row-' + id);
            if (!row) return;

            var btns = row.querySelectorAll('button');
            btns.forEach(function(b) {
                b.disabled = true;
            });
            this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> حذف...';

            var formData = new FormData();
            formData.append('booking_id', id);
            formData.append('csrf_token', document.getElementById('csrf_token_bookings').value);

            var deleteUrl = '<?php echo url("admin/api/delete-booking"); ?>';

            fetch(deleteUrl, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                body: formData
            })
            .then(function(response) {
                if (!response.ok) {
                    throw new Error('Server Error: ' + response.status);
                }
                return response.json();
            })
            .then(function(data) {
                if (data.success) {
                    showToast(data.message || '✅ رزرو حذف شد', 'success');
                    row.style.transition = 'opacity 0.5s ease';
                    row.style.opacity = '0';
                    setTimeout(function() {
                        row.remove();
                    }, 500);
                } else {
                    showToast(data.message || '❌ خطا در حذف رزرو', 'error');
                    btns.forEach(function(b) {
                        b.disabled = false;
                    });
                    btn.innerHTML = '<i class="fas fa-trash-alt"></i> حذف';
                }
            })
            .catch(function(error) {
                console.error('Error:', error);
                showToast('❌ خطا در ارتباط با سرور: ' + error.message, 'error');
                btns.forEach(function(b) {
                    b.disabled = false;
                });
                btn.innerHTML = '<i class="fas fa-trash-alt"></i> حذف';
            });
        });
    });
});
</script>