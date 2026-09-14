// Load products and ensure they persist after refresh
function loadProductsManagement() {
    console.log('Loading products for management...');
    
    fetch('/api/products')
        .then(response => response.json())
        .then(products => {
            console.log('Products loaded:', products);
            
            if (!products || products.length === 0) {
                console.log('No products found, using defaults');
                useDefaultProducts();
                return;
            }
            
            renderProductsManagement(products);
        })
        .catch(error => {
            console.error('Error loading products:', error);
            console.log('Using default products due to error');
            useDefaultProducts();
        });
}

// Render products management grid
function renderProductsManagement(products) {
    console.log('Rendering products:', products);
    const grid = document.getElementById('productsManagementGrid');
    if (!grid) {
        console.error('Products management grid not found!');
        return;
    }
    
    if (!products || products.length === 0) {
        grid.innerHTML = '<div style="text-align: center; padding: 2rem; color: #8d6e63;">No products found. Please add products to the database.</div>';
        return;
    }
    
    grid.innerHTML = products.map(product => {
        const stockClass = getStockClass(product.stock);
        const stockText = product.stock === 0 ? 'Out of Stock' : 
                         product.stock <= 5 ? 'Low Stock' : 'In Stock';
        
        return `
            <div class="product-management-card">
                <div class="product-header">
                    <div>
                        <div class="product-name">${product.name}</div>
                        <div class="product-unit">Per ${product.unit}</div>
                    </div>
                    <div class="stock-badge ${stockClass}">
                        ${stockText}: ${product.stock}
                    </div>
                </div>
                
                <div class="product-controls">
                    <div class="price-display" onclick="editProductPrice(${product.id}, ${product.price})" title="Click to edit price">
                        ₱${parseFloat(product.price).toFixed(2)}
                        <div style="font-size: 0.7rem; opacity: 0.8; margin-top: 0.25rem;">Click to edit</div>
                    </div>
                    <div class="stock-display" onclick="editProductStock(${product.id}, ${product.stock})" title="Click to edit stock">
                        Stock: ${product.stock}
                        <div style="font-size: 0.7rem; opacity: 0.8; margin-top: 0.25rem;">Click to edit</div>
                    </div>
                </div>
            </div>
        `;
    }).join('');
    
    console.log('Products rendered successfully');
}

// Auto-load products when page loads
document.addEventListener('DOMContentLoaded', function() {
    // Load products immediately
    loadProductsManagement();
    
    // Also load when switching to orders tab
    const ordersTab = document.querySelector('[onclick*="orders"]');
    if (ordersTab) {
        ordersTab.addEventListener('click', function() {
            setTimeout(loadProductsManagement, 100);
        });
    }
});
