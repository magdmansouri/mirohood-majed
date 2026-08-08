(function() {
    'use strict';

    // ============================================================
    // Entrance animations
    // ============================================================
    function initAnimations() {
        var animated = document.querySelectorAll('[data-animate]');
        if (!animated.length || !('IntersectionObserver' in window)) return;

        var observer = new IntersectionObserver(function(entries) {
            entries.forEach(function(entry) {
                if (entry.isIntersecting) {
                    var delay = parseInt(entry.target.getAttribute('data-delay') || '0', 10);
                    setTimeout(function() {
                        entry.target.classList.add('animate-in');
                    }, delay);
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.1, rootMargin: '0px 0px -50px 0px' });

        animated.forEach(function(el) { observer.observe(el); });
    }

    // ============================================================
    // Photo Lightbox — person gallery
    // ============================================================
    function initLightbox() {
        var lightbox = document.getElementById('photo-lightbox');
        if (!lightbox) return;

        var img = document.getElementById('lightbox-img');
        var caption = lightbox.querySelector('.lightbox-caption');
        var closeBtn = lightbox.querySelector('.lightbox-close');
        var prevBtn = lightbox.querySelector('.lightbox-prev');
        var nextBtn = lightbox.querySelector('.lightbox-next');
        var items = Array.prototype.slice.call(document.querySelectorAll('.photo-item'));
        var currentIndex = -1;
        var touchStartX = 0;
        var bodyOverflow = '';
        var lastFocusedElement = null;

        if (!img || !caption || !closeBtn || !prevBtn || !nextBtn || !items.length) return;

        // آلبوم‌های قدیمی هم در صورت نداشتن data-src از تصویر داخل کارت استفاده می‌کنند.
        items = items.filter(function(item) {
            var thumbnail = item.querySelector('img');
            if (!item.getAttribute('data-src') && thumbnail) {
                item.setAttribute('data-src', thumbnail.currentSrc || thumbnail.src || '');
                item.setAttribute('data-alt', item.getAttribute('data-alt') || thumbnail.alt || '');
            }
            return !!item.getAttribute('data-src');
        });
        if (!items.length) return;

        if (items.length < 2) {
            prevBtn.hidden = true;
            nextBtn.hidden = true;
        }

        function sourceFor(item) {
            return item.getAttribute('data-src') || '';
        }

        function focusElement(element) {
            if (!element || typeof element.focus !== 'function') return;
            try {
                element.focus({ preventScroll: true });
            } catch (error) {
                element.focus();
            }
        }

        function open(index) {
            if (index < 0 || index >= items.length) return;

            var item = items[index];
            var source = sourceFor(item);
            if (!source) return;

            var wasOpen = lightbox.classList.contains('active');
            currentIndex = index;
            if (!wasOpen) {
                lastFocusedElement = document.activeElement;
                bodyOverflow = document.body.style.overflow;
            }
            img.classList.remove('loaded');
            img.style.transform = '';
            img.alt = item.getAttribute('data-alt') || '';
            caption.textContent = item.getAttribute('data-caption') || '';
            caption.hidden = !caption.textContent;

            // رویدادها قبل از تنظیم src تعریف می‌شوند تا تصویر cache شده هم درست نمایش داده شود.
            img.onload = function() {
                img.classList.add('loaded');
            };
            img.onerror = function() {
                img.alt = 'بارگذاری تصویر انجام نشد';
                img.classList.add('loaded');
            };
            img.src = source;

            lightbox.setAttribute('aria-hidden', 'false');
            lightbox.classList.add('active');
            document.body.style.overflow = 'hidden';
            focusElement(lightbox);

            if (img.complete && img.naturalWidth > 0) {
                img.classList.add('loaded');
            }
        }

        function close() {
            if (!lightbox.classList.contains('active')) return;

            lightbox.classList.remove('active');
            lightbox.setAttribute('aria-hidden', 'true');
            document.body.style.overflow = bodyOverflow;
            currentIndex = -1;
            img.style.transform = '';

            focusElement(lastFocusedElement);
        }

        function next() {
            if (items.length > 1 && currentIndex !== -1) {
                open((currentIndex + 1) % items.length);
            }
        }

        function prev() {
            if (items.length > 1 && currentIndex !== -1) {
                open((currentIndex - 1 + items.length) % items.length);
            }
        }

        items.forEach(function(item, index) {
            item.addEventListener('click', function() { open(index); });
            item.addEventListener('keydown', function(event) {
                if (event.key === 'Enter' || event.key === ' ') {
                    event.preventDefault();
                    open(index);
                }
            });
        });

        closeBtn.addEventListener('click', close);
        nextBtn.addEventListener('click', next);
        prevBtn.addEventListener('click', prev);

        lightbox.addEventListener('click', function(event) {
            if (event.target === lightbox || event.target.classList.contains('lightbox-backdrop')) {
                close();
            }
        });

        document.addEventListener('keydown', function(event) {
            if (!lightbox.classList.contains('active')) return;
            if (event.key === 'Escape') {
                event.preventDefault();
                close();
            } else if (event.key === 'ArrowRight') {
                event.preventDefault();
                next();
            } else if (event.key === 'ArrowLeft') {
                event.preventDefault();
                prev();
            }
        });

        // Swipe navigation on phones
        lightbox.addEventListener('touchstart', function(event) {
            touchStartX = event.changedTouches[0].screenX;
        }, { passive: true });

        lightbox.addEventListener('touchend', function(event) {
            var diff = touchStartX - event.changedTouches[0].screenX;
            if (Math.abs(diff) > 50) {
                if (diff > 0) next(); else prev();
            }
        }, { passive: true });

        // Double-click / double-tap mouse support for a closer view.
        img.addEventListener('dblclick', function() {
            img.style.transform = img.style.transform === 'scale(1.5)' ? 'scale(1)' : 'scale(1.5)';
        });
    }

    function init() {
        initAnimations();
        initLightbox();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
