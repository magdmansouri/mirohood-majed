<?php
// views/partials/navbar.php
$current_page = $current_page ?? '';
$isLoggedIn = is_logged_in();
?>
<nav class="navbar">
    <div class="container">
<a href="<?php echo url(''); ?>" class="logo">Miro<span>hood</span></a>
        <ul class="nav-links">
            <li><a href="<?php echo url(''); ?>" class="<?php echo $current_page === 'home' ? 'active' : ''; ?>">Home</a></li>
            <li><a href="<?php echo url('gallery'); ?>" class="<?php echo $current_page === 'gallery' ? 'active' : ''; ?>">Gallery</a></li>
            <li><a href="<?php echo url('booking'); ?>" class="<?php echo $current_page === 'booking' ? 'active' : ''; ?>">Booking</a></li>
            <li><a href="<?php echo url('about'); ?>" class="<?php echo $current_page === 'about' ? 'active' : ''; ?>">About</a></li>
            <?php if ($isLoggedIn): ?>
                <li><a href="<?php echo url('admin'); ?>" class="<?php echo strpos($current_page, 'admin') !== false ? 'active' : ''; ?>">Dashboard</a></li>
                <li><a href="<?php echo url('admin/logout'); ?>">Logout</a></li>
            <?php else: ?>
                <li><a href="<?php echo url('admin/login'); ?>">Login</a></li>
            <?php endif; ?>
        </ul>
        <button class="menu-toggle md:hidden" aria-label="Toggle menu"><i class="fas fa-bars"></i></button>
    </div>
</nav>
<div id="mobile-menu" class="hidden md:hidden fixed inset-0 z-40 bg-(--color-noir) pt-20 px-6">
    <ul class="flex flex-col space-y-6 text-center">
        <li><a href="<?php echo url(''); ?>">Home</a></li>
        <li><a href="<?php echo url('gallery'); ?>">Gallery</a></li>
        <li><a href="<?php echo url('booking'); ?>">Booking</a></li>
        <li><a href="<?php echo url('about'); ?>">About</a></li>
        <?php if ($isLoggedIn): ?>
            <li><a href="<?php echo url('admin'); ?>">Dashboard</a></li>
            <li><a href="<?php echo url('admin/logout'); ?>">Logout</a></li>
        <?php else: ?>
            <li><a href="<?php echo url('admin/login'); ?>">Login</a></li>
        <?php endif; ?>
    </ul>
</div>