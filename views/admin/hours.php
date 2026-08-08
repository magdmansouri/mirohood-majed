<?php
// views/admin/hours.php - ساعت کاری
$contentModel = new SiteContent();
$holidayDates = $contentModel->get('holiday_dates');
$holidayDates = $holidayDates ? json_decode($holidayDates, true) : [];
$csrf_token = generate_csrf_token();
?>
<h1 class="admin-page-title">تعطیلی روزهای خاص</h1>
<p class="admin-page-subtitle">روزهایی که دفتر تعطیل است را مشخص کنید</p>

<!-- ===== Add Holiday ===== -->
<div class="admin-card">
    <h2 class="admin-card-title">➕ افزودن روز تعطیل</h2>
    <form method="POST" action="<?php echo url('admin/api/update-exceptions'); ?>" id="holiday-form">
        <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
        
        <div style="display:flex;gap:1rem;align-items:flex-end;flex-wrap:wrap;">
            <div style="flex:1;min-width:200px;position:relative;">
                <label class="admin-label">تاریخ (مثال: 1404/01/15)</label>
                <div style="display:flex;gap:0.5rem;">
                    <input type="text" name="new_holiday" id="new_holiday" placeholder="1404/01/15" class="admin-input" autocomplete="off">
                    <button type="button" id="openCalendarBtn" class="admin-btn admin-btn-outline" style="padding:0.6rem 0.9rem;flex-shrink:0;" title="انتخاب از تقویم">
                        <i class="fas fa-calendar-days"></i>
                    </button>
                </div>
                <div id="jalaliCalendar" class="jalali-calendar hidden"></div>
            </div>
            <button type="submit" class="admin-btn" id="addHolidayBtn">
                <i class="fas fa-plus"></i> <span>افزودن</span>
            </button>
        </div>
    </form>
</div>

