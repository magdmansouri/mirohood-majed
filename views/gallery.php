<?php
// views/gallery.php
$people = $people ?? [];
$content = $content ?? [];
$page_title = $page_title ?? 'گالری';
$page_description = $page_description ?? '';
$featured = array_filter($people, function($p) { return !empty($p['featured']); });
$regular = array_filter($people, function($p) { return empty($p['featured']); });
$ordered = array_merge($featured, $regular);
$photoLabel = $content['gallery.photo_count_label'] ?? 'عکس';
?>
<section class="gallery-page"<?php if (!empty($galleryBg)): ?> style="background-image:url('<?php echo h($galleryBg); ?>');background-size:cover;background-position:center;background-attachment:fixed;background-repeat:no-repeat;"<?php endif; ?>>
    <div class="gallery-container">
        <header class="gallery-header">
            <p class="gallery-eyebrow"><?php echo h($content['gallery.eyebrow'] ?? 'Gallery'); ?></p>
            <h1 class="gallery-title"><?php echo h($content['gallery.title'] ?? 'Gallery'); ?></h1>
            <p class="gallery-subtitle"><?php echo h($content['gallery.subtitle'] ?? 'آلبوم‌های عکاسی بر اساس افراد'); ?></p>
        </header>

        <?php if (!empty($ordered)): ?>
            <div class="people-grid">
                <?php foreach ($ordered as $person): ?>
                    <a href="<?php echo url('gallery/' . h($person['slug'])); ?>" class="person-card" aria-label="<?php echo h($person['name']); ?>">
                        <div class="person-card-media">
                            <?php if (!empty($person['avatar'])): ?>
                                <img src="<?php echo h($person['avatar']); ?>" alt="<?php echo h($person['name']); ?>" loading="lazy" decoding="async">
                            <?php else: ?>
                                <div class="person-initial"><?php echo h(mb_substr($person['name'], 0, 1, 'UTF-8')); ?></div>
                            <?php endif; ?>
                            <div class="person-card-overlay">
                                <span class="person-count"><?php echo (int) ($person['photo_count'] ?? 0); ?> <?php echo h($photoLabel); ?></span>
                            </div>
                        </div>
                        <div class="person-card-info">
                            <h2 class="person-name"><?php echo h($person['name']); ?></h2>
                            <?php if (!empty($person['bio'])): ?>
                                <p class="person-bio"><?php echo h(mb_substr($person['bio'], 0, 60, 'UTF-8') . (mb_strlen($person['bio'], 'UTF-8') > 60 ? '…' : '')); ?></p>
                            <?php endif; ?>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="gallery-empty">
                <p><?php echo h($content['gallery.empty_text'] ?? 'هنوز شخصی به گالری اضافه نشده است.'); ?></p>
            </div>
        <?php endif; ?>
    </div>
</section>