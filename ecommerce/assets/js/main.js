'use strict';

// Automatski sakrij flash poruke
document.addEventListener('DOMContentLoaded', function () {
    const alerts = document.querySelectorAll('.alert-success, .alert-error');
    alerts.forEach(function (alert) {
        setTimeout(function () {
            alert.style.transition = 'opacity 0.5s';
            alert.style.opacity = '0';
            setTimeout(function () { alert.remove(); }, 500);
        }, 5000);
    });
});

// Overlay
const overlay = document.querySelector('[data-overlay]');

// Mobilni meni
const mobileMenuOpenBtns = document.querySelectorAll('[data-mobile-menu-open-btn]');
const mobileMenus = document.querySelectorAll('[data-mobile-menu]');
const mobileMenuCloseBtns = document.querySelectorAll('[data-mobile-menu-close-btn]');

if (overlay && mobileMenus.length) {
    const closeMobileMenu = function () {
        mobileMenus.forEach(function (menu) {
            menu.classList.remove('active');
        });
        overlay.classList.remove('active');
        document.body.style.overflow = '';
    };

    mobileMenuOpenBtns.forEach(function (btn) {
        btn.addEventListener('click', function () {
            mobileMenus.forEach(function (menu) {
                menu.classList.add('active');
            });
            overlay.classList.add('active');
            document.body.style.overflow = 'hidden';
        });
    });

    mobileMenuCloseBtns.forEach(function (btn) {
        btn.addEventListener('click', closeMobileMenu);
    });

    overlay.addEventListener('click', closeMobileMenu);
}
