// Product Management Functions
let currentEditProductId = null;
let currentEditType = null;

function useDefaultProducts() {
    console.log('Using default products...');
    const defaultProducts = [
        { id: 1, name: 'Fresh Quail Eggs', unit: 'tray (24 pieces)', price: 80, stock: 50 },
        { id: 2, name: 'Live Quail', unit: 'piece', price: 180, stock: 25 },
        { id: 3, name: 'Dressed Quail', unit: 'piece (cleaned)', price: 250, stock: 15 }
    ];
    renderProductsManagement(defaultProducts);
}

async function loadProductsManagement() {
    console.log('Loading products for management...');
    
    try {
        const response = await fetch('/api/products');
        console.log('API Response status:', response.status);
        
        if (!response.ok) {
            console.error('API response not ok:', response.status, response.statusText);
            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }
        
        const products = await response.json();
        console.log('Products loaded:', products);
        
        if (!products || products.length === 0) {
            console.log('No products found, using defaults');
            useDefaultProducts();
            return;
        }
        
        renderProductsManagement(products);
    } catch (error) {
        console.error('Error loading products:', error);
        console.log('Using default products due to error');
        useDefaultProducts();
    }
}

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

function getStockClass(stock) {
    if (stock === 0) return 'out';
    if (stock <= 5) return 'low';
    return '';
}

function editProductPrice(productId, currentPrice) {
    currentEditProductId = productId;
    currentEditType = 'price';
    document.getElementById('priceEditInput').value = currentPrice;
    document.getElementById('priceEditModal').classList.add('show');
}

function editProductStock(productId, currentStock) {
    currentEditProductId = productId;
    currentEditType = 'stock';
    document.getElementById('stockEditInput').value = currentStock;
    document.getElementById('stockEditModal').classList.add('show');
}

function closeEditModal(modalId) {
    document.getElementById(modalId).classList.remove('show');
    currentEditProductId = null;
    currentEditType = null;
}

async function savePriceEdit() {
    const newPrice = document.getElementById('priceEditInput').value;
    
    if (!newPrice || newPrice <= 0) {
        alert('Please enter a valid price');
        return;
    }
    
    try {
        const response = await fetch(`/products/${currentEditProductId}/price`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ price: newPrice })
        });
        
        const result = await response.json();
        
        if (response.ok && result.success) {
            closeEditModal('priceEditModal');
            loadProductsManagement();
            alert(`Price updated to ₱${parseFloat(newPrice).toFixed(2)}`);
        } else {
            alert('Error updating price: ' + (result?.message || `Server error (${response.status})`));
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Error updating price: ' + error.message);
    }
}

async function saveStockEdit() {
    const newStock = document.getElementById('stockEditInput').value;
    
    if (newStock === '' || newStock < 0) {
        alert('Please enter a valid stock quantity');
        return;
    }
    
    try {
        const response = await fetch(`/products/${currentEditProductId}/stock`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ stock: newStock })
        });
        
        const result = await response.json();
        
        if (response.ok && result.success) {
            closeEditModal('stockEditModal');
            loadProductsManagement();
            alert(`Stock updated to ${newStock} units`);
        } else {
            alert('Error updating stock: ' + (result?.message || `Server error (${response.status})`));
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Error updating stock: ' + error.message);
    }
}

function callCustomer(phone) {
    if (phone) {
        window.location.href = 'tel:' + phone.replace(/\s/g, '');
    } else {
        alert('No phone number available');
    }
}

function markOrderDone(orderId) {
    const id = String(orderId);
    
    if (!id || id === 'undefined' || id === 'null') {
        alert('Error: Invalid order ID.');
        return;
    }
    
    if (!confirm(`Mark order #${id} as done?`)) {
        return;
    }
    
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    
    if (!csrfToken) {
        alert('CSRF token not found. Please refresh the page.');
        return;
    }
    
    const btn = document.getElementById('check-btn-' + id);
    if (btn) {
        btn.textContent = '⏳';
        btn.disabled = true;
    }
    
    fetch('/orders/' + id + '/status', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json'
        },
        body: JSON.stringify({ status: 'done' })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const badge = document.getElementById('status-badge-' + id);
            if (badge) {
                badge.textContent = '✓ Done';
                badge.style.background = '#e8f5e9';
                badge.style.color = '#2e7d32';
            }
            
            if (btn) {
                btn.textContent = '✓';
                btn.disabled = true;
                btn.style.opacity = '0.5';
                btn.style.cursor = 'not-allowed';
                btn.title = 'Already marked as done';
            }
            
            alert('Order marked as done successfully');
        } else {
            if (btn) {
                btn.textContent = '✓ Mark Done';
                btn.disabled = false;
            }
            alert('Error: ' + (data.message || 'Unknown error'));
        }
    })
    .catch(error => {
        if (btn) {
            btn.textContent = '✓ Mark Done';
            btn.disabled = false;
        }
        alert('Error updating order status. Please try again.');
    });
}

function deleteOrder(orderId) {
    if (!confirm('Are you sure you want to delete this order? This action cannot be undone.')) {
        return;
    }
    
    fetch('/orders/' + orderId + '/delete', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const row = document.querySelector(`[data-order-id="${orderId}"]`);
            if (row) {
                row.remove();
            } else {
                location.reload();
            }
            alert('Order deleted successfully');
        } else {
            alert('Error deleting order: ' + (data.message || 'Unknown error'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error deleting order');
    });
}

document.addEventListener('DOMContentLoaded', function() {
    loadProductsManagement();
});
