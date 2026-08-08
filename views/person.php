<?php
// views/person.php
$person = $person ?? [];
$images = $images ?? [];
$content = $content ?? [];
$photoLabel = $content['gallery.person_photo_count_label'] ?? 'عکس';
$backLabel = $content['gallery.person_back_label'] ?? 'بازگشت به گالری';
$emptyText = $content['gallery.person_empty_text'] ?? 'هنوز تصویری برای این شخص آپلود نشده است.';
$websiteLabel = $content['gallery.person_website_label'] ?? 'Website';
$personInstagram = !empty($person['instagram']) ? ltrim($person['instagram'], '@') : '';
$personWebsite = !empty($person['website']) ? $person['website'] : '';
?>
<section class="person-page">
    <div class="person-container">
        <header class="person-header">
            <div class="person-header-inner">
                <div class="person-avatar">
                    <?php if (!empty($person['avatar'])): ?>
                        <img src="<?php echo h($person['avatar']); ?>" alt="<?php echo h($person['name']); ?>" loading="eager">
                    <?php else: ?>
                        <div class="person-avatar-initial"><?php echo h(mb_substr($person['name'], 0, 1, 'UTF-8')); ?></div>
                    <?php endif; ?>
                </div>
                <div class="person-meta">
                    <h1 class="person-name"><?php echo h($person['name']); ?></h1>
                    <?php if (!empty($person['bio'])): ?>
                        <p class="person-bio"><?php echo nl2br(h($person['bio'])); ?></p>
                    <?php endif; ?>
                    <div class="person-links">
                        <?php if (!empty($person['instagram'])): ?>
                            <a href="https://instagram.com/<?php echo h(ltrim($person['instagram'], '@')); ?>" target="_blank" rel="noopener me" class="person-link">
                                <i class="fab fa-instagram"></i>
                                <span><?php echo h($person['instagram']); ?></span>
                            </a>
                        <?php endif; ?>
                        <?php if (!empty($person['website'])): ?>
                            <a href="<?php echo h($person['website']); ?>" target="_blank" rel="noopener" class="person-link">
                                <i class="fas fa-globe"></i>
                                <span><?php echo h($websiteLabel); ?></span>
                            </a>
                        <?php endif; ?>
                    </div>
                    <div class="person-stats">
                        <span class="stat"><strong><?php echo count($images); ?></strong> <?php echo h($photoLabel); ?></span>
                    </div>
                    <div class="share-bar">
                        <span class="share-label">اشتراک‌گذاری:</span>
                        <?php
                        $shareUrl = urlencode((isset($_SERVER['HTTPS']) ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
                        $shareText = urlencode('گالری عکاسی ' . $person['name'] . ' در Mirohood');
                        ?>
                        <a href="https://wa.me/?text=<?php echo $shareText; ?>%20<?php echo $shareUrl; ?>" target="_blank" rel="noopener" class="share-btn whatsapp" aria-label="WhatsApp"><i class="fab fa-whatsapp"></i></a>
                        <a href="https://t.me/share/url?url=<?php echo $shareUrl; ?>&text=<?php echo $shareText; ?>" target="_blank" rel="noopener" class="share-btn telegram" aria-label="Telegram"><i class="fab fa-telegram-plane"></i></a>
                        <a href="https://twitter.com/intent/tweet?url=<?php echo $shareUrl; ?>&text=<?php echo $shareText; ?>" target="_blank" rel="noopener" class="share-btn twitter" aria-label="Twitter"><i class="fab fa-twitter"></i></a>
                        <button type="button" class="share-btn copy" data-url="<?php echo h((isset($_SERVER['HTTPS']) ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH)); ?>" aria-label="کپی لینک"><i class="fas fa-link"></i></button>
                        <button type="button" class="share-btn native-share" data-url="<?php echo h((isset($_SERVER['HTTPS']) ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH)); ?>" data-title="<?php echo h($person['name']); ?> — گالری عکاسی Mirohood" data-text="گالری عکاسی حرفه‌ای در Mirohood" aria-label="اشتراک‌گذاری" style="display:none;"><i class="fas fa-share-alt"></i></button>
                    </div>
                    <?php if (!empty($personInstagram) || !empty($personWebsite)): ?>
                        <div class="person-seo-meta" style="margin-top:0.8rem;font-size:0.75rem;color:#8a8580;">
                            <?php if (!empty($personInstagram)): ?>
                                <span>Instagram: @<?php echo h($personInstagram); ?></span>
                            <?php endif; ?>
                            <?php if (!empty($personInstagram) && !empty($personWebsite)): ?>
                                <span style="margin:0 0.5rem;">|</span>
                            <?php endif; ?>
                            <?php if (!empty($personWebsite)): ?>
                                <span>Website: <?php echo h($personWebsite); ?></span>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            <a href="<?php echo url('gallery'); ?>" class="back-link">
                <i class="fas fa-arrow-right"></i>
                <span><?php echo h($backLabel); ?></span>
            </a>
        </header>

        <?php if (!empty($images)): ?>
            <div class="photo-grid">
                <?php foreach ($images as $index => $img): ?>
                    <?php
                    // تصویر اصلی تنها پس از کلیک در lightbox لود می‌شود؛
                    // بنابراین شبکهٔ گالری همچنان سریع و کم‌حجم باقی می‌ماند.
                    $fullImage = $img['image'] ?? ($img['large'] ?? ($img['medium'] ?? ''));
                    $imageAlt = $img['alt'] ?? $person['name'];
                    $imageCaption = $img['caption'] ?? '';
                    ?>
                    <figure class="photo-item"
                            data-index="<?php echo (int) $index; ?>"
                            data-src="<?php echo h($fullImage); ?>"
                            data-caption="<?php echo h($imageCaption); ?>"
                            data-alt="<?php echo h($imageAlt); ?>"
                            role="button"
                            tabindex="0"
                            aria-label="نمایش تمام‌صفحهٔ <?php echo h($imageAlt); ?>">
                        <?php echo responsive_image($img, 'large', $imageAlt, '', true); ?>
                    </figure>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="gallery-empty">
                <p><?php echo h($emptyText); ?></p>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php if (!empty($images)): ?>
    <!-- نمایشگر تمام‌صفحهٔ تصاویر گالری شخص -->
    <div id="photo-lightbox" class="photo-lightbox" aria-hidden="true" aria-label="نمایش تمام‌صفحهٔ تصاویر" role="dialog" aria-modal="true" tabindex="-1">
        <div class="lightbox-backdrop"></div>
        <button type="button" class="lightbox-close" aria-label="بستن نمایش تصویر"><i class="fas fa-times" aria-hidden="true"></i></button>
        <button type="button" class="lightbox-prev" aria-label="تصویر قبلی"><i class="fas fa-chevron-right" aria-hidden="true"></i></button>
        <button type="button" class="lightbox-next" aria-label="تصویر بعدی"><i class="fas fa-chevron-left" aria-hidden="true"></i></button>
        <div class="lightbox-stage">
            <img id="lightbox-img" src="" alt="" loading="eager">
        </div>
        <div class="lightbox-caption" aria-live="polite"></div>
    </div>
<?php endif; ?>

<script>
(function() {
    document.querySelectorAll('.share-btn.copy').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var url = this.dataset.url;
            if (navigator.clipboard) {
                navigator.clipboard.writeText(url).then(function() {
                    if (typeof showToast === 'function') showToast('لینک کپی شد', 'success');
                    else alert('لینک کپی شد');
                }).catch(function() { fallbackCopy(url); });
            } else {
                fallbackCopy(url);
            }
        });
    });

    function fallbackCopy(text) {
        var ta = document.createElement('textarea');
        ta.value = text;
        document.body.appendChild(ta);
        ta.select();
        document.execCommand('copy');
        document.body.removeChild(ta);
        if (typeof showToast === 'function') showToast('لینک کپی شد', 'success');
        else alert('لینک کپی شد');
    }

    if (navigator.share) {
        document.querySelectorAll('.share-btn.native-share').forEach(function(btn) {
            btn.style.display = 'inline-flex';
            btn.addEventListener('click', function() {
                navigator.share({
                    title: this.dataset.title,
                    text: this.dataset.text,
                    url: this.dataset.url
                }).catch(function() {});
            });
        });
    }
})();
</script>
