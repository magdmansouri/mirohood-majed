<?php
// views/client-gallery/shared.php - Public shared client gallery view
$gallery = $gallery ?? [];
$images = $images ?? [];
$share_url = $share_url ?? '';
?>
<style>
    .shared-gallery-wrap { max-width: 1100px; margin: 0 auto; padding: 4rem 1.5rem; }
    .shared-gallery-header {
        margin-bottom: 2.5rem;
        padding-bottom: 1.5rem;
        border-bottom: 1px solid rgba(200,168,98,0.08);
    }
    .shared-gallery-header h1 {
        font-family: 'Cormorant Garamond', Georgia, serif;
        font-size: 2rem; font-weight: 300; color: #f4f1ea; margin-bottom: 0.5rem;
    }
    .shared-gallery-header p { color: #8a8580; font-size: 0.85rem; }
    .shared-gallery-actions { margin-top: 1.5rem; display: flex; gap: 0.75rem; flex-wrap: wrap; }
    .shared-gallery-actions a {
        display: inline-flex; align-items: center; gap: 0.5rem;
        padding: 0.6rem 1.4rem; border-radius: 9999px;
        font-size: 0.8rem; text-decoration: none; transition: all 0.3s ease;
    }
    .shared-gallery-actions .download-all {
        display:inline-flex; align-items:center; gap:0.5rem; padding:0.6rem 1.4rem; border-radius:9999px;
        background: linear-gradient(135deg, #c8a862, #a8893a); color: #0a0908; font-weight: 600;
        border: 1px solid #c8a862;
    }
    .shared-gallery-actions .download-all:hover { transform: translateY(-2px); box-shadow: 0 8px 25px rgba(200,168,98,0.25); }
    .shared-download-menu { position: relative; }
    .shared-download-menu summary { list-style: none; cursor: pointer; }
    .shared-download-menu summary::-webkit-details-marker { display: none; }
    .shared-download-options { position:absolute;z-index:30;top:calc(100% + 0.55rem);right:0;min-width:210px;padding:0.45rem;border:1px solid rgba(200,168,98,0.22);border-radius:0.85rem;background:#1a1715;box-shadow:0 14px 35px rgba(0,0,0,0.4); }
    .shared-download-options a { display:flex;flex-direction:column;gap:0.1rem;padding:0.6rem 0.7rem;border-radius:0.55rem;color:#f4f1ea;text-decoration:none;font-size:0.74rem; }
    .shared-download-options a:hover { background:rgba(200,168,98,0.12);color:#c8a862; }
    .shared-download-options small { color:#8a8580;font-size:0.62rem; }
    .shared-gallery-actions .copy-link,
    .shared-gallery-actions .share-btn {
        display: inline-flex; align-items: center; gap: 0.5rem;
        padding: 0.6rem 1.4rem; border-radius: 9999px;
        font-size: 0.8rem; text-decoration: none; transition: all 0.3s ease;
        border: 1px solid rgba(200,168,98,0.2); color: #f4f1ea;
        background: transparent; cursor: pointer;
    }
    .shared-gallery-actions .copy-link:hover,
    .shared-gallery-actions .share-btn:hover { border-color: #c8a862; color: #c8a862; transform: translateY(-2px); }
    .shared-gallery-actions .share-btn.whatsapp:hover { background: rgba(37,211,102,0.1); border-color: #25d366; color: #25d366; }
    .shared-gallery-actions .share-btn.telegram:hover { background: rgba(0,136,204,0.1); border-color: #08c; color: #08c; }
    .shared-gallery-actions .share-btn.twitter:hover { background: rgba(29,161,242,0.1); border-color: #1da1f2; color: #1da1f2; }
    @media (max-width: 640px) {
        .shared-gallery-actions { justify-content: center; }
        .shared-gallery-actions a, .shared-gallery-actions button, .shared-download-menu summary { flex: 1; justify-content: center; min-width: 120px; }
        .shared-download-options { right:auto; left:0; }
    }

    .client-photo-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 1rem;
    }
    .client-photo-item {
        aspect-ratio: 1;
        border-radius: 0.8rem;
        overflow: hidden;
        background: rgba(255,255,255,0.03);
        border: 1px solid rgba(200,168,98,0.08);
        cursor: pointer;
        transition: all 0.3s ease;
        position: relative;
    }
    .client-photo-item:hover {
        border-color: rgba(200,168,98,0.2);
        transform: translateY(-4px);
    }
    .client-photo-item img {
        width: 100%; height: 100%; object-fit: cover; display: block;
    }
    .client-photo-caption {
        position: absolute; bottom: 0; left: 0; right: 0;
        padding: 0.5rem 0.8rem;
        font-size: 0.75rem; color: rgba(255,255,255,0.85);
        background: linear-gradient(180deg, transparent, rgba(0,0,0,0.7));
        white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
    }
    .photo-actions {
        position: absolute; top: 0.6rem; right: 0.6rem;
        display: flex; gap: 0.4rem; opacity: 0;
        transition: opacity 0.3s ease;
    }
    .client-photo-item:hover .photo-actions { opacity: 1; }
    .action-btn {
        width: 34px; height: 34px; border-radius: 50%;
        background: rgba(10,9,8,0.7); border: 1px solid rgba(255,255,255,0.1);
        color: #f4f1ea; display: flex; align-items: center; justify-content: center;
        cursor: pointer; text-decoration: none; transition: all 0.3s ease; font-size: 0.85rem;
        backdrop-filter: blur(4px);
    }
    .action-btn:hover { background: rgba(200,168,98,0.2); border-color: #c8a862; color: #c8a862; }

    .empty-gallery {
        text-align: center; padding: 4rem 1.5rem;
        background: rgba(255,255,255,0.03); border: 1px solid rgba(200,168,98,0.08);
        border-radius: 1rem;
    }

    /* Lightbox */
    .lightbox {
        position: fixed; inset: 0; z-index: 1000;
        background: rgba(10,9,8,0.95); backdrop-filter: blur(10px);
        display: none; align-items: center; justify-content: center;
    }
    .lightbox.active { display: flex; }
    .lightbox-close {
        position: absolute; top: 1.5rem; right: 1.5rem;
        background: none; border: none; color: #f4f1ea; font-size: 1.5rem; cursor: pointer;
    }
    .lightbox-nav {
        position: absolute; top: 50%; transform: translateY(-50%);
        background: rgba(255,255,255,0.05); border: 1px solid rgba(200,168,98,0.15);
        color: #f4f1ea; width: 44px; height: 44px; border-radius: 50%;
        display: flex; align-items: center; justify-content: center; cursor: pointer;
        transition: all 0.3s ease; font-size: 1.2rem;
    }
    .lightbox-nav:hover { background: rgba(200,168,98,0.1); }
    .lightbox-prev { left: 1.5rem; }
    .lightbox-next { right: 1.5rem; }
    .lightbox-img {
        max-width: 90vw; max-height: 85vh; object-fit: contain;
        border-radius: 0.5rem; box-shadow: 0 20px 60px rgba(0,0,0,0.5);
    }
    .lightbox-caption {
        position: absolute; bottom: 1.5rem; left: 50%; transform: translateX(-50%);
        color: #f4f1ea; font-size: 0.9rem; text-align: center;
        background: rgba(0,0,0,0.4); padding: 0.5rem 1rem; border-radius: 9999px;
    }
    .lightbox-download {
        position: absolute; bottom: 1.5rem; right: 1.5rem;
        padding: 0.6rem 1.2rem; border-radius: 9999px;
        background: linear-gradient(135deg, #c8a862, #a8893a); color: #0a0908;
        font-size: 0.8rem; font-weight: 600; text-decoration: none;
        display: inline-flex; align-items: center; gap: 0.5rem;
        transition: all 0.3s ease;
    }
    .lightbox-download:hover { transform: translateY(-2px); box-shadow: 0 8px 25px rgba(200,168,98,0.25); }

    @media (max-width: 900px) { .client-photo-grid { grid-template-columns: repeat(3, 1fr); } }
    @media (max-width: 600px) { .client-photo-grid { grid-template-columns: repeat(2, 1fr); gap: 0.75rem; } }
</style>

<div class="shared-gallery-wrap">
    <div class="shared-gallery-header">
        <h1><?php echo h($gallery['title']); ?></h1>
        <p>گالری اختصاصی <?php echo h($gallery['user_name']); ?> در Mirohood</p>
        <?php if (!empty($gallery['description'])): ?>
            <p style="margin-top:0.75rem;color:#f4f1ea;font-size:0.9rem;"><?php echo h($gallery['description']); ?></p>
        <?php endif; ?>
        <div class="shared-gallery-actions">
            <details class="shared-download-menu">
                <summary class="download-all"><i class="fas fa-download"></i> دانلود ZIP <i class="fas fa-chevron-down" style="font-size:0.6rem;"></i></summary>
                <div class="shared-download-options">
                    <a href="<?php echo url('gallery/share/' . $gallery['share_token'] . '/download?quality=original'); ?>">
                        <span><i class="fas fa-image"></i> کیفیت اصلی</span>
                        <small>JPEG اصلی تا 2400px — حجم بیشتر</small>
                    </a>
                    <a href="<?php echo url('gallery/share/' . $gallery['share_token'] . '/download?quality=optimized'); ?>">
                        <span><i class="fas fa-feather-alt"></i> حجم کمتر</span>
                        <small>نسخه 1200px WebP/JPEG — دانلود سریع‌تر</small>
                    </a>
                </div>
            </details>
            <a href="https://wa.me/?text=<?php echo urlencode('گالری عکس‌های ' . $gallery['title'] . ' در Mirohood'); ?>%20<?php echo urlencode($share_url); ?>" target="_blank" rel="noopener" class="share-btn whatsapp">
                <i class="fab fa-whatsapp"></i> واتساپ
            </a>
            <a href="https://t.me/share/url?url=<?php echo urlencode($share_url); ?>&text=<?php echo urlencode('گالری عکس‌های ' . $gallery['title'] . ' در Mirohood'); ?>" target="_blank" rel="noopener" class="share-btn telegram">
                <i class="fab fa-telegram-plane"></i> تلگرام
            </a>
            <a href="https://twitter.com/intent/tweet?url=<?php echo urlencode($share_url); ?>&text=<?php echo urlencode('گالری عکس‌های ' . $gallery['title'] . ' در Mirohood'); ?>" target="_blank" rel="noopener" class="share-btn twitter">
                <i class="fab fa-twitter"></i> توییتر
            </a>
            <button type="button" class="copy-link" data-url="<?php echo h($share_url); ?>">
                <i class="fas fa-link"></i> کپی لینک
            </button>
            <button type="button" class="share-btn native-share" data-url="<?php echo h($share_url); ?>" data-title="<?php echo h($gallery['title']); ?> — گالری Mirohood" data-text="گالری عکس‌های حرفه‌ای در Mirohood" style="display:none;">
                <i class="fas fa-share-alt"></i> اشتراک
            </button>
        </div>
    </div>

    <?php if (!empty($images)): ?>
        <div class="client-photo-grid" data-gallery="<?php echo $gallery['id']; ?>">
            <?php foreach ($images as $index => $img): ?>
                <div class="client-photo-item" data-index="<?php echo $index; ?>" data-full="<?php echo h($img['image']); ?>" data-caption="<?php echo h($img['caption'] ?? ''); ?>" data-id="<?php echo $img['id']; ?>">
                    <img src="<?php echo h($img['thumbnail'] ?: $img['image']); ?>" alt="<?php echo h($img['caption'] ?? ''); ?>">
                    <?php if (!empty($img['caption'])): ?>
                        <div class="client-photo-caption"><?php echo h($img['caption']); ?></div>
                    <?php endif; ?>
                    <div class="photo-actions">
                        <a href="<?php echo h($img['image']); ?>" class="action-btn download-btn" download title="دانلود با کیفیت اصلی">
                            <i class="fas fa-download"></i>
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="empty-gallery">
            <i class="fas fa-images" style="font-size:3rem;color:#c8a862;margin-bottom:1rem;opacity:0.6;"></i>
            <h3 style="font-family:'Cormorant Garamond',Georgia,serif;font-size:1.5rem;color:#f4f1ea;margin-bottom:0.5rem;font-weight:300;">تصویری در این گالری نیست</h3>
            <p style="color:#8a8580;">هنوز عکسی در این گالری قرار نگرفته است.</p>
        </div>
    <?php endif; ?>
</div>

<div class="lightbox" id="lightbox">
    <button class="lightbox-close" id="lightboxClose"><i class="fas fa-times"></i></button>
    <button class="lightbox-nav lightbox-prev" id="lightboxPrev"><i class="fas fa-chevron-left"></i></button>
    <button class="lightbox-nav lightbox-next" id="lightboxNext"><i class="fas fa-chevron-right"></i></button>
    <img src="" alt="" class="lightbox-img" id="lightboxImg">
    <div class="lightbox-caption" id="lightboxCaption"></div>
    <a href="" class="lightbox-download" id="lightboxDownload" download><i class="fas fa-download"></i> دانلود</a>
</div>

<script>
(function() {
    var lightbox = document.getElementById('lightbox');
    var lightboxImg = document.getElementById('lightboxImg');
    var lightboxCaption = document.getElementById('lightboxCaption');
    var lightboxDownload = document.getElementById('lightboxDownload');
    var currentImages = [];
    var currentIndex = 0;

    function openLightbox(images, index) {
        currentImages = images;
        currentIndex = index;
        updateLightbox();
        lightbox.classList.add('active');
        document.body.style.overflow = 'hidden';
    }

    function closeLightbox() {
        lightbox.classList.remove('active');
        document.body.style.overflow = '';
    }

    function updateLightbox() {
        var img = currentImages[currentIndex];
        lightboxImg.src = img.full;
        lightboxCaption.textContent = img.caption || '';
        lightboxCaption.style.display = img.caption ? 'block' : 'none';
        lightboxDownload.href = img.full;
        lightboxDownload.setAttribute('download', 'mirohood-' + img.id + '.jpg');
    }

    var grid = document.querySelector('.client-photo-grid');
    if (grid) {
        var images = [];
        grid.querySelectorAll('.client-photo-item').forEach(function(item, index) {
            images.push({
                id: item.dataset.id,
                full: item.dataset.full,
                caption: item.dataset.caption
            });
            item.addEventListener('click', function(e) {
                if (e.target.closest('.action-btn')) return;
                openLightbox(images, index);
            });
        });
    }

    document.querySelectorAll('.copy-link').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var url = this.dataset.url;
            if (navigator.clipboard) {
                navigator.clipboard.writeText(url).then(function() {
                    showToast ? showToast('لینک گالری کپی شد', 'success') : alert('لینک کپی شد');
                }).catch(function() {
                    fallbackCopy(url);
                });
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
        showToast ? showToast('لینک گالری کپی شد', 'success') : alert('لینک کپی شد');
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

    document.getElementById('lightboxClose').addEventListener('click', closeLightbox);
    document.getElementById('lightboxPrev').addEventListener('click', function(e) {
        e.stopPropagation();
        currentIndex = (currentIndex - 1 + currentImages.length) % currentImages.length;
        updateLightbox();
    });
    document.getElementById('lightboxNext').addEventListener('click', function(e) {
        e.stopPropagation();
        currentIndex = (currentIndex + 1) % currentImages.length;
        updateLightbox();
    });
    lightbox.addEventListener('click', function(e) {
        if (e.target === lightbox) closeLightbox();
    });
    document.addEventListener('keydown', function(e) {
        if (!lightbox.classList.contains('active')) return;
        if (e.key === 'Escape') closeLightbox();
        if (e.key === 'ArrowLeft') document.getElementById('lightboxPrev').click();
        if (e.key === 'ArrowRight') document.getElementById('lightboxNext').click();
    });
})();
</script>
