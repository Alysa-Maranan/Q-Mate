<script>
// Fix: Ensure products load immediately when page loads
(function() {
    'use strict';
    
    // Wait for DOM to be ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initProducts);
    } else {
        initProducts();
    }
    
    function initProducts() {
        console.log('Initializing products...');
        
        // Load products immediately
        if (typeof loadProductsManagement === 'function') {
            loadProductsManagement();
        }
        
        // Also reload when switching to orders tab
        const categoryTabs = document.querySelectorAll('.category-tab');
        categoryTabs.forEach(tab => {
            tab.addEventListener('click', function() {
                const tabText = this.textContent.toLowerCase();
                if (tabText.includes('orders') || tabText.includes('products')) {
                    setTimeout(function() {
                        if (typeof loadProductsManagement === 'function') {
                            loadProductsManagement();
                        }
                    }, 100);
                }
            });
        });
        
        // Check if we're on orders tab on page load
        const ordersTab = document.getElementById('orders');
        if (ordersTab && ordersTab.classList.contains('active')) {
            setTimeout(function() {
                if (typeof loadProductsManagement === 'function') {
                    loadProductsManagement();
                }
            }, 500);
        }
    }
})();
</script>
