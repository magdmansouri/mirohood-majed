// assets/js/security.js - Frontend source protection deterrent
// Note: This only discourages casual users. Real security is server-side.
(function() {
    'use strict';

    var isDev = false;
    try {
        isDev = location.hostname === 'localhost' || location.hostname === '127.0.0.1';
    } catch (e) {}
    if (isDev) return;

    // Disable right-click context menu
    document.addEventListener('contextmenu', function(e) {
        if (e.target.tagName === 'IMG' || e.target.tagName === 'VIDEO' || e.target.closest('.allow-context')) return;
        e.preventDefault();
    });

    // Disable common dev-tool shortcuts
    document.addEventListener('keydown', function(e) {
        var key = e.key || e.keyCode;
        var ctrl = e.ctrlKey || e.metaKey;
        var shift = e.shiftKey;
        var alt = e.altKey;

        if (key === 'F12' || key === 123) { e.preventDefault(); return; }
        if (ctrl && shift && (key === 'I' || key === 'J' || key === 'C' || key === 73 || key === 74 || key === 67)) { e.preventDefault(); return; }
        if (ctrl && (key === 'U' || key === 85)) { e.preventDefault(); return; }
        if (ctrl && shift && (key === 'K' || key === 75)) { e.preventDefault(); return; }
        if (alt && (key === 'F12' || key === 123)) { e.preventDefault(); return; }
    });

    // Clear console and show a friendly warning (only once)
    if (typeof console !== 'undefined' && console.log) {
        try {
            console.clear();
            console.log('%c Mirohood ', 'background:linear-gradient(135deg,#c8a862,#a8893a);color:#0a0908;padding:4px 12px;border-radius:4px;font-weight:bold;');
            console.log('%cThis is a browser feature intended for developers. Using it to copy or inspect code is not allowed. ', 'color:#8a8580;font-size:12px;');
        } catch (e) {}
    }

    // Disable drag-and-drop of images for protection
    document.addEventListener('dragstart', function(e) {
        if (e.target.tagName === 'IMG' || e.target.tagName === 'VIDEO') {
            e.preventDefault();
        }
    });

    // Prevent printing via keyboard (Ctrl+P) as a mild deterrent
    document.addEventListener('keydown', function(e) {
        var key = e.key || e.keyCode;
        if ((e.ctrlKey || e.metaKey) && (key === 'P' || key === 80)) {
            e.preventDefault();
        }
    });
})();
