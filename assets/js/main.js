// assets/js/main.js - جاوااسکریپت اصلی

document.addEventListener('DOMContentLoaded', function() {
    'use strict';

    // ===== منوی موبایل =====
    const menuToggle = document.querySelector('.menu-toggle');
    const mobileMenu = document.getElementById('mobile-menu');

    if (menuToggle && mobileMenu) {
        menuToggle.addEventListener('click', function() {
            mobileMenu.classList.toggle('hidden');
            const icon = this.querySelector('i');
            if (icon) {
                icon.classList.toggle('fa-bars');
                icon.classList.toggle('fa-times');
            }
        });
    }

    // ===== افکت Reveal =====
    const revealElements = document.querySelectorAll('.reveal');

    function checkReveal() {
        const windowHeight = window.innerHeight;
        const revealPoint = 150;

        revealElements.forEach(function(element) {
            const rect = element.getBoundingClientRect();
            const delay = parseFloat(element.getAttribute('data-delay')) || 0;

            if (rect.top < windowHeight - revealPoint) {
                setTimeout(function() {
                    element.classList.add('visible');
                }, delay * 1000);
            }
        });
    }

    checkReveal();
    window.addEventListener('scroll', checkReveal);

    // ===== هدر چسبنده =====
    const navbar = document.querySelector('.navbar');
    
    if (navbar) {
        window.addEventListener('scroll', function() {
            if (window.scrollY > 50) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        });
    }

    console.log('Mirohood — loaded successfully');
});