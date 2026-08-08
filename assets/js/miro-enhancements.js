// assets/js/miro-enhancements.js - Mirohood global UI enhancements
(function() {
    'use strict';

    var isTouch = window.matchMedia('(pointer: coarse)').matches;
    var reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    // ============================================================
    // 1. Custom Cursor
    // ============================================================
    function initCursor() {
        if (isTouch || reducedMotion) return;
        if (document.getElementById('miro-cursor')) return;

        var cursor = document.createElement('div');
        cursor.id = 'miro-cursor';
        document.body.appendChild(cursor);
        document.body.classList.add('miro-custom-cursor');

        var mouseX = 0, mouseY = 0, curX = 0, curY = 0;
        var rafId = null;

        function animate() {
            curX += (mouseX - curX) * 0.18;
            curY += (mouseY - curY) * 0.18;
            cursor.style.transform = 'translate(' + curX + 'px, ' + curY + 'px) translate(-50%, -50%)';
            rafId = requestAnimationFrame(animate);
        }

        document.addEventListener('mousemove', function(e) {
            mouseX = e.clientX;
            mouseY = e.clientY;
            if (!rafId) animate();
        }, { passive: true });

        var hoverTargets = 'a, button, .btn-primary, .btn-outline, .submit-btn, .admin-btn, .pkg-card, .gal-item, .person-card, .photo-item, .client-photo-item, .glass-card, .quick-link-card';
        document.addEventListener('mouseover', function(e) {
            if (e.target.closest(hoverTargets)) cursor.classList.add('miro-cursor--hover');
        });
        document.addEventListener('mouseout', function(e) {
            if (e.target.closest(hoverTargets)) cursor.classList.remove('miro-cursor--hover');
        });
    }

    // ============================================================
    // 2. Scroll Reveal
    // ============================================================
    function initReveal() {
        var elements = document.querySelectorAll('[data-animate]');
        if (!elements.length) return;

        var observer = new IntersectionObserver(function(entries) {
            entries.forEach(function(entry) {
                if (entry.isIntersecting) {
                    var el = entry.target;
                    var delay = parseInt(el.getAttribute('data-delay') || '0', 10);
                    setTimeout(function() {
                        el.classList.add('miro-revealed');
                    }, delay);
                    observer.unobserve(el);
                }
            });
        }, { threshold: 0.12, rootMargin: '0px 0px -40px 0px' });

        elements.forEach(function(el) { observer.observe(el); });
    }

    // ============================================================
    // 3. Floating Labels (JS fallback for label-before-input markup)
    // ============================================================
    function initFloatingLabels() {
        document.querySelectorAll('.miro-floating-label').forEach(function(wrap) {
            var input = wrap.querySelector('input, textarea, select');
            if (!input) return;

            function update() {
                if (input.value.trim() || document.activeElement === input) {
                    wrap.classList.add('miro-floating--active');
                } else {
                    wrap.classList.remove('miro-floating--active');
                }
            }

            input.addEventListener('focus', update);
            input.addEventListener('blur', update);
            input.addEventListener('input', update);
            input.addEventListener('change', update);
            update();
        });
    }

    // ============================================================
    // 4. Ripple Effect
    // ============================================================
    function initRipple() {
        var selectors = [
            '.btn-primary', '.btn-outline', '.submit-btn', '.admin-btn',
            '.admin-btn-outline', '.admin-btn-danger', '.pkg-btn',
            '.bottom-nav-link', '.action-btn', '.lightbox-download', '.download-all'
        ].join(', ');

        document.addEventListener('click', function(e) {
            var btn = e.target.closest(selectors);
            if (!btn) return;
            if (!btn.classList.contains('miro-ripple')) btn.classList.add('miro-ripple');

            var rect = btn.getBoundingClientRect();
            var size = Math.max(rect.width, rect.height) * 0.5;
            var x = e.clientX - rect.left - size;
            var y = e.clientY - rect.top - size;

            var ripple = document.createElement('span');
            ripple.className = 'miro-ripple-effect';
            ripple.style.width = ripple.style.height = (size * 2) + 'px';
            ripple.style.left = x + 'px';
            ripple.style.top = y + 'px';
            btn.appendChild(ripple);

            setTimeout(function() {
                if (ripple.parentNode) ripple.parentNode.removeChild(ripple);
            }, 700);
        });
    }

    // ============================================================
    // 5. Universal Lightbox
    // ============================================================
    function initLightbox() {
        var triggers = document.querySelectorAll('[data-lightbox]');
        if (!triggers.length) return;

        var box = document.createElement('div');
        box.id = 'miro-lightbox';
        box.setAttribute('aria-hidden', 'true');
        box.innerHTML = '<div class="miro-lb-backdrop"></div>' +
            '<div class="miro-lb-counter" id="miro-lb-counter"></div>' +
            '<button class="miro-lb-btn miro-lb-close" aria-label="بستن"><i class="fas fa-times"></i></button>' +
            '<button class="miro-lb-btn miro-lb-prev" aria-label="قبلی"><i class="fas fa-chevron-right"></i></button>' +
            '<button class="miro-lb-btn miro-lb-next" aria-label="بعدی"><i class="fas fa-chevron-left"></i></button>' +
            '<div class="miro-lb-stage"><img class="miro-lb-img" id="miro-lb-img" src="" alt="" loading="eager"></div>' +
            '<div class="miro-lb-caption" id="miro-lb-caption"></div>';
        document.body.appendChild(box);

        var items = [];
        var current = 0;
        var imgEl = document.getElementById('miro-lb-img');
        var captionEl = document.getElementById('miro-lb-caption');
        var counterEl = document.getElementById('miro-lb-counter');
        var isOpen = false;

        function collectItems() {
            items = Array.from(document.querySelectorAll('[data-lightbox]')).map(function(el) {
                return {
                    el: el,
                    src: el.getAttribute('data-lightbox') || el.src || '',
                    caption: el.getAttribute('data-caption') || el.getAttribute('alt') || el.getAttribute('aria-label') || ''
                };
            }).filter(function(it) { return it.src; });
        }

        function open(index) {
            collectItems();
            if (!items.length) return;
            current = index;
            if (current < 0) current = 0;
            if (current >= items.length) current = items.length - 1;
            update();
            box.classList.add('miro-lightbox--active');
            box.setAttribute('aria-hidden', 'false');
            document.body.style.overflow = 'hidden';
            isOpen = true;
        }

        function close() {
            box.classList.remove('miro-lightbox--active');
            box.setAttribute('aria-hidden', 'true');
            document.body.style.overflow = '';
            isOpen = false;
            setTimeout(function() { imgEl.src = ''; }, 300);
        }

        function update() {
            var it = items[current];
            if (!it) return;
            imgEl.src = it.src;
            imgEl.alt = it.caption || '';
            captionEl.textContent = it.caption || '';
            captionEl.style.display = it.caption ? 'block' : 'none';
            counterEl.textContent = (current + 1) + ' / ' + items.length;
        }

        function next() {
            if (!items.length) return;
            current = (current + 1) % items.length;
            update();
        }

        function prev() {
            if (!items.length) return;
            current = (current - 1 + items.length) % items.length;
            update();
        }

        document.addEventListener('click', function(e) {
            var trigger = e.target.closest('[data-lightbox]');
            if (!trigger) return;
            // Don't open if clicking a download link or action button inside a trigger
            if (e.target.closest('a[download], .action-btn, .download-btn, .lightbox-download')) return;
            e.preventDefault();
            collectItems();
            var index = items.findIndex(function(it) { return it.el === trigger; });
            if (index === -1) {
                // fallback by src
                var src = trigger.getAttribute('data-lightbox') || trigger.src || '';
                index = items.findIndex(function(it) { return it.src === src; });
            }
            open(index >= 0 ? index : 0);
        });

        box.querySelector('.miro-lb-close').addEventListener('click', close);
        box.querySelector('.miro-lb-prev').addEventListener('click', function(e) { e.stopPropagation(); prev(); });
        box.querySelector('.miro-lb-next').addEventListener('click', function(e) { e.stopPropagation(); next(); });
        box.querySelector('.miro-lb-backdrop').addEventListener('click', close);

        document.addEventListener('keydown', function(e) {
            if (!isOpen) return;
            if (e.key === 'Escape') close();
            if (e.key === 'ArrowLeft') prev();
            if (e.key === 'ArrowRight') next();
        });

        // Swipe support
        var startX = 0;
        box.addEventListener('touchstart', function(e) { startX = e.touches[0].clientX; }, { passive: true });
        box.addEventListener('touchend', function(e) {
            var endX = e.changedTouches[0].clientX;
            var diff = startX - endX;
            if (Math.abs(diff) > 50) {
                if (diff > 0) next(); else prev();
            }
        }, { passive: true });

        // Re-collect when DOM changes (lazy loading, AJAX)
        var mo = new MutationObserver(function() { collectItems(); });
        mo.observe(document.body, { childList: true, subtree: true });
    }

    // ============================================================
    // Initialize
    // ============================================================
    function init() {
        if (!isTouch && !reducedMotion) initCursor();
        initReveal();
        initFloatingLabels();
        initRipple();
        initLightbox();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
