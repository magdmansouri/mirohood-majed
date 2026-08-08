<?php
// views/admin/dashboard.php

$totalBookings = $totalBookings ?? 0;
$pendingBookings = $pendingBookings ?? 0;
$galleryCount = $galleryCount ?? 0;
$contentCount = $contentCount ?? 0;
?>

<h1 class="admin-page-title">داشبورد مدیریت</h1>

<p class="admin-page-subtitle">
    خوش آمدید، وضعیت کلی سایت:
</p>


<div style="
display:grid;
grid-template-columns:repeat(auto-fit,minmax(220px,1fr));
gap:1.2rem;
margin-top:2rem;
">


<div class="admin-card">
<h3 style="color:#c8a862;">
📅 رزروها
</h3>

<p style="font-size:2rem;">
<?php echo h($totalBookings); ?>
</p>

<a href="<?php echo url('admin/bookings'); ?>">
مدیریت رزروها
</a>

</div>



<div class="admin-card">
<h3 style="color:#c8a862;">
⏳ رزروهای در انتظار
</h3>

<p style="font-size:2rem;">
<?php echo h($pendingBookings); ?>
</p>

</div>




<div class="admin-card">
<h3 style="color:#c8a862;">
🖼 گالری
</h3>

<p style="font-size:2rem;">
<?php echo h($galleryCount); ?>
</p>

<a href="<?php echo url('admin/gallery'); ?>">
مدیریت گالری
</a>

</div>




<div class="admin-card">
<h3 style="color:#c8a862;">
📝 محتوا
</h3>

<p style="font-size:2rem;">
<?php echo h($contentCount); ?>
</p>

<a href="<?php echo url('admin/content'); ?>">
ویرایش محتوا
</a>

</div>


</div>