// Performance optimization script
(function() {
    'use strict';
    
    // Debounce function to prevent excessive calls
    function debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }
    
    // Throttle function for scroll/resize events
    function throttle(func, limit) {
        let inThrottle;
        return function() {
            const args = arguments;
            const context = this;
            if (!inThrottle) {
                func.apply(context, args);
                inThrottle = true;
                setTimeout(() => inThrottle = false, limit);
            }
        }
    }
    
    // Optimize animations
    function optimizeAnimations() {
        // Reduce motion for users who prefer it
        if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
            const style = document.createElement('style');
            style.textContent = `
                *, *::before, *::after {
                    animation-duration: 0.01ms !important;
                    animation-iteration-count: 1 !important;
                    transition-duration: 0.01ms !important;
                }
            `;
            document.head.appendChild(style);
        }
    }
    
    // Lazy load images
    function lazyLoadImages() {
        const images = document.querySelectorAll('img[data-src]');
        const imageObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    img.src = img.dataset.src;
                    img.removeAttribute('data-src');
                    observer.unobserve(img);
                }
            });
        });
        
        images.forEach(img => imageObserver.observe(img));
    }
    
    // Optimize notification system
    function optimizeNotifications() {
        const originalAddNotification = window.notify?.success;
        if (originalAddNotification) {
            window.notify.success = debounce(originalAddNotification, 1000);
        }
    }
    
    // Clean up event listeners
    function cleanupEventListeners() {
        // Remove duplicate event listeners
        const elements = document.querySelectorAll('[onclick]');
        elements.forEach(el => {
            const onclick = el.getAttribute('onclick');
            el.removeAttribute('onclick');
            el.addEventListener('click', new Function(onclick), { once: false });
        });
    }
    
    // Initialize optimizations
    document.addEventListener('DOMContentLoaded', function() {
        optimizeAnimations();
        lazyLoadImages();
        optimizeNotifications();
        cleanupEventListeners();
        
        // Clear localStorage periodically to prevent bloat
        if (Math.random() < 0.1) { // 10% chance
            const keys = Object.keys(localStorage);
            keys.forEach(key => {
                if (key.startsWith('temp_') || key.includes('cache_')) {
                    localStorage.removeItem(key);
                }
            });
        }
    });
    
    // Optimize page visibility changes
    document.addEventListener('visibilitychange', function() {
        if (document.hidden) {
            // Pause animations when tab is not visible
            document.body.style.animationPlayState = 'paused';
        } else {
            document.body.style.animationPlayState = 'running';
        }
    });
    
})();