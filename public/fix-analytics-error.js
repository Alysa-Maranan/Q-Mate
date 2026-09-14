// Fix: Disable automatic analytics loading on tab switch
// Only load analytics when user is on Analytics tab AND clicks "Update Analytics" button

function showCategory(categoryId) {
    // Hide all categories
    document.querySelectorAll('.category-content').forEach(content => {
        content.classList.remove('active');
    });
    
    // Remove active class from all tabs
    document.querySelectorAll('.category-tab').forEach(tab => {
        tab.classList.remove('active');
    });
    
    // Show selected category
    document.getElementById(categoryId).classList.add('active');
    
    // Add active class to clicked tab
    if (event && event.currentTarget) {
        event.currentTarget.classList.add('active');
    } else {
        // If called programmatically, find and activate the corresponding tab
        document.querySelectorAll('.category-tab').forEach(tab => {
            if (tab.textContent.toLowerCase().includes(categoryId.toLowerCase()) || 
                tab.getAttribute('onclick').includes(categoryId)) {
                tab.classList.add('active');
            }
        });
    }
    
    // Save active tab to localStorage
    localStorage.setItem('inventoryActiveTab', categoryId);
    
    // REMOVED: Automatic analytics loading on tab switch
    // Users must manually click "Update Analytics" button
    
    // If switching to orders tab, load products
    if (categoryId === 'orders') {
        console.log('Switching to orders tab - loading products...');
        setTimeout(() => {
            if (typeof loadProductsManagement === 'function') {
                loadProductsManagement();
            }
        }, 200);
    }
    
    // Scroll to top
    window.scrollTo({ top: 200, behavior: 'smooth' });
}
