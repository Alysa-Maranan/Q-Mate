<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Products | Escalona's Farm</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Poppins', 'Inter', sans-serif; background: #fffdfa; color: #4e342e; }

        /* NAV */
        .nav {
            background: linear-gradient(135deg, #fffdfa 0%, #f5eee6 100%);
            border-bottom: 2px solid #d7ccc8;
            padding: 1rem 3rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 4px 20px rgba(121,85,72,0.08);
        }
        .nav-logo { display: flex; align-items: center; gap: 0.75rem; text-decoration: none; }
        .nav-logo img { width: 45px; height: 45px; border-radius: 12px; object-fit: cover; border: 2px solid #a1887f; box-shadow: 0 2px 8px rgba(121,85,72,0.18); }
        .nav-logo span { font-size: 1.35rem; font-weight: 800; color: #6d4c41; letter-spacing: 0.5px; }
        .nav-links { display: flex; gap: 0.5rem; align-items: center; }
        .nav-links a { padding: 0.6rem 1.25rem; border-radius: 10px; font-weight: 600; font-size: 0.95rem; text-decoration: none; color: #6d4c41; transition: all 0.2s; display: inline-flex; align-items: center; gap: 0.5rem; border: 1.5px solid #d7ccc8; }
        .nav-links a:hover { background: #efebe9; border-color: #a1887f; }

        /* HEADER */
        .header { background: linear-gradient(135deg, #6d4c41 0%, #4e342e 100%); padding: 4rem 2rem 3rem; text-align: center; color: white; }
        .header h1 { font-size: 2.5rem; font-weight: 800; margin-bottom: 1rem; }
        .header p { font-size: 1.1rem; opacity: 0.9; max-width: 600px; margin: 0 auto; }

        /* MAIN */
        .main { padding: 4rem 2rem; max-width: 1200px; margin: 0 auto; }
        .products-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(350px, 1fr)); gap: 2rem; }
        
        .product-card {
            background: white;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 8px 32px rgba(161,136,127,0.12);
            border: 1px solid rgba(161,136,127,0.1);
            transition: all 0.3s;
        }
        .product-card:hover { transform: translateY(-8px); box-shadow: 0 16px 48px rgba(161,136,127,0.2); }
        
        .product-image {
            height: 200px;
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            border-bottom: 1px solid rgba(161,136,127,0.1);
        }
        
        .product-content { padding: 2rem; }
        .product-title { font-size: 1.5rem; font-weight: 700; color: #6d4c41; margin-bottom: 0.5rem; }
        .product-price { font-size: 1.8rem; font-weight: 800; color: #a1887f; margin-bottom: 1rem; }
        .product-description { color: #8d6e63; line-height: 1.6; margin-bottom: 1.5rem; }
        
        .order-btn {
            width: 100%;
            padding: 1rem;
            background: linear-gradient(135deg, #a1887f 0%, #6d4c41 100%);
            color: white;
            border: none;
            border-radius: 12px;
            font-weight: 700;
            font-size: 1rem;
            cursor: pointer;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-block;
            text-align: center;
        }
        .order-btn:hover { transform: translateY(-2px); box-shadow: 0 8px 24px rgba(161,136,127,0.3); }

        /* FOOTER */
        .footer { background: #4e342e; color: #ffe0b2; padding: 2.5rem 2rem 1.5rem; }
        .footer-inner { max-width: 1200px; margin: 0 auto; display: grid; grid-template-columns: 2fr 1fr 1fr 1.5fr; gap: 2.5rem; padding-bottom: 2rem; border-bottom: 1px solid rgba(255,224,178,0.15); }
        .footer-brand-name { font-size: 1.2rem; font-weight: 800; color: #ffe0b2; margin-bottom: 0.5rem; }
        .footer-brand-desc { font-size: 0.85rem; color: rgba(255,224,178,0.7); line-height: 1.6; margin-bottom: 1rem; }
        .footer-col-title { font-size: 0.85rem; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; color: #ffe0b2; margin-bottom: 0.85rem; }
        .footer-col a { display: block; color: rgba(255,224,178,0.7); text-decoration: none; font-size: 0.85rem; margin-bottom: 0.5rem; transition: color 0.2s; }
        .footer-col a:hover { color: #fffdfa; }
        .footer-contact-line { font-size: 0.85rem; color: rgba(255,224,178,0.7); margin-bottom: 0.5rem; display: flex; align-items: flex-start; gap: 0.5rem; }
        .footer-contact-line a { color: rgba(255,224,178,0.85); text-decoration: none; }
        .footer-contact-line a:hover { color: #fffdfa; }
        .footer-bottom { max-width: 1200px; margin: 1.25rem auto 0; text-align: center; font-size: 0.8rem; color: rgba(255,224,178,0.5); }

        @media (max-width: 768px) {
            .nav { padding: 1rem 1.5rem; }
            .header { padding: 3rem 1.5rem 2rem; }
            .header h1 { font-size: 2rem; }
            .main { padding: 3rem 1.5rem; }
            .products-grid { grid-template-columns: 1fr; }
        }
        @media (max-width: 600px) {
            .nav { padding: 0.75rem 1rem; }
            .nav-logo span { font-size: 1.1rem; }
            .nav-links a { padding: 0.5rem 1rem; font-size: 0.85rem; }
        }

        /* PRODUCT MODAL */
        .product-modal { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center; }
        .product-modal.active { display: flex !important; }
        .product-modal-content { background: white; border-radius: 16px; max-width: 900px; width: 90%; max-height: 90vh; overflow-y: auto; position: relative; display: flex; flex-direction: column; }
        .product-modal-close { position: absolute; top: 1rem; right: 1rem; width: 36px; height: 36px; border-radius: 50%; background: #f5f0eb; border: none; cursor: pointer; font-size: 1.2rem; display: flex; align-items: center; justify-content: center; z-index: 10; }
        .product-modal-body { display: grid; grid-template-columns: 1fr 1fr; }
        .product-modal-image { height: 350px; background-size: cover; background-position: center; border-radius: 16px 0 0 16px; }
        .product-modal-info { padding: 2rem; }
        .product-modal-title { font-size: 1.5rem; font-weight: 700; color: #6d4c41; margin-bottom: 0.5rem; }
        .product-modal-price { font-size: 1.8rem; font-weight: 800; color: #a1887f; margin-bottom: 1rem; }
        .product-modal-desc { color: #8d6e63; line-height: 1.6; margin-bottom: 1.5rem; }
        .product-modal-order-btn { width: 100%; padding: 1rem; background: linear-gradient(135deg, #a1887f 0%, #6d4c41 100%); color: white; border: none; border-radius: 12px; font-weight: 700; font-size: 1rem; cursor: pointer; transition: all 0.3s; }

        /* REVIEWS SECTION */
        .reviews-section { padding: 2rem; border-top: 1px solid #f0f0f0; }
        .reviews-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.5rem; }
        .reviews-title { font-size: 1.1rem; font-weight: 700; color: #4e342e; }
        .reviews-count { font-size: 0.9rem; color: #8d6e63; }
        .reviews-summary { display: flex; align-items: center; gap: 1rem; background: #fff8e1; padding: 1rem 1.5rem; border-radius: 12px; margin-bottom: 1.5rem; }
        .reviews-score { font-size: 2rem; font-weight: 800; color: #ffc107; }
        .reviews-stars { display: flex; gap: 2px; }
        .reviews-stars i { color: #e0e0e0; font-size: 1rem; }
        .reviews-stars i.filled { color: #ffc107; }
        .reviews-bars { flex: 1; }
        .reviews-bar-row { display: flex; align-items: center; gap: 0.5rem; font-size: 0.8rem; color: #8d6e63; }
        .reviews-bar { flex: 1; height: 8px; background: #e0e0e0; border-radius: 4px; overflow: hidden; }
        .reviews-bar-fill { height: 100%; background: #ffc107; border-radius: 4px; }
        .review-card { padding: 1rem 0; border-bottom: 1px solid #f0f0f0; }
        .review-card:last-child { border-bottom: none; }
        .review-header { display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.5rem; }
        .review-avatar { width: 32px; height: 32px; border-radius: 50%; background: #6d4c41; color: white; display: flex; align-items: center; justify-content: center; font-size: 0.8rem; font-weight: 600; }
        .review-name { font-weight: 600; color: #4e342e; font-size: 0.9rem; }
        .review-date { font-size: 0.75rem; color: #a1887f; }
        .review-stars { color: #ffc107; font-size: 0.8rem; display: flex; gap: 1px; }
        .review-text { color: #5d4037; font-size: 0.9rem; line-height: 1.5; margin-top: 0.5rem; }
        .review-variant { font-size: 0.8rem; color: #8d6e63; margin-top: 0.25rem; }
        @media (max-width: 768px) {
            .product-modal-body { grid-template-columns: 1fr; }
            .product-modal-image { height: 250px; border-radius: 16px 16px 0 0; }
        }
    </style>
</head>
<body>

<!-- NAV -->
<nav class="nav">
    <a href="{{ url('/') }}" class="nav-logo">
        <img src="https://th.bing.com/th/id/R.2f6964b62e896f5f94f40390e330a1f8?rik=xmu%2bEOduizkGYw&riu=http%3a%2f%2f1.bp.blogspot.com%2f-unYxCWFMHHg%2fVkwfiPzQihI%2fAAAAAAAAq2w%2fhxBh0S_eU3A%2fs1600%2fQuail-Bird-Eggs-(4).jpg&ehk=NmS88wB52fy2LF8VygVGin9Yh5Nn9arRYJNlwN1WaGQ%3d&risl=&pid=ImgRaw&r=0" alt="Escalona's Farm">
        <span>Escalona's Farm</span>
    </a>
    <div class="nav-links">
        <a href="{{ url('/') }}">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" width="16" height="16" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Back to Home
        </a>
    </div>
</nav>

<!-- HEADER -->
<section class="header">
    <h1>Our Fresh Products</h1>
    <p>Premium quality quail products delivered fresh to your doorstep.<br>All products are carefully selected and prepared with the highest standards.</p>
</section>

<!-- MAIN PRODUCTS -->
<main class="main">
    <div class="products-grid">

        <!-- FRESH QUAIL EGGS -->
        <div class="product-card">
            <div class="product-image" style="background-image: url('https://d2gg9evh47fn9z.cloudfront.net/1600px_COLOURBOX7106353.jpg');"></div>
            <div class="product-content">
                <h2 class="product-title">Fresh Quail Eggs</h2>
                <div class="product-price">₱160 <span style="font-size: 0.6em; color: #8d6e63;">per tray</span></div>
                <p class="product-description">Premium fresh quail eggs collected daily from our farm.<br>Each tray contains 24 pieces of carefully selected, high-quality eggs<br>delivered within 24-48 hours.</p>

                <button class="order-btn" onclick="showProductDetail('eggs')">View Details</button>
            </div>
        </div>

        <!-- LIVE QUAIL -->
        <div class="product-card">
            <div class="product-image" style="background-image: url('https://media.istockphoto.com/id/516262976/photo/coturnix-on-a-white-background.jpg?s=612x612&w=0&k=20&c=mXuboIkTnVA1is46_EqA9rrLk-2gvBGgq-5cjWTLR9U=');"></div>
            <div class="product-content">
                <h2 class="product-title">Live Quail</h2>
                <div class="product-price">₱180 <span style="font-size: 0.6em; color: #8d6e63;">per piece</span></div>
                <p class="product-description">Healthy, active quail birds perfect for breeding or raising.<br>All birds are health-checked and ready for transport<br>with proper care instructions provided.</p>

                <button class="order-btn" onclick="showProductDetail('quail')">View Details</button>
            </div>
        </div>

        <!-- DRESSED QUAIL -->
        <div class="product-card">
            <div class="product-image" style="background-image: url('https://wbldc.in/wp-content/uploads/2021/03/quail.jpg');"></div>
            <div class="product-content">
                <h2 class="product-title">Dressed Quail</h2>
                <div class="product-price">₱250 <span style="font-size: 0.6em; color: #8d6e63;">per piece</span></div>
                <p class="product-description">Freshly cleaned and dressed quail ready for cooking.<br>Professionally processed with proper hygiene standards<br>and vacuum-sealed packaging.</p>

                <button class="order-btn" onclick="showProductDetail('dressed')">View Details</button>
            </div>
        </div>

    </div>
</main>

<!-- PRODUCT MODAL -->
<div class="product-modal" id="productModal">
    <div class="product-modal-content">
        <button class="product-modal-close" onclick="closeProductModal()">&times;</button>
        <div class="product-modal-body">
            <div class="product-modal-image" id="modalImage"></div>
            <div class="product-modal-info">
                <h2 class="product-modal-title" id="modalTitle"></h2>
                <div class="product-modal-price" id="modalPrice"></div>
                <p class="product-modal-desc" id="modalDesc"></p>
                <button class="product-modal-order-btn" onclick="goToOrder()">Order Now</button>
            </div>
        </div>
        <div class="reviews-section">
            <div class="reviews-header">
                <span class="reviews-title">Customer Reviews</span>
                <span class="reviews-count" id="reviewsCount"></span>
            </div>
            <div class="reviews-summary">
                <div class="reviews-score" id="reviewsScore">4.8</div>
                <div class="reviews-stars" id="reviewsStars"></div>
                <div class="reviews-bars" id="reviewsBars"></div>
            </div>
            <div id="reviewsList"></div>
        </div>
    </div>
</div>

<!-- FOOTER -->
<footer class="footer">
    <div class="footer-inner">
        <div>
            <div class="footer-brand-name">Escalona's Farm</div>
            <div class="footer-brand-desc">An IoT-powered smart quail management system designed to make farm life easier and more efficient.</div>
        </div>
        <div class="footer-col">
            <div class="footer-col-title">Quick Links</div>
            <a href="{{ url('/') }}">Home</a>
            <a href="{{ url('/about') }}">About</a>
            <a href="{{ route('products') }}">Products</a>
            <a href="{{ route('contact.support') }}">Contact</a>
        </div>
        <div class="footer-col">
            <div class="footer-col-title">Account</div>
            <a href="{{ route('login') }}">Login</a>
            <a href="{{ route('register') }}">Sign Up</a>
            <a href="{{ route('settings') }}">Settings</a>
        </div>
        <div class="footer-col">
            <div class="footer-col-title">Contact Info</div>
            <div class="footer-contact-line">📧 <a href="mailto:escalona.farm@gmail.com">escalona.farm@gmail.com</a></div>
            <div class="footer-contact-line">📞 <a href="tel:+639171234567">+63 917 123 4567</a></div>
            <div class="footer-contact-line">📍 Pagkakaisa, Naujan, Oriental Mindoro</div>
        </div>
    </div>
    <div class="footer-bottom">&copy; {{ date('Y') }} Escalona's Farm. All rights reserved.</div>
</footer>

<script>
// Product data with reviews
const productsData = {
    eggs: {
        name: 'Fresh Quail Eggs',
        price: '₱160 <span style="font-size: 0.6em; color: #8d6e63;">per tray</span>',
        image: "url('https://d2gg9evh47fn9z.cloudfront.net/1600px_COLOURBOX7106353.jpg')",
        desc: 'Premium fresh quail eggs collected daily from our farm. Each tray contains 24 pieces of carefully selected, high-quality eggs delivered within 24-48 hours.',
        reviews: [
            { name: 'Maria S.', avatar: 'M', date: '2 days ago', rating: 5, text: 'Super fresh eggs! Laging sariwa ang delivery. Highly recommended!', variant: 'Variant: 24pcs Tray' },
            { name: 'Juan P.', avatar: 'J', date: '1 week ago', rating: 5, text: 'Ang sarap ng itlog ng quail dito. Masarap mag scramble eggs.' },
            { name: 'Ana L.', avatar: 'A', date: '2 weeks ago', rating: 4, text: 'Okay naman, medyo marami pang late deliveries dati pero ngayon mas okay na.' },
            { name: 'Carlos D.', avatar: 'C', date: '3 weeks ago', rating: 5, text: 'Best quality quail eggs in town! Order na namin to lagi.' }
        ]
    },
    quail: {
        name: 'Live Quail',
        price: '₱180 <span style="font-size: 0.6em; color: #8d6e63;">per piece</span>',
        image: "url('https://media.istockphoto.com/id/516262976/photo/coturnix-on-a-white-background.jpg?s=612x612&w=0&k=20&c=mXuboIkTnVA1is46_EqA9rrLk-2gvBGgq-5cjWTLR9U=')",
        desc: 'Healthy, active quail birds perfect for breeding or raising. All birds are health-checked and ready for transport with proper care instructions provided.',
        reviews: [
            { name: 'Pedro M.', avatar: 'P', date: '1 week ago', rating: 5, text: 'Malulusog ang quail na natanggap ko. Healthy pa at active. Salamat!', variant: 'Variant: Live Adult' },
            { name: 'Lisa R.', avatar: 'L', date: '2 weeks ago', rating: 5, text: 'Great for breeding! May kasamang care instructions na malinaw.' },
            { name: 'Rico T.', avatar: 'R', date: '1 month ago', rating: 4, text: 'Descent quality quails. Medyo nabawasan sa travel pero overall okay naman.' }
        ]
    },
    dressed: {
        name: 'Dressed Quail',
        price: '₱250 <span style="font-size: 0.6em; color: #8d6e63;">per piece</span>',
        image: "url('https://wbldc.in/wp-content/uploads/2021/03/quail.jpg')",
        desc: 'Freshly cleaned and dressed quail ready for cooking. Professionally processed with proper hygiene standards and vacuum-sealed packaging.',
        reviews: [
            { name: 'Chef Marco.', avatar: 'M', date: '3 days ago', rating: 5, text: 'Super linis ng dressing! Perfect forRestaurant use. Masarap sa grill.', variant: 'Variant: Vacuum Sealed' },
            { name: 'Sarah G.', avatar: 'S', date: '5 days ago', rating: 5, text: 'Super convenient! Dressed na sya so pwede na agad lutuin. Masarap!', variant: 'Variant: 2pcs Pack' },
            { name: 'Manny V.', avatar: 'M', date: '1 week ago', rating: 5, text: 'Ginamit ko sa sisig. Ang sarap! Delivery pa mismo sa bahay.' },
            { name: 'Ellen K.', avatar: 'E', date: '2 weeks ago', rating: 4, text: 'Okay naman overall. Medyo mas mataas ang price pero worth it naman.' },
            { name: 'Tony B.', avatar: 'T', date: '3 weeks ago', rating: 5, text: 'Best dressed quail I've tried! Wala kang maramdaman ang lasa.' }
        ]
    }
};

function showProductDetail(productKey) {
    console.log('showProductDetail called with:', productKey);
    const product = productsData[productKey];
    if (!product) { alert('Product not found: ' + productKey); return; }

    document.getElementById('modalImage').style.backgroundImage = product.image;
    document.getElementById('modalTitle').textContent = product.name;
    document.getElementById('modalPrice').innerHTML = product.price;
    document.getElementById('modalDesc').textContent = product.desc;

    // Show stars
    const avgRating = 4.8;
    let starsHtml = '';
    for (let i = 1; i <= 5; i++) {
        starsHtml += `<i class="ph-bold ph-star" style="${i <= Math.round(avgRating) ? 'color:#ffc107;' : 'color:#e0e0e0;'}"></i>`;
    }
    document.getElementById('reviewsStars').innerHTML = starsHtml;

    // Show rating bars
    let barsHtml = '';
    [5, 4, 3, 2, 1].forEach(stars => {
        const percentage = stars === 5 ? 75 : stars === 4 ? 15 : stars === 3 ? 6 : stars === 2 ? 3 : 1;
        barsHtml += `
            <div class="reviews-bar-row">
                <span>${stars}</span><i class="ph-bold ph-star" style="color:#ffc107;font-size:0.7rem;"></i>
                <div class="reviews-bar"><div class="reviews-bar-fill" style="width:${percentage}%;"></div></div>
                <span>${percentage}%</span>
            </div>`;
    });
    document.getElementById('reviewsBars').innerHTML = barsHtml;

    // Show reviews count
    document.getElementById('reviewsCount').textContent = `${product.reviews.length} reviews`;

    // Show reviews list
    let reviewsHtml = '';
    product.reviews.forEach(review => {
        let starsReview = '';
        for (let i = 1; i <= 5; i++) {
            starsReview += `<i class="ph-bold ph-star" style="${i <= review.rating ? 'color:#ffc107;' : 'color:#e0e0e0;'}"></i>`;
        }
        reviewsHtml += `
            <div class="review-card">
                <div class="review-header">
                    <div class="review-avatar">${review.avatar}</div>
                    <div>
                        <div class="review-name">${review.name}</div>
                        <div class="review-date">${review.date}</div>
                    </div>
                    <div class="review-stars">${starsReview}</div>
                </div>
                <div class="review-text">${review.text}</div>
                ${review.variant ? `<div class="review-variant">${review.variant}</div>` : ''}
            </div>`;
    });
    document.getElementById('reviewsList').innerHTML = reviewsHtml;

    document.getElementById('productModal').classList.add('active');
    document.body.style.overflow = 'hidden';
}

function closeProductModal() {
    document.getElementById('productModal').classList.remove('active');
    document.body.style.overflow = 'auto';
}

function goToOrder() {
    window.location.href = '{{ route('order') }}';
}

// Close modal on outside click
document.getElementById('productModal').addEventListener('click', function(e) {
    if (e.target === this) closeProductModal();
});
</script>

</body>
</html>