<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Advertising | Escalona's Farm</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Inter', sans-serif; background: #f8f5f2; min-height: 100vh; color: #4e342e; }

        .nav { 
            background: linear-gradient(135deg, #fffdfa 0%, #f5eee6 100%); 
            border-bottom: 2px solid #d7ccc8;
            padding: 1rem 2rem; 
            display: flex; 
            align-items: center; 
            justify-content: space-between; 
            position: sticky; 
            top: 0; 
            z-index: 100;
            box-shadow: 0 4px 20px rgba(121,85,72,0.08);
        }
        .nav-logo { display: flex; align-items: center; gap: 0.75rem; text-decoration: none; }
        .nav-logo-img { 
            width: 45px; 
            height: 45px; 
            border-radius: 12px; 
            background: white; 
            overflow: hidden; 
            border: 2px solid #a1887f;
            box-shadow: 0 2px 8px rgba(121,85,72,0.18);
        }
        .nav-logo-img img { width: 100%; height: 100%; object-fit: cover; }
        .nav-logo span { font-size: 1.25rem; font-weight: 800; color: #6d4c41; letter-spacing: 0.5px; }
        .nav-back { 
            text-decoration: none; 
            color: #6d4c41; 
            font-weight: 600; 
            font-size: 0.9rem; 
            padding: 0.6rem 1.25rem; 
            border-radius: 8px; 
            background: #fffdfa; 
            border: 1.5px solid #d7ccc8;
            transition: all 0.2s;
        }
        .nav-back:hover { background: #efebe9; border-color: #a1887f; transform: translateY(-1px); }

        .hero {
            background: linear-gradient(135deg, rgba(109,76,65,0.95) 0%, rgba(62,39,35,0.98) 100%), 
                        url('https://images.unsplash.com/photo-1569288052389-dac9b01c9c05?w=1200') center/cover no-repeat;
            padding: 4rem 2rem;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        .hero-content { position: relative; z-index: 1; max-width: 600px; margin: 0 auto; }
        .hero h1 { 
            font-size: clamp(1.8rem, 5vw, 2.8rem); 
            font-weight: 900; 
            color: #fff; 
            margin-bottom: 0.75rem;
            letter-spacing: -1px;
            line-height: 1.2;
        }
        .hero p { color: rgba(255,224,178,0.9); font-size: 1.05rem; line-height: 1.7; }

        .main-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2.5rem 1.5rem 3rem;
        }

        .section-header {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin-bottom: 2rem;
        }
        .section-icon {
            width: 42px;
            height: 42px;
            background: linear-gradient(135deg, #a1887f 0%, #6d4c41 100%);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
            box-shadow: 0 4px 12px rgba(109,76,65,0.2);
        }
        .section-header h2 {
            font-size: 1.35rem;
            font-weight: 800;
            color: #4e342e;
            letter-spacing: -0.5px;
        }

        .products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
        }

        .product-card {
            background: #fff;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 8px 32px rgba(109,76,65,0.1);
            border: 1px solid rgba(215,204,200,0.5);
            transition: all 0.3s ease;
            position: relative;
        }
        .product-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 16px 48px rgba(109,76,65,0.2);
        }

        .product-img-wrapper {
            height: 200px;
            background: linear-gradient(135deg, #efebe9 0%, #d7ccc8 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            position: relative;
        }
        .product-img-wrapper img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s ease;
        }
        .product-card:hover .product-img-wrapper img { transform: scale(1.1); }

        .product-content { padding: 1.5rem; }
        .product-name {
            font-size: 1.2rem;
            font-weight: 700;
            color: #4e342e;
            margin-bottom: 0.5rem;
        }
        .product-description {
            font-size: 0.9rem;
            color: #8d6e63;
            margin-bottom: 1rem;
            line-height: 1.5;
        }
        .product-unit {
            font-size: 0.85rem;
            color: #8d6e63;
            margin-bottom: 1rem;
        }

        .product-footer {
            margin-top: 1rem;
        }

        .product-price {
            display: inline-flex;
            align-items: center;
            background: linear-gradient(135deg, #6d4c41 0%, #5d4037 100%);
            color: #fff;
            padding: 0.6rem 1.2rem;
            border-radius: 12px;
            font-size: 1.1rem;
            font-weight: 800;
            cursor: pointer;
            transition: all 0.2s;
        }
        .product-price:hover { 
            background: linear-gradient(135deg, #5d4037 0%, #4e342e 100%);
            transform: translateY(-2px);
        }

        .stock-info {
            display: flex;
            flex-direction: column;
            align-items: flex-end;
        }
        .stock-badge {
            background: #4caf50;
            color: white;
            padding: 0.4rem 0.8rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }
        .stock-badge.low { background: #ff9800; }
        .stock-badge.out { background: #f44336; }

        .stock-edit {
            font-size: 0.75rem;
            color: #8d6e63;
            cursor: pointer;
            text-decoration: underline;
        }
        .stock-edit:hover { color: #6d4c41; }

        .edit-modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 1000;
            align-items: center;
            justify-content: center;
        }
        .edit-content {
            background: white;
            padding: 2rem;
            border-radius: 16px;
            width: 90%;
            max-width: 400px;
        }
        .edit-header {
            font-size: 1.2rem;
            font-weight: 700;
            color: #4e342e;
            margin-bottom: 1rem;
        }
        .edit-input {
            width: 100%;
            padding: 0.75rem;
            border: 2px solid #e8e0dc;
            border-radius: 8px;
            font-size: 1rem;
            margin-bottom: 1rem;
        }
        .edit-buttons {
            display: flex;
            gap: 0.5rem;
        }
        .btn {
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            flex: 1;
        }
        .btn-primary {
            background: #6d4c41;
            color: white;
        }
        .btn-secondary {
            background: #e0e0e0;
            color: #666;
        }

        .order-btn {
            background: linear-gradient(135deg, #4caf50 0%, #388e3c 100%);
            color: white;
            padding: 0.6rem 1.2rem;
            border: none;
            border-radius: 12px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
        }
        .order-btn:hover {
            background: linear-gradient(135deg, #388e3c 0%, #2e7d32 100%);
            transform: translateY(-2px);
        }
    </style>
</head>
<body>

<nav class="nav">
    <a href="{{ url('/') }}" class="nav-logo">
        <div class="nav-logo-img">
            <img src="https://th.bing.com/th/id/R.2f6964b62e896f5f94f40390e330a1f8?rik=xmu%2bEOduizkGYw&riu=http%3a%2f%2f1.bp.blogspot.com%2f-unYxCWFMHHg%2fVkwfiPzQihI%2fAAAAAAAAq2w%2fhxBh0S_eU3A%2fs1600%2fQuail-Bird-Eggs-(4).jpg&ehk=NmS88wB52fy2LF8VygVGin9Yh5Nn9arRYJNlwN1WaGQ%3d&risl=&pid=ImgRaw&r=0" alt="Logo">
        </div>
        <span>Escalona's Farm</span>
    </a>
    <a href="{{ url('/') }}" class="nav-back">← Back to Home</a>
</nav>

<header class="hero">
    <div class="hero-content">
        <h1>Our Premium Quail Products</h1>
        <p>Fresh, quality products from our smart IoT-managed farm in Naujan, Oriental Mindoro.</p>
    </div>
</header>

<main class="main-container">
    <section>
        <div class="section-header">
            <div class="section-icon">📢</div>
            <h2>Product Catalog</h2>
        </div>
        <div class="products-grid" id="productsGrid">
            <div style="text-align: center; padding: 2rem; color: #8d6e63;">
                🔄 Loading products...
            </div>
        </div>
    </section>
</main>

<!-- Price Edit Modal -->
<div class="edit-modal" id="priceEditModal">
    <div class="edit-content">
        <div class="edit-header">Edit Price</div>
        <input type="number" class="edit-input" id="priceInput" step="0.01" min="0" placeholder="Enter new price">
        <div class="edit-buttons">
            <button class="btn btn-secondary" onclick="closeModal('priceEditModal')">Cancel</button>
            <button class="btn btn-primary" onclick="savePrice()">Save</button>
        </div>
    </div>
</div>

<!-- Stock Edit Modal -->
<div class="edit-modal" id="stockEditModal">
    <div class="edit-content">
        <div class="edit-header">Edit Stock</div>
        <input type="number" class="edit-input" id="stockInput" min="0" placeholder="Enter stock quantity">
        <div class="edit-buttons">
            <button class="btn btn-secondary" onclick="closeModal('stockEditModal')">Cancel</button>
            <button class="btn btn-primary" onclick="saveStock()">Save</button>
        </div>
    </div>
</div>

<script>
let currentProductId = null;

// Load products dynamically
async function loadProducts() {
    try {
        const response = await fetch('/api/products');
        if (!response.ok) {
            throw new Error('Failed to load products');
        }
        
        const products = await response.json();
        renderProducts(products);
    } catch (error) {
        console.error('Error loading products:', error);
        // Use fallback products
        const fallbackProducts = [
            { id: 1, name: 'Fresh Quail Eggs', slug: 'quail_eggs', unit: 'tray (24 pieces)', price: 80, stock: 50, description: 'Fresh quail eggs from our farm', image_url: 'https://www.instacart.com/company/wp-content/uploads/2023/01/quail-eggs.jpg' },
            { id: 2, name: 'Live Quail', slug: 'live_quail', unit: 'piece', price: 180, stock: 25, description: 'Healthy live quail for breeding or raising', image_url: 'https://media.istockphoto.com/id/1289671737/photo/young-quail-isolated-on-white-background.jpg?s=170667a&w=0&k=20&c=o2qMiqHdhcA74EuXQv1XKm1yfVnZiKzjGE6vtIx45ME=' },
            { id: 3, name: 'Dressed Quail', slug: 'dressed_quail', unit: 'piece (cleaned)', price: 250, stock: 15, description: 'Cleaned and dressed quail ready for cooking', image_url: 'http://wbldc.in/wp-content/uploads/2021/03/quail.jpg' }
        ];
        renderProducts(fallbackProducts);
    }
}

// Render products
function renderProducts(products) {
    const grid = document.getElementById('productsGrid');
    if (!grid) return;
    
    grid.innerHTML = products.map(product => {
        const stockClass = getStockClass(product.stock);
        const stockText = product.stock === 0 ? 'Out of Stock' : 
                         product.stock <= 5 ? 'Low Stock' : 'In Stock';
        
        return `
            <div class="product-card">
                <div class="product-img-wrapper">
                    <img src="${product.image_url}" alt="${product.name}">
                </div>
                <div class="product-content">
                    <div class="product-name">${product.name}</div>
                    <div class="product-description">${product.description || ''}</div>
                    <div class="product-unit">Per ${product.unit}</div>
                    
                    <div class="product-footer">
                        <div style="display: flex; justify-content: space-between; align-items: center; width: 100%;">
                            <div class="product-price" onclick="editPrice(${product.id}, ${product.price})">
                                ₱${parseFloat(product.price).toFixed(2)}
                            </div>
                            <div class="stock-badge ${stockClass}" id="stock-${product.id}">
                                Stock: ${product.stock}
                            </div>
                        </div>
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 0.5rem;">
                            <span style="font-size: 0.8rem; font-weight: 700; color: ${product.stock === 0 ? '#f44336' : product.stock <= 5 ? '#ff9800' : '#4caf50'};">
                                ${product.stock === 0 ? '❌ Out of Stock' : product.stock <= 5 ? '⚠️ Low Stock' : '✅ In Stock'}
                            </span>
                            <div class="stock-edit" onclick="editStock(${product.id}, ${product.stock})">
                                Edit Stock
                            </div>
                        </div>
                    </div>
                    
                    <button class="order-btn" onclick="orderProduct('${product.slug}')" style="width: 100%; margin-top: 1rem;">
                        Order Now
                    </button>
                </div>
            </div>
        `;
    }).join('');
}

// Get stock class for styling
function getStockClass(stock) {
    if (stock === 0) return 'out';
    if (stock <= 5) return 'low';
    return '';
}

function editPrice(productId, currentPrice) {
    currentProductId = productId;
    document.getElementById('priceInput').value = currentPrice;
    document.getElementById('priceEditModal').style.display = 'flex';
}

function editStock(productId, currentStock) {
    currentProductId = productId;
    document.getElementById('stockInput').value = currentStock;
    document.getElementById('stockEditModal').style.display = 'flex';
}

function closeModal(modalId) {
    document.getElementById(modalId).style.display = 'none';
    currentProductId = null;
}

async function savePrice() {
    const newPrice = document.getElementById('priceInput').value;
    
    try {
        const response = await fetch(`/products/${currentProductId}/price`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ price: newPrice })
        });
        
        const result = await response.json();
        
        if (result.success) {
            closeModal('priceEditModal');
            loadProducts();
            alert('Price updated successfully!');
        } else {
            alert('Error updating price');
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Error updating price');
    }
}

async function saveStock() {
    const newStock = document.getElementById('stockInput').value;
    
    try {
        const response = await fetch(`/products/${currentProductId}/stock`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ stock: newStock })
        });
        
        const result = await response.json();
        
        if (result.success) {
            closeModal('stockEditModal');
            loadProducts();
            alert('Stock updated successfully!');
        } else {
            alert('Error updating stock');
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Error updating stock');
    }
}

function orderProduct(productSlug) {
    window.location.href = `/order?product=${productSlug}`;
}

// Load products on page load
document.addEventListener('DOMContentLoaded', loadProducts);
</script>

</body>
</html>