<!-- ===== Holiday List ===== -->
<div class="admin-card">
    <h2 class="admin-card-title">📋 لیست روزهای تعطیل</h2>
    
    <div id="holidayListEmpty" style="<?php echo !empty($holidayDates) ? 'display:none;' : ''; ?>color:var(--color-muted);text-align:center;padding:1rem 0;">
        هیچ روز تعطیلی ثبت نشده است.
    </div>
    <div id="holidayList" style="display:grid;grid-template-columns:repeat(auto-fill,minmax(180px,1fr));gap:0.5rem;">
        <?php foreach ($holidayDates as $date): ?>
            <div class="holiday-pill" data-date="<?php echo h($date); ?>" style="display:flex;justify-content:space-between;align-items:center;padding:0.4rem 0.8rem;background:rgba(239,68,68,0.04);border:1px solid rgba(239,68,68,0.06);border-radius:0.5rem;">
                <span style="color:var(--danger);font-size:0.85rem;">📅 <?php echo h($date); ?></span>
                <button type="button" class="remove-holiday-btn" data-date="<?php echo h($date); ?>" style="background:none;border:none;color:var(--danger);font-size:1rem;cursor:pointer;transition:all 0.2s ease;">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<style>
    .jalali-calendar {
        position: absolute;
        top: calc(100% + 0.4rem);
        right: 0;
        z-index: 50;
        width: 260px;
        background: var(--color-panel);
        border: 1px solid var(--color-glass-border);
        border-radius: 0.8rem;
        padding: 0.8rem;
        box-shadow: 0 20px 60px rgba(0,0,0,0.5);
    }
    .jalali-calendar.hidden { display: none; }
    .jalali-calendar .cal-head {
        display: flex; align-items: center; justify-content: space-between;
        margin-bottom: 0.6rem; font-size: 0.85rem; color: var(--color-ivory);
    }
    .jalali-calendar .cal-head button {
        background: rgba(255,255,255,0.04); border: 1px solid var(--color-glass-border);
        color: var(--color-ivory); border-radius: 0.4rem; width: 1.8rem; height: 1.8rem; cursor: pointer;
    }
    .jalali-calendar .cal-grid {
        display: grid; grid-template-columns: repeat(7, 1fr); gap: 0.2rem;
    }
    .jalali-calendar .cal-grid span.dow {
        font-size: 0.65rem; color: var(--color-muted); text-align: center; padding-bottom: 0.3rem;
    }
    .jalali-calendar .cal-grid button.day {
        background: none; border: none; color: var(--color-ivory);
        font-size: 0.75rem; padding: 0.35rem 0; border-radius: 0.4rem; cursor: pointer;
    }
    .jalali-calendar .cal-grid button.day:hover { background: rgba(200,168,98,0.12); color: var(--color-champagne); }
    .jalali-calendar .cal-grid button.day.today { border: 1px solid var(--color-champagne); }
    .jalali-calendar .cal-grid button.day.empty { visibility: hidden; cursor: default; }
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var csrfToken = '<?php echo $csrf_token; ?>';

    /* ============================================================
       تبدیل تاریخ میلادی به شمسی (الگوریتم استاندارد، بدون کتابخانه خارجی)
       ============================================================ */
    function gregorianToJalali(gy, gm, gd) {
        var g_d_m = [0, 31, 59, 90, 120, 151, 181, 212, 243, 273, 304, 334];
        var gy2 = (gm > 2) ? (gy + 1) : gy;
        var days = 355666 + (365 * gy) + Math.floor((gy2 + 3) / 4) - Math.floor((gy2 + 99) / 100) + Math.floor((gy2 + 399) / 400) + gd + g_d_m[gm - 1];
        var jy = -1595 + (33 * Math.floor(days / 12053));
        days %= 12053;
        jy += 4 * Math.floor(days / 1461);
        days %= 1461;
        if (days > 365) {
            jy += Math.floor((days - 1) / 365);
            days = (days - 1) % 365;
        }
        var jm, jd;
        if (days < 186) {
            jm = 1 + Math.floor(days / 31);
            jd = 1 + (days % 31);
        } else {
            jm = 7 + Math.floor((days - 186) / 30);
            jd = 1 + ((days - 186) % 30);
        }
        return { jy: jy, jm: jm, jd: jd };
    }

    function pad(n) { return n < 10 ? '0' + n : '' + n; }

    var today = new Date();
    var todayJalali = gregorianToJalali(today.getFullYear(), today.getMonth() + 1, today.getDate());

    var viewYear = todayJalali.jy;
    var viewMonth = todayJalali.jm;

    var monthNames = ['فروردین','اردیبهشت','خرداد','تیر','مرداد','شهریور','مهر','آبان','آذر','دی','بهمن','اسفند'];
    var dowNames = ['ش','ی','د','س','چ','پ','ج'];

    function jalaliMonthLength(jy, jm) {
        if (jm <= 6) return 31;
        if (jm <= 11) return 30;
        // اسفند: تشخیص کبیسه با روش تقریبی رایج
        var isLeap = [1,5,9,13,17,22,26,30].indexOf(((jy - (jy > 0 ? 979 : 980)) % 33 + 33) % 33) !== -1;
        return isLeap ? 30 : 29;
    }

    // محاسبه روز هفته اول ماه شمسی با استفاده از یک تاریخ میلادی مرجع شناخته‌شده
    function firstWeekdayOfJalaliMonth(jy, jm) {
        // 1404/01/01 شمسی برابر است با 2025-03-21 میلادی (چهارشنبه = index 3 در آرایه‌ی ش=0)
        var refG = new Date(Date.UTC(2025, 2, 21));
        var refJy = 1404, refJm = 1, refJd = 1;

        var targetDaysFromRef = 0;
        var y = refJy, m = refJm;
        if (jy > refJy || (jy === refJy && jm > refJm)) {
            while (y !== jy || m !== jm) {
                targetDaysFromRef += jalaliMonthLength(y, m);
                m++;
                if (m > 12) { m = 1; y++; }
            }
        } else if (jy < refJy || (jy === refJy && jm < refJm)) {
            while (y !== jy || m !== jm) {
                m--;
                if (m < 1) { m = 12; y--; }
                targetDaysFromRef -= jalaliMonthLength(y, m);
            }
        }

        var targetDate = new Date(refG.getTime() + targetDaysFromRef * 86400000);
        // getUTCDay: 0=یکشنبه در جاوااسکریپت. می‌خواهیم ش=0 ... ج=6
        var jsDow = targetDate.getUTCDay(); // 0=Sun,1=Mon,...6=Sat
        return (jsDow + 1) % 7; // تبدیل به ش=0
    }

    function renderCalendar() {
        var cal = document.getElementById('jalaliCalendar');
        var monthLen = jalaliMonthLength(viewYear, viewMonth);
        var firstDow = firstWeekdayOfJalaliMonth(viewYear, viewMonth);

        var html = '<div class="cal-head">' +
            '<button type="button" id="calPrev"><i class="fas fa-chevron-right"></i></button>' +
            '<span>' + monthNames[viewMonth - 1] + ' ' + viewYear + '</span>' +
            '<button type="button" id="calNext"><i class="fas fa-chevron-left"></i></button>' +
            '</div><div class="cal-grid">';

        dowNames.forEach(function (d) { html += '<span class="dow">' + d + '</span>'; });
        for (var i = 0; i < firstDow; i++) html += '<button type="button" class="day empty"></button>';
        for (var d = 1; d <= monthLen; d++) {
            var isToday = (viewYear === todayJalali.jy && viewMonth === todayJalali.jm && d === todayJalali.jd);
            html += '<button type="button" class="day' + (isToday ? ' today' : '') + '" data-day="' + d + '">' + d + '</button>';
        }
        html += '</div>';
        cal.innerHTML = html;

        document.getElementById('calPrev').addEventListener('click', function () {
            viewMonth--; if (viewMonth < 1) { viewMonth = 12; viewYear--; }
            renderCalendar();
        });
        document.getElementById('calNext').addEventListener('click', function () {
            viewMonth++; if (viewMonth > 12) { viewMonth = 1; viewYear++; }
            renderCalendar();
        });
        cal.querySelectorAll('.day:not(.empty)').forEach(function (btn) {
            btn.addEventListener('click', function () {
                document.getElementById('new_holiday').value = viewYear + '/' + pad(viewMonth) + '/' + pad(parseInt(btn.dataset.day, 10));
                cal.classList.add('hidden');
            });
        });
    }

    var calBox = document.getElementById('jalaliCalendar');
    document.getElementById('openCalendarBtn').addEventListener('click', function (e) {
        e.stopPropagation();
        renderCalendar();
        calBox.classList.toggle('hidden');
    });
    document.addEventListener('click', function (e) {
        if (!calBox.contains(e.target) && e.target.id !== 'openCalendarBtn') {
            calBox.classList.add('hidden');
        }
    });

    /* ============================================================
       افزودن روز تعطیل (AJAX، بدون رفرش صفحه)
       ============================================================ */
    var holidayForm = document.getElementById('holiday-form');
    var holidayList = document.getElementById('holidayList');
    var holidayListEmpty = document.getElementById('holidayListEmpty');

    function addHolidayPill(date) {
        holidayListEmpty.style.display = 'none';
        var div = document.createElement('div');
        div.className = 'holiday-pill';
        div.dataset.date = date;
        div.style.cssText = 'display:flex;justify-content:space-between;align-items:center;padding:0.4rem 0.8rem;background:rgba(239,68,68,0.04);border:1px solid rgba(239,68,68,0.06);border-radius:0.5rem;';
        div.innerHTML = '<span style="color:var(--danger);font-size:0.85rem;">📅 ' + date + '</span>' +
            '<button type="button" class="remove-holiday-btn" data-date="' + date + '" style="background:none;border:none;color:var(--danger);font-size:1rem;cursor:pointer;">' +
            '<i class="fas fa-times"></i></button>';
        holidayList.appendChild(div);
        bindRemove(div.querySelector('.remove-holiday-btn'));
    }

    holidayForm.addEventListener('submit', function (e) {
        e.preventDefault();
        var input = document.getElementById('new_holiday');
        var date = input.value.trim();
        if (!date) {
            showToast('❌ لطفاً یک تاریخ وارد کنید یا از تقویم انتخاب کنید', 'error');
            return;
        }

        var btn = document.getElementById('addHolidayBtn');
        btn.disabled = true;

        var fd = new FormData();
        fd.append('new_holiday', date);
        fd.append('csrf_token', csrfToken);

        fetch(holidayForm.action, {
            method: 'POST',
            body: fd,
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(function (r) { if (!r.ok) throw new Error('خطای سرور: ' + r.status); return r.json(); })
        .then(function (data) {
            if (data.success) {
                showToast('✅ ' + data.message, 'success');
                addHolidayPill(date);
                input.value = '';
            } else {
                showToast('❌ ' + (data.message || 'خطا در ذخیره'), 'error');
            }
        })
        .catch(function (err) {
            showToast('❌ خطا در ارتباط با سرور: ' + err.message, 'error');
        })
        .finally(function () { btn.disabled = false; });
    });

    function bindRemove(btn) {
        btn.addEventListener('click', function () {
            var date = btn.dataset.date;
            if (!confirm('آیا از حذف این تاریخ مطمئن هستید؟')) return;

            var fd = new FormData();
            fd.append('remove', date);
            fd.append('csrf_token', csrfToken);

            fetch('<?php echo url("admin/api/update-exceptions"); ?>', {
                method: 'POST',
                body: fd,
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(function (r) { if (!r.ok) throw new Error('خطای سرور: ' + r.status); return r.json(); })
            .then(function (data) {
                if (data.success) {
                    showToast('✅ روز تعطیل حذف شد', 'success');
                    var pill = holidayList.querySelector('.holiday-pill[data-date="' + CSS.escape(date) + '"]');
                    if (pill) pill.remove();
                    if (!holidayList.querySelector('.holiday-pill')) holidayListEmpty.style.display = '';
                } else {
                    showToast('❌ ' + (data.message || 'خطا در حذف'), 'error');
                }
            })
            .catch(function (err) {
                showToast('❌ خطا در ارتباط با سرور: ' + err.message, 'error');
            });
        });
    }

    document.querySelectorAll('.remove-holiday-btn').forEach(bindRemove);
});
</script>