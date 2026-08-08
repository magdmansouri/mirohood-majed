<?php
// views/auth/my-gallery.php - User Private Gallery with selection mode
$user = $user ?? [];
$galleries = $galleries ?? [];
?>
<style>
    .mg-wrap { max-width: 1300px; margin: 0 auto; padding: 4rem 1.25rem 6rem; contain: layout style; }
    .mg-header {
        display: flex; justify-content: space-between; align-items: flex-end;
        flex-wrap: wrap; gap: 1.5rem;
        margin-bottom: 2.5rem; padding-bottom: 1.5rem;
        border-bottom: 1px solid rgba(200,168,98,0.08);
    }
    .mg-header h1 {
        font-family: 'Cormorant Garamond', Georgia, serif;
        font-size: 2.4rem; font-weight: 300; color: #f4f1ea; margin-bottom: 0.4rem;
    }
    .mg-header p { color: #8a8580; font-size: 0.9rem; }
    .mg-back {
        display: inline-flex; align-items: center; gap: 0.5rem;
        padding: 0.6rem 1.4rem; border: 1px solid rgba(200,168,98,0.2);
        border-radius: 9999px; color: #f4f1ea; font-size: 0.8rem;
        text-decoration: none; transition: all 0.3s ease;
    }
    .mg-back:hover { border-color: #c8a862; color: #c8a862; }

    .mg-section { margin-bottom: 3.5rem; contain: layout style; }
    .mg-section-header {
        display: flex; justify-content: space-between; align-items: flex-start;
        flex-wrap: wrap; gap: 1rem; margin-bottom: 1.25rem;
    }
    .mg-section-title {
        font-family: 'Cormorant Garamond', Georgia, serif;
        font-size: 1.6rem; font-weight: 300; color: #f4f1ea; margin-bottom: 0.3rem;
    }
    .mg-section-desc { color: #8a8580; font-size: 0.85rem; }
    .mg-section-stats {
        display: flex; gap: 0.75rem; align-items: center; flex-wrap: wrap;
    }
    .mg-stat {
        display: inline-flex; align-items: center; gap: 0.35rem;
        padding: 0.35rem 0.9rem; background: rgba(255,255,255,0.03);
        border: 1px solid rgba(200,168,98,0.08); border-radius: 9999px;
        color: #8a8580; font-size: 0.75rem;
    }
    .mg-stat i { color: #c8a862; }
    .mg-actions { display: flex; gap: 0.5rem; flex-wrap: wrap; }
    .mg-btn {
        display: inline-flex; align-items: center; gap: 0.4rem;
        padding: 0.4rem 1rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 500;
        background: transparent; border: 1px solid rgba(200,168,98,0.2);
        color: #8a8580; cursor: pointer; transition: all 0.3s ease;
    }
    .mg-btn:hover { border-color: #c8a862; color: #c8a862; }
    .mg-btn.active { background: rgba(200,168,98,0.1); border-color: #c8a862; color: #c8a862; }
    .mg-btn.download {
        background: linear-gradient(135deg, #c8a862, #a8893a);
        border-color: #c8a862; color: #0a0908; font-weight: 600;
        text-decoration: none;
    }
    .mg-btn.download:hover { transform: translateY(-2px); box-shadow: 0 8px 25px rgba(200,168,98,0.25); }
    .mg-download-menu { position: relative; }
    .mg-download-menu summary { list-style: none; }
    .mg-download-menu summary::-webkit-details-marker { display: none; }
    .mg-download-menu[open] summary { border-color: #c8a862; color: #0a0908; }
    .mg-download-options {
        position: absolute; z-index: 25; top: calc(100% + 0.55rem); left: 0; min-width: 205px;
        padding: 0.45rem; border: 1px solid rgba(200,168,98,0.22); border-radius: 0.85rem;
        background: #1a1715; box-shadow: 0 14px 35px rgba(0,0,0,0.4);
    }
    .mg-download-options a { display: flex; flex-direction: column; gap: 0.1rem; padding: 0.6rem 0.7rem; border-radius: 0.55rem; color: #f4f1ea; text-decoration: none; font-size: 0.74rem; }
    .mg-download-options a:hover { background: rgba(200,168,98,0.12); color: #c8a862; }
    .mg-download-options small { color: #8a8580; font-size: 0.62rem; }
    .mg-btn.confirm {
        background: linear-gradient(135deg, #22c55e, #16a34a);
        border-color: #22c55e; color: #0a0908; font-weight: 600;
    }
    .mg-btn.confirm:hover { transform: translateY(-2px); box-shadow: 0 8px 25px rgba(34,197,94,0.25); }
    .mg-btn:disabled { opacity: 0.5; cursor: not-allowed; transform: none !important; }

    .mg-selection-bar {
        display: none; align-items: center; justify-content: space-between; gap: 1rem;
        padding: 0.75rem 1rem; margin-bottom: 1rem;
        background: rgba(200,168,98,0.08); border: 1px solid rgba(200,168,98,0.15); border-radius: 0.75rem;
    }
    .mg-selection-bar.active { display: flex; }
    .mg-selection-bar span { color: #f4f1ea; font-size: 0.85rem; }
    .mg-selection-bar .count { color: #c8a862; font-weight: 600; }

    .mg-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 1rem;
    }
    .mg-item {
        aspect-ratio: 1;
        border-radius: 0.8rem;
        overflow: hidden;
        background: rgba(255,255,255,0.03);
        border: 1px solid rgba(200,168,98,0.08);
        cursor: pointer;
        transition: all 0.3s ease;
        position: relative;
    }
    .mg-item:hover {
        border-color: rgba(200,168,98,0.25);
        transform: translateY(-5px);
        box-shadow: 0 12px 30px rgba(0,0,0,0.35);
    }
    .mg-item img {
        width: 100%; height: 100%; object-fit: cover; display: block;
        transition: transform 0.5s ease;
    }
    .mg-item:hover img { transform: scale(1.05); }
    .mg-item.favorite { border-color: rgba(200,168,98,0.3); box-shadow: 0 0 20px rgba(200,168,98,0.1); }
    .mg-item.selected { border-color: #22c55e; box-shadow: 0 0 0 2px rgba(34,197,94,0.3); }
    .mg-item.selected::after {
        content: '✓'; position: absolute; top: 0.6rem; left: 0.6rem;
        width: 26px; height: 26px; border-radius: 50%;
        background: #22c55e; color: #0a0908; font-size: 0.75rem; font-weight: 700;
        display: flex; align-items: center; justify-content: center;
    }
    .mg-item .caption {
        position: absolute; bottom: 0; left: 0; right: 0;
        padding: 0.6rem 0.9rem;
        font-size: 0.75rem; color: rgba(255,255,255,0.9);
        background: linear-gradient(180deg, transparent, rgba(0,0,0,0.7));
        white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
    }
    .mg-item-actions {
        position: absolute; top: 0.7rem; right: 0.7rem;
        display: flex; gap: 0.45rem; opacity: 0;
        transition: opacity 0.3s ease, transform 0.3s ease;
        transform: translateY(-5px);
    }
    .mg-item:hover .mg-item-actions { opacity: 1; transform: translateY(0); }
    .mg-item.selected .mg-item-actions { right: 2.6rem; }
    .mg-action-btn {
        width: 36px; height: 36px; border-radius: 50%;
        background: rgba(10,9,8,0.75); border: 1px solid rgba(255,255,255,0.1);
        color: #f4f1ea; display: flex; align-items: center; justify-content: center;
        cursor: pointer; text-decoration: none; transition: all 0.3s ease; font-size: 0.85rem;
        backdrop-filter: blur(4px);
    }
    .mg-action-btn:hover { background: rgba(200,168,98,0.2); border-color: #c8a862; color: #c8a862; }
    .mg-action-btn.fav.active { background: rgba(239,68,68,0.2); border-color: #f87171; color: #f87171; }
    .mg-action-btn.select.active { background: rgba(34,197,94,0.2); border-color: #22c55e; color: #22c55e; }

    .mg-empty {
        text-align: center; padding: 5rem 1.5rem;
        background: rgba(255,255,255,0.03); border: 1px solid rgba(200,168,98,0.08);
        border-radius: 1rem;
    }
    .mg-empty i { font-size: 3.5rem; color: #c8a862; margin-bottom: 1.25rem; opacity: 0.6; }
    .mg-empty h3 { font-family: 'Cormorant Garamond', Georgia, serif; font-size: 1.7rem; color: #f4f1ea; margin-bottom: 0.5rem; font-weight: 300; }
    .mg-empty p { color: #8a8580; font-size: 0.95rem; }

    /* Lightbox */
    .lb {
        position: fixed; inset: 0; z-index: 1000;
        background: rgba(10,9,8,0.96); backdrop-filter: blur(12px);
        display: none; align-items: center; justify-content: center;
    }
    .lb.active { display: flex; }
    .lb-close {
        position: absolute; top: 1.5rem; right: 1.5rem;
        background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1);
        color: #f4f1ea; width: 44px; height: 44px; border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.2rem; cursor: pointer; transition: all 0.3s ease;
    }
    .lb-close:hover { background: rgba(200,168,98,0.15); border-color: #c8a862; }
    .lb-nav {
        position: absolute; top: 50%; transform: translateY(-50%);
        background: rgba(255,255,255,0.05); border: 1px solid rgba(200,168,98,0.15);
        color: #f4f1ea; width: 48px; height: 48px; border-radius: 50%;
        display: flex; align-items: center; justify-content: center; cursor: pointer;
        transition: all 0.3s ease; font-size: 1.2rem;
    }
    .lb-nav:hover { background: rgba(200,168,98,0.15); border-color: #c8a862; }
    .lb-prev { left: 1.5rem; }
    .lb-next { right: 1.5rem; }
    .lb-img {
        max-width: 90vw; max-height: 84vh; object-fit: contain;
        border-radius: 0.5rem; box-shadow: 0 24px 70px rgba(0,0,0,0.6);
    }
    .lb-caption {
        position: absolute; bottom: 1.5rem; left: 50%; transform: translateX(-50%);
        color: #f4f1ea; font-size: 0.9rem; text-align: center;
        background: rgba(0,0,0,0.45); padding: 0.6rem 1.2rem; border-radius: 9999px;
    }
    .lb-fav {
        position: absolute; bottom: 1.5rem; left: 1.5rem;
        width: 48px; height: 48px; border-radius: 50%;
        background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1);
        color: #f4f1ea; font-size: 1.2rem; cursor: pointer; transition: all 0.3s ease;
        display: flex; align-items: center; justify-content: center;
    }
    .lb-fav:hover { background: rgba(239,68,68,0.2); border-color: #f87171; color: #f87171; }
    .lb-fav.active { background: rgba(239,68,68,0.2); border-color: #f87171; color: #f87171; }
    .lb-select {
        position: absolute; bottom: 1.5rem; left: 5.5rem;
        padding: 0.65rem 1.4rem; border-radius: 9999px;
        background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1);
        color: #f4f1ea; font-size: 0.8rem; font-weight: 600; cursor: pointer;
        display: inline-flex; align-items: center; gap: 0.5rem; transition: all 0.3s ease;
    }
    .lb-select:hover { background: rgba(34,197,94,0.15); border-color: #22c55e; color: #22c55e; }
    .lb-select.active { background: rgba(34,197,94,0.2); border-color: #22c55e; color: #22c55e; }
    .lb-download {
        position: absolute; bottom: 1.5rem; right: 1.5rem;
        padding: 0.65rem 1.4rem; border-radius: 9999px;
        background: linear-gradient(135deg, #c8a862, #a8893a); color: #0a0908;
        font-size: 0.8rem; font-weight: 600; text-decoration: none;
        display: inline-flex; align-items: center; gap: 0.5rem;
        transition: all 0.3s ease;
    }
    .lb-download:hover { transform: translateY(-2px); box-shadow: 0 8px 25px rgba(200,168,98,0.25); }
    .lb-counter {
        position: absolute; top: 1.5rem; left: 1.5rem;
        color: #8a8580; font-size: 0.75rem;
        background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.08);
        padding: 0.4rem 0.9rem; border-radius: 9999px;
    }

    @media (max-width: 1024px) { .mg-grid { grid-template-columns: repeat(3, 1fr); } }
    @media (max-width: 640px) { .mg-grid { grid-template-columns: repeat(2, 1fr); gap: 0.75rem; } }
</style>

<div class="mg-wrap">
    <div class="mg-header">
        <div>
            <h1>گالری من</h1>
            <p><?php echo h($user['name']); ?> — عکس‌های اختصاصی شما</p>
        </div>
        <a href="<?php echo url('dashboard'); ?>" class="mg-back"><i class="fas fa-arrow-left"></i> بازگشت به داشبورد</a>
    </div>

    <?php if (!empty($galleries)): ?>
        <?php foreach ($galleries as $gallery): ?>
            <?php
            $maxSelections = (int) ($gallery['max_selections'] ?? 0);
            $selectionMode = $maxSelections > 0;
            ?>
            <div class="mg-section" data-gallery="<?php echo $gallery['id']; ?>" data-max-selections="<?php echo $maxSelections; ?>">
                <div class="mg-section-header">
                    <div>
                        <div class="mg-section-title"><?php echo h($gallery['title']); ?></div>
                        <?php if (!empty($gallery['description'])): ?>
                            <div class="mg-section-desc"><?php echo h($gallery['description']); ?></div>
                        <?php endif; ?>
                    </div>
                    <div class="mg-section-stats">
                        <span class="mg-stat"><i class="fas fa-images"></i> <?php echo count($gallery['images']); ?> عکس</span>
                        <span class="mg-stat"><i class="fas fa-heart"></i> <?php echo (int) ($gallery['favorite_count'] ?? 0); ?> علاقه‌مندی</span>
                        <?php if ($selectionMode): ?>
                            <span class="mg-stat"><i class="fas fa-check-circle"></i> <?php echo (int) ($gallery['final_count'] ?? 0); ?> / <?php echo $maxSelections; ?> انتخاب نهایی</span>
                        <?php endif; ?>
                        <div class="mg-actions">
                            <button type="button" class="mg-btn active" data-gallery="<?php echo $gallery['id']; ?>" data-filter="all">همه</button>
                            <button type="button" class="mg-btn" data-gallery="<?php echo $gallery['id']; ?>" data-filter="favorites">⭐ علاقه‌مندی‌ها</button>
                            <?php if ($selectionMode): ?>
                                <button type="button" class="mg-btn" data-gallery="<?php echo $gallery['id']; ?>" data-selection-mode="1">انتخاب نهایی</button>
                                <button type="button" class="mg-btn confirm" data-gallery="<?php echo $gallery['id']; ?>" data-confirm-selection="1" style="display:none;">تأیید انتخاب</button>
                            <?php endif; ?>
                            <details class="mg-download-menu">
                                <summary class="mg-btn download"><i class="fas fa-download"></i> دانلود ZIP <i class="fas fa-chevron-down" style="font-size:0.6rem;"></i></summary>
                                <div class="mg-download-options">
                                    <a href="<?php echo url('my-gallery/download?id=' . $gallery['id'] . '&quality=original'); ?>">
                                        <span><i class="fas fa-image"></i> کیفیت اصلی</span>
                                        <small>JPEG اصلی تا 2400px — حجم بیشتر</small>
                                    </a>
                                    <a href="<?php echo url('my-gallery/download?id=' . $gallery['id'] . '&quality=optimized'); ?>">
                                        <span><i class="fas fa-feather-alt"></i> حجم کمتر</span>
                                        <small>نسخه 1200px WebP/JPEG — دانلود سریع‌تر</small>
                                    </a>
                                </div>
                            </details>
                        </div>
                    </div>
                </div>

                <?php if ($selectionMode): ?>
                    <div class="mg-selection-bar" data-gallery="<?php echo $gallery['id']; ?>">
                        <span>انتخاب نهایی: <span class="count" data-gallery="<?php echo $gallery['id']; ?>"><?php echo (int) ($gallery['final_count'] ?? 0); ?></span> / <?php echo $maxSelections; ?> عکس</span>
                        <button type="button" class="mg-btn" onclick="clearSelection(<?php echo $gallery['id']; ?>)">پاک کردن</button>
                    </div>
                <?php endif; ?>

                <?php if (!empty($gallery['images'])): ?>
                    <div class="mg-grid" data-gallery="<?php echo $gallery['id']; ?>">
                        <?php foreach ($gallery['images'] as $index => $img): ?>
                            <div class="mg-item <?php echo $img['is_favorite'] ? 'favorite' : ''; ?> <?php echo $img['is_final_selection'] ? 'selected' : ''; ?>" data-index="<?php echo $index; ?>" data-gallery="<?php echo $gallery['id']; ?>" data-full="<?php echo h($img['image']); ?>" data-caption="<?php echo h($img['caption'] ?? '', ENT_QUOTES); ?>" data-id="<?php echo $img['id']; ?>">
                                <img src="<?php echo h($img['thumbnail'] ?: $img['image']); ?>" alt="<?php echo h($img['caption'] ?? ''); ?>" loading="lazy" decoding="async">
                                <?php if (!empty($img['caption'])): ?>
                                    <div class="caption"><?php echo h($img['caption']); ?></div>
                                <?php endif; ?>
                                <div class="mg-item-actions">
                                    <button type="button" class="mg-action-btn fav <?php echo $img['is_favorite'] ? 'active' : ''; ?>" data-id="<?php echo $img['id']; ?>" data-action="favorite" title="علاقه‌مندی">
                                        <i class="<?php echo $img['is_favorite'] ? 'fas' : 'far'; ?> fa-heart"></i>
                                    </button>
                                    <?php if ($selectionMode): ?>
                                        <button type="button" class="mg-action-btn select <?php echo $img['is_final_selection'] ? 'active' : ''; ?>" data-id="<?php echo $img['id']; ?>" data-action="select" title="انتخاب نهایی" style="display:none;">
                                            <i class="<?php echo $img['is_final_selection'] ? 'fas' : 'far'; ?> fa-check-circle"></i>
                                        </button>
                                    <?php endif; ?>
                                    <a href="<?php echo h($img['image']); ?>" class="mg-action-btn" download title="دانلود با کیفیت اصلی">
                                        <i class="fas fa-download"></i>
                                    </a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="mg-empty" style="padding:2.5rem;">
                        <p style="color:#8a8580;">تصویری در این گالری وجود ندارد.</p>
                    </div>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="mg-empty">
            <i class="fas fa-images"></i>
            <h3>گالری فعالی ندارید</h3>
            <p>وقتی عکس‌های شما آماده شود، استودیو آن‌ها را در اینجا قرار می‌دهد.</p>
        </div>
    <?php endif; ?>
</div>

<div class="lb" id="lb">
    <button class="lb-close" id="lbClose"><i class="fas fa-times"></i></button>
    <button class="lb-nav lb-prev" id="lbPrev"><i class="fas fa-chevron-left"></i></button>
    <button class="lb-nav lb-next" id="lbNext"><i class="fas fa-chevron-right"></i></button>
    <div class="lb-counter" id="lbCounter"></div>
    <img src="" alt="" class="lb-img" id="lbImg">
    <div class="lb-caption" id="lbCaption"></div>
    <button type="button" class="lb-fav" id="lbFav"><i class="far fa-heart"></i></button>
    <button type="button" class="lb-select" id="lbSelect"><i class="far fa-check-circle"></i> انتخاب نهایی</button>
    <a href="" class="lb-download" id="lbDownload" download><i class="fas fa-download"></i> دانلود</a>
</div>

<script>
(function() {
    var csrfToken = '<?php echo h($_SESSION['csrf_token'] ?? generate_csrf_token()); ?>';
    var lb = document.getElementById('lb');
    var lbImg = document.getElementById('lbImg');
    var lbCaption = document.getElementById('lbCaption');
    var lbFav = document.getElementById('lbFav');
    var lbSelect = document.getElementById('lbSelect');
    var lbDownload = document.getElementById('lbDownload');
    var lbCounter = document.getElementById('lbCounter');
    var currentImages = [];
    var currentIndex = 0;
    var currentGalleryId = null;
    var selectionModeGalleries = {};

    function openLb(images, index, galleryId) {
        currentImages = images;
        currentIndex = index;
        currentGalleryId = galleryId;
        updateLb();
        lb.classList.add('active');
        document.body.style.overflow = 'hidden';
    }

    function closeLb() {
        lb.classList.remove('active');
        document.body.style.overflow = '';
    }

    function updateLb() {
        var img = currentImages[currentIndex];
        lbImg.src = img.full;
        lbCaption.textContent = img.caption || '';
        lbCaption.style.display = img.caption ? 'block' : 'none';
        lbDownload.href = img.full;
        lbDownload.setAttribute('download', 'mirohood-' + img.id + '.jpg');
        lbFav.classList.toggle('active', img.is_favorite);
        lbFav.innerHTML = '<i class="' + (img.is_favorite ? 'fas' : 'far') + ' fa-heart"></i>';
        lbFav.dataset.id = img.id;

        var gallerySection = document.querySelector('.mg-section[data-gallery="' + currentGalleryId + '"]');
        var selectionMode = gallerySection && parseInt(gallerySection.dataset.maxSelections) > 0;
        lbSelect.style.display = selectionMode ? 'flex' : 'none';
        if (selectionMode) {
            lbSelect.classList.toggle('active', img.is_final_selection);
            lbSelect.innerHTML = '<i class="' + (img.is_final_selection ? 'fas' : 'far') + ' fa-check-circle"></i> ' + (img.is_final_selection ? 'حذف از انتخاب' : 'انتخاب نهایی');
            lbSelect.dataset.id = img.id;
        }
        lbCounter.textContent = (currentIndex + 1) + ' / ' + currentImages.length;
    }

    document.querySelectorAll('.mg-grid').forEach(function(grid) {
        var images = [];
        var galleryId = grid.dataset.gallery;
        grid.querySelectorAll('.mg-item').forEach(function(item, index) {
            images.push({
                id: item.dataset.id,
                full: item.dataset.full,
                caption: item.dataset.caption,
                is_favorite: item.classList.contains('favorite'),
                is_final_selection: item.classList.contains('selected')
            });
            item.addEventListener('click', function(e) {
                if (e.target.closest('.mg-item-actions') || e.target.closest('.mg-action-btn')) return;
                if (selectionModeGalleries[galleryId]) {
                    toggleSelect(item.dataset.id, item, null, galleryId);
                    return;
                }
                openLb(images, index, galleryId);
            });
        });
    });

    document.querySelectorAll('.mg-action-btn').forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            e.stopPropagation();
            var id = this.dataset.id;
            var action = this.dataset.action;
            var card = document.querySelector('.mg-item[data-id="' + id + '"]');
            var galleryId = card ? card.dataset.gallery : null;
            if (action === 'favorite') {
                toggleFavorite(id, this, card);
            } else if (action === 'select') {
                toggleSelect(id, card, this, galleryId);
            }
        });
    });

    lbFav.addEventListener('click', function() {
        var id = this.dataset.id;
        var card = document.querySelector('.mg-item[data-id="' + id + '"]');
        var gridBtn = card ? card.querySelector('.mg-action-btn.fav') : null;
        toggleFavorite(id, gridBtn, card);
    });

    lbSelect.addEventListener('click', function() {
        var id = this.dataset.id;
        var card = document.querySelector('.mg-item[data-id="' + id + '"]');
        var gridBtn = card ? card.querySelector('.mg-action-btn.select') : null;
        toggleSelect(id, card, gridBtn, currentGalleryId);
    });

    function toggleFavorite(id, btn, card) {
        sendToggle('<?php echo url('my-gallery/toggle-favorite'); ?>', id, function(data) {
            var isFav = data.is_favorite;
            if (btn) { btn.classList.toggle('active', isFav); btn.innerHTML = '<i class="' + (isFav ? 'fas' : 'far') + ' fa-heart"></i>'; }
            if (card) { card.classList.toggle('favorite', isFav); }
            var idx = -1;
            for (var j = 0; j < currentImages.length; j++) { if (currentImages[j].id == id) { idx = j; break; } }
            if (idx >= 0) { currentImages[idx].is_favorite = isFav; }
            if (currentImages[currentIndex] && currentImages[currentIndex].id == id) { updateLb(); }
            showToast(data.message, 'success');
        });
    }

    function toggleSelect(id, card, btn, galleryId) {
        sendToggle('<?php echo url('my-gallery/toggle-selection'); ?>', id, function(data) {
            var isSelected = data.is_final_selection;
            if (btn) { btn.classList.toggle('active', isSelected); btn.innerHTML = '<i class="' + (isSelected ? 'fas' : 'far') + ' fa-check-circle"></i>'; }
            if (card) { card.classList.toggle('selected', isSelected); }
            var idx = -1;
            for (var j = 0; j < currentImages.length; j++) { if (currentImages[j].id == id) { idx = j; break; } }
            if (idx >= 0) { currentImages[idx].is_final_selection = isSelected; }
            if (currentImages[currentIndex] && currentImages[currentIndex].id == id) { updateLb(); }
            updateSelectionCount(galleryId);
            showToast(data.message, 'success');
        }, function(err) {
            showToast(err.message || 'خطا', 'error');
        });
    }

    function sendToggle(url, id, onSuccess, onError) {
        var formData = new FormData();
        formData.append('image_id', id);
        formData.append('csrf_token', csrfToken);
        fetch(url, { method: 'POST', body: formData })
            .then(function(r) { return r.json(); })
            .then(function(data) {
                if (data.success) { onSuccess(data); } else { if (onError) onError(data); else showToast(data.message || 'خطا', 'error'); }
            })
            .catch(function() { showToast('خطا در ارتباط', 'error'); });
    }

    function updateSelectionCount(galleryId) {
        var section = document.querySelector('.mg-section[data-gallery="' + galleryId + '"]');
        if (!section) return;
        var count = section.querySelectorAll('.mg-item.selected').length;
        var countEl = section.querySelector('.mg-selection-bar .count');
        if (countEl) countEl.textContent = count;
    }

    window.clearSelection = function(galleryId) {
        var section = document.querySelector('.mg-section[data-gallery="' + galleryId + '"]');
        if (!section) return;
        section.querySelectorAll('.mg-item.selected').forEach(function(item) {
            var id = item.dataset.id;
            var btn = item.querySelector('.mg-action-btn.select');
            toggleSelect(id, item, btn, galleryId);
        });
    };

    document.querySelectorAll('[data-selection-mode]').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var galleryId = this.dataset.gallery;
            var section = document.querySelector('.mg-section[data-gallery="' + galleryId + '"]');
            var isActive = !this.classList.contains('active');
            this.classList.toggle('active', isActive);
            selectionModeGalleries[galleryId] = isActive;
            section.querySelector('.mg-selection-bar').classList.toggle('active', isActive);
            section.querySelectorAll('.mg-action-btn.select').forEach(function(b) { b.style.display = isActive ? 'flex' : 'none'; });
            section.querySelector('[data-confirm-selection]').style.display = isActive ? 'inline-flex' : 'none';
            this.textContent = isActive ? 'خروج از حالت انتخاب' : 'انتخاب نهایی';
        });
    });

    document.querySelectorAll('[data-confirm-selection]').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var galleryId = this.dataset.gallery;
            var section = document.querySelector('.mg-section[data-gallery="' + galleryId + '"]');
            var count = section.querySelectorAll('.mg-item.selected').length;
            if (count === 0) { showToast('حداقل یک عکس انتخاب کنید', 'error'); return; }
            if (!confirm('آیا از ثبت ' + count + ' عکس به عنوان انتخاب نهایی اطمینان دارید؟')) return;

            var formData = new FormData();
            formData.append('gallery_id', galleryId);
            formData.append('csrf_token', csrfToken);
            fetch('<?php echo url('my-gallery/confirm-selection'); ?>', { method: 'POST', body: formData })
                .then(function(r) { return r.json(); })
                .then(function(data) {
                    if (data.success) { showToast(data.message, 'success'); } else { showToast(data.message || 'خطا', 'error'); }
                })
                .catch(function() { showToast('خطا در ارتباط', 'error'); });
        });
    });

    document.querySelectorAll('.mg-btn[data-filter]').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var galleryId = this.dataset.gallery;
            var filter = this.dataset.filter;
            var section = document.querySelector('.mg-section[data-gallery="' + galleryId + '"]');
            section.querySelectorAll('.mg-btn[data-filter]').forEach(function(b) { b.classList.remove('active'); });
            this.classList.add('active');
            section.querySelectorAll('.mg-item').forEach(function(item) {
                item.style.display = (filter === 'all' || (filter === 'favorites' && item.classList.contains('favorite'))) ? '' : 'none';
            });
        });
    });

    document.getElementById('lbClose').addEventListener('click', closeLb);
    document.getElementById('lbPrev').addEventListener('click', function(e) {
        e.stopPropagation();
        currentIndex = (currentIndex - 1 + currentImages.length) % currentImages.length;
        updateLb();
    });
    document.getElementById('lbNext').addEventListener('click', function(e) {
        e.stopPropagation();
        currentIndex = (currentIndex + 1) % currentImages.length;
        updateLb();
    });
    lb.addEventListener('click', function(e) { if (e.target === lb) closeLb(); });
    document.addEventListener('keydown', function(e) {
        if (!lb.classList.contains('active')) return;
        if (e.key === 'Escape') closeLb();
        if (e.key === 'ArrowLeft') document.getElementById('lbPrev').click();
        if (e.key === 'ArrowRight') document.getElementById('lbNext').click();
    });
})();
</script>
