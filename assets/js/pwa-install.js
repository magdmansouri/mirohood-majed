// assets/js/pwa-install.js - PWA install helper
(function() {
    'use strict';

    var deferredPrompt = null;
    var isIos = /iPad|iPhone|iPod/.test(navigator.userAgent) && !window.MSStream;
    var isStandalone = window.matchMedia('(display-mode: standalone)').matches || window.navigator.standalone === true;

    function createInstallBanner() {
        if (document.getElementById('pwa-install-banner')) return;

        var banner = document.createElement('div');
        banner.id = 'pwa-install-banner';
        banner.style.cssText = 'position:fixed;bottom:calc(12px + env(safe-area-inset-bottom));left:50%;transform:translateX(-50%);width:min(92%,400px);z-index:9999;background:rgba(26,23,21,0.98);border:1px solid rgba(200,168,98,0.2);border-radius:1rem;padding:1rem 1.2rem;box-shadow:0 20px 60px rgba(0,0,0,0.6);backdrop-filter:blur(20px);display:flex;align-items:center;gap:1rem;transition:all 0.4s ease;';
        banner.innerHTML = '<div style="flex:1;"><p style="margin:0 0 0.2rem;color:#f4f1ea;font-size:0.85rem;font-weight:500;">Mirohood Studio</p><p style="margin:0;color:#8a8580;font-size:0.75rem;line-height:1.5;">' + (isIos ? 'برای نصب روی صفحه اصلی، دکمه Share را بزنید و Add to Home Screen را انتخاب کنید.' : 'Mirohood را به صفحه اصلی گوشی خود اضافه کنید.') + '</p></div><button id="pwa-install-btn" style="padding:0.5rem 1.2rem;background:linear-gradient(135deg,#c8a862,#a8893a);border:none;border-radius:9999px;color:#0a0908;font-size:0.75rem;font-weight:600;cursor:pointer;white-space:nowrap;">' + (isIos ? 'متوجه شدم' : 'نصب') + '</button><button id="pwa-dismiss-btn" style="background:none;border:none;color:#8a8580;font-size:1rem;cursor:pointer;padding:0.2rem;">&times;</button>';

        document.body.appendChild(banner);

        document.getElementById('pwa-dismiss-btn').addEventListener('click', function() {
            banner.style.opacity = '0';
            banner.style.transform = 'translateX(-50%) translateY(20px)';
            setTimeout(function() { banner.remove(); }, 400);
            try { localStorage.setItem('pwa_banner_dismissed', '1'); } catch (e) {}
        });

        document.getElementById('pwa-install-btn').addEventListener('click', function() {
            if (isIos) {
                banner.remove();
                return;
            }
            if (deferredPrompt) {
                deferredPrompt.prompt();
                deferredPrompt.userChoice.then(function() {
                    deferredPrompt = null;
                    banner.remove();
                });
            }
        });
    }

    if (isStandalone) return;

    try {
        if (localStorage.getItem('pwa_banner_dismissed') === '1') return;
    } catch (e) {}

    window.addEventListener('beforeinstallprompt', function(e) {
        e.preventDefault();
        deferredPrompt = e;
        if (!document.getElementById('pwa-install-banner')) {
            setTimeout(createInstallBanner, 2000);
        }
    });

    if (isIos) {
        setTimeout(function() {
            if (!document.getElementById('pwa-install-banner')) createInstallBanner();
        }, 3000);
    }
})();
