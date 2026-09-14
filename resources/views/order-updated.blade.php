<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Order | Escalona's Farm</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Inter', sans-serif; background: #f8f5f2; min-height: 100vh; color: #4e342e; }

        /* NAV */
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

        /* HERO */
        .hero {
            background: linear-gradient(135deg, rgba(109,76,65,0.95) 0%, rgba(62,39,35,0.98) 100%), 
                        url('https://images.unsplash.com/photo-1569288052389-dac9b01c9c05?w=1200') center/cover no-repeat;
            padding: 4rem 2rem;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        .hero-content { position: relative; z-index: 1; max-width: 600px; margin: 0 auto; }
        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background: rgba(255,224,178,0.15);
            border: 1px solid rgba(255,224,178,0.3);
            padding: 0.5rem 1rem;
            border-radius: 50px;
            font-size: 0.8rem;
            font-weight: 600;
            color: #ffe0b2;
            margin-bottom: 1rem;
            backdrop-filter: blur(10px);
        }
        .hero h1 { 
            font-size: clamp(1.8rem, 5vw, 2.8rem); 
            font-weight: 900; 
            color: #fff; 
            margin-bottom: 0.75rem;
            letter-spacing: -1px;
            line-height: 1.2;
        }
        .hero p { color: rgba(255,224,178,0.9); font-size: 1.05rem; line-height: 1.7; }

        /* MAIN CONTAINER */
        .main-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2.5rem 1.5rem 3rem;
        }

        /* PRODUCT CARDS */
        .products-section { margin-bottom: 2.5rem; }
        .section-header {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin-bottom: 1.25rem;
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
            grid-template-columns: repeat(3, 1fr);
            gap: 1.25rem;
        }
        @media(max-width: 768px) { .products-grid { grid-template-columns: 1fr; } }
        .product-card {
            background: #fff;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(109,76,65,0.08);
            border: 1px solid rgba(215,204,200,0.5);
            transition: all 0.3s ease;
            position: relative;
        }
        .product-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 32px rgba(109,76,65,0.15);
        }
        .product-img-wrapper {
            height: 140px;
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
        .product-card:hover .product-img-wrapper img { transform: scale(1.05); }
        .product-content { padding: 1.25rem; }
        .product-name {
            font-size: 1rem;
            font-weight: 700;
            color: #4e342e;
            margin-bottom: 0.35rem;
        }
        .product-unit {
            font-size: 0.8rem;
            color: #8d6e63;
            margin-bottom: 0.75rem;
        }
        .product-price {
            display: inline-flex;
            align-items: center;
            background: linear-gradient(135deg, #6d4c41 0%, #5d4037 100%);
            color: #fff;
            padding: 0.4rem 0.85rem;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 800;
            cursor: pointer;
            position: relative;
        }
        .product-price:hover { background: linear-gradient(135deg, #5d4037 0%, #4e342e 100%); }
        .stock-badge {
            position: absolute;
            top: 10px;
            right: 10px;
            background: #4caf50;
            color: white;
            padding: 0.25rem 0.5rem;
            border-radius: 12px;
            font-size: 0.7rem;
            font-weight: 600;
        }
        .stock-badge.low { background: #ff9800; }
        .stock-badge.out { background: #f44336; }
        .confirm-modal-overlay {
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
        .confirm-modal-overlay.show {
            display: flex;
        }
        .confirm-modal {
            background: white;
            padding: 2rem;
            border-radius: 16px;
            width: 90%;
            max-width: 500px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
        }
        .confirm-modal-title {
            font-size: 1.2rem;
            font-weight: 700;
            color: #4e342e;
            margin-bottom: 1rem;
        }
        .confirm-modal-buttons {
            display: flex;
            gap: 1rem;
            justify-content: flex-end;
        }
        .confirm-modal-btn {
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
        }
        .confirm-modal-btn.cancel {
            background: #e0e0e0;
            color: #666;
        }
        .confirm-modal-btn.confirm {
            background: #6d4c41;
            color: white;
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
        <div class="hero-badge">🥚 Fresh from our farm</div>
        <h1>Order Fresh Quail Products</h1>
        <p>Premium quail eggs and quality-raised quail from our smart IoT-managed farm in Naujan, Oriental Mindoro.</p>
    </div>
</header>

<main class="main-container">
    <!-- PRODUCTS -->
    <section class="products-section">
        <div class="section-header">
            <div class="section-icon">🛒</div>
            <h2>Our Products & Prices</h2>
        </div>
        <div class="products-grid" id="productsGrid">
            <!-- Products will be loaded here -->
        </div>
    </section>
</main>

<!-- Price Edit Modal -->
<div class="confirm-modal-overlay" id="priceEditModal">
    <div class="confirm-modal">
        <div class="confirm-modal-title">Edit Price</div>
        <input type="number" class="price-input" id="priceInput" step="0.01" min="0" style="width: 100%; padding: 0.75rem; border: 2px solid #e8e0dc; border-radius: 8px; font-size: 1rem; margin-bottom: 1rem; box-sizing: border-box;">
        <div class="confirm-modal-buttons">
            <button class="confirm-modal-btn cancel" onclick="closePriceModal()">Cancel</button>
            <button class="confirm-modal-btn confirm" onclick="savePrice()">Save</button>
        </div>
    </div>
</div>

<script>
let currentProductId = null;

// Load products
async function loadProducts() {
    try {
        const response = await fetch('/api/products');
        const products = await response.json();
        
        const grid = document.getElementById('productsGrid');
        grid.innerHTML = products.map(product => `
            <div class="product-card" onclick="selectProduct('${product.slug}')" style="cursor: pointer;">
                <div class="product-img-wrapper">
                    <img src="${product.image_url}" alt="${product.name}">
                </div>
                <div class="product-content">
                    <div class="product-name">${product.name}</div>
                    <div class="product-unit">Per ${product.unit}</div>
                    <div class="product-price" onclick="editPrice(event, ${product.id}, ${product.price})">
                        ₱${parseFloat(product.price).toFixed(2)}
                    </div>
                </div>
                <div class="stock-badge ${getStockClass(product.stock)}">
                    Stock: ${product.stock}
                </div>
            </div>
        `).join('');
    } catch (error) {
        console.error('Error loading products:', error);
    }
}

function getStockClass(stock) {
    if (stock === 0) return 'out';
    if (stock <= 5) return 'low';
    return '';
}

function editPrice(event, productId, currentPrice) {
    event.stopPropagation();
    currentProductId = productId;
    document.getElementById('priceInput').value = currentPrice;
    document.getElementById('priceEditModal').classList.add('show');
}

function closePriceModal() {
    document.getElementById('priceEditModal').classList.remove('show');
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
            closePriceModal();
            loadProducts(); // Reload products
            alert('Price updated successfully!');
        } else {
            alert('Error updating price');
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Error updating price');
    }
}

function selectProduct(productSlug) {
    // Existing product selection logic
    console.log('Selected product:', productSlug);
}

// Load products on page load
document.addEventListener('DOMContentLoaded', loadProducts);
</script>

</body>
</html>