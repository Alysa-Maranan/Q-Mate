@extends('customer.layout')
@section('page-title', 'Browse Products')
@section('active-nav', 'browse')
@section('header-action')
<style>
.content-header .content-title { display: none; }
</style>
@endsection
@section('styles')
<style>
@keyframes spin { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }
@keyframes modalSlideIn { from { opacity: 0; transform: translateY(-30px) scale(0.95); } to { opacity: 1; transform: translateY(0) scale(1); } }
.page-header { padding: 2rem; text-align: center; border-bottom: 1px solid #efebe9; }
.page-title { font-size: 2rem; font-weight: 700; color: #4e342e; margin-bottom: 0.5rem; }
.page-subtitle { text-align: center; color: #8d6e63; }
.products-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1.5rem; padding: 2rem; align-items: stretch; max-width: 1200px; margin: 0 auto; }
.products-grid.search-mode { grid-template-columns: repeat(auto-fit, minmax(250px, 320px)); justify-content: center; }
.product-card { background: white; border-radius: 16px; overflow: hidden; border: 1px solid rgba(161,136,127,0.2); border-bottom: 5px solid #6d4c41; transition: all 0.3s ease; display: flex; flex-direction: column; max-width: 350px; margin: 0 auto; width: 100%; }
.product-card:hover { transform: translateY(-5px); box-shadow: 0 15px 35px rgba(109,76,65,0.15); border-bottom-color: #6d4c41; }
.product-img { height: 180px; overflow: hidden; position: relative; flex-shrink: 0; }
.product-img img { width: 100%; height: 100%; object-fit: cover; transition: transform 0.5s ease; }
.product-card:hover .product-img img { transform: scale(1.05); }
.product-badge { position: absolute; top: 12px; right: 12px; padding: 0.35rem 0.75rem; border-radius: 8px; font-weight: 700; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.5px; box-shadow: 0 4px 12px rgba(0,0,0,0.15); }
.product-badge.bestseller { background: linear-gradient(135deg, #ff6b6b 0%, #ee5a6f 100%); color: white; }
.product-badge.available { background: linear-gradient(135deg, #51cf66 0%, #37b24d 100%); color: white; }
.product-badge.limited { background: linear-gradient(135deg, #ffd43b 0%, #f59f00 100%); color: #333; }
.product-content { padding: 1.25rem; display: flex; flex-direction: column; flex-grow: 1; }
.product-title { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 0.5rem; }
.product-title h3 { font-size: 1.1rem; color: #4e342e; }
.product-price { font-weight: 700; color: #6d4c41; font-size: 1.1rem; background: rgba(109,76,65,0.1); padding: 0.25rem 0.7rem; border-radius: 8px; white-space: nowrap; }
.product-desc { color: #795548; font-size: 0.9rem; line-height: 1.5; margin-bottom: 1rem; flex-grow: 1; }
.product-action { width: 100%; margin-top: auto; }
.modal-overlay { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.6); z-index: 2000; align-items: center; justify-content: center; backdrop-filter: blur(5px); }
.modal-overlay.show { display: flex; }
.modal { background: white; padding: 2.5rem; border-radius: 20px; width: 90%; max-width: 500px; max-height: 90vh; overflow-y: auto; box-shadow: 0 25px 60px rgba(0, 0, 0, 0.3); text-align: center; position: relative; animation: modalSlideIn 0.3s ease-out; }
.modal::-webkit-scrollbar { width: 8px; }
.modal::-webkit-scrollbar-track { background: #f5f0eb; border-radius: 10px; }
.modal::-webkit-scrollbar-thumb { background: #a1887f; border-radius: 10px; }
.modal::-webkit-scrollbar-thumb:hover { background: #6d4c41; }
.modal-close { position: absolute; top: 1rem; right: 1rem; background: none; border: none; font-size: 1.5rem; color: #8d6e63; cursor: pointer; padding: 0.5rem; border-radius: 50%; transition: all 0.2s; }
.modal-close:hover { background: #f5f0eb; color: #4e342e; }
.modal-icon { width: 80px; height: 80px; background: linear-gradient(135deg, #6d4c41 0%, #4e342e 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem; font-size: 2.5rem; color: white; box-shadow: 0 8px 25px rgba(109, 76, 65, 0.3); }
.modal-title { font-size: 1.8rem; font-weight: 700; color: #4e342e; margin-bottom: 1.5rem; }
.modal-buttons { display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap; }
.modal-btn { padding: 0.85rem 1.8rem; border: none; border-radius: 12px; font-weight: 600; font-size: 0.95rem; cursor: pointer; transition: all 0.3s; font-family: 'Inter', sans-serif; }
.modal-btn.primary { background: #6d4c41; color: white; box-shadow: 0 4px 15px rgba(109, 76, 65, 0.3); }
.modal-btn.primary:hover { background: #5d4037; transform: translateY(-2px); box-shadow: 0 6px 20px rgba(109, 76, 65, 0.4); }
.modal-btn.secondary { background: #f5f0eb; color: #6d4c41; border: 2px solid rgba(161,136,127,0.2); }
.modal-btn.secondary:hover { background: #efebe9; border-color: #6d4c41; }
.empty-state { text-align: center; padding: 4rem 2rem; color: #8d6e63; }
.empty-state i { font-size: 4rem; margin-bottom: 1rem; opacity: 0.5; }
.empty-state h3 { font-size: 1.2rem; margin-bottom: 0.5rem; color: #4e342e; }
.empty-state p { font-size: 0.9rem; margin-bottom: 1.5rem; }

/* PRODUCT DETAIL MODAL */
.product-detail-modal { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.6); z-index: 2000; align-items: center; justify-content: center; backdrop-filter: blur(4px); }
.product-detail-modal.active { display: flex; }
.product-detail-content { background: white; border-radius: 20px; max-width: 950px; width: 95%; max-height: 92vh; overflow-y: auto; position: relative; box-shadow: 0 25px 60px rgba(0,0,0,0.3); }
.product-detail-content::-webkit-scrollbar { width: 8px; }
.product-detail-content::-webkit-scrollbar-track { background: #f5f0eb; border-radius: 10px; }
.product-detail-content::-webkit-scrollbar-thumb { background: #a1887f; border-radius: 10px; }
.product-detail-close { position: absolute; top: 1rem; right: 1rem; width: 40px; height: 40px; border-radius: 50%; background: #f5f0eb; border: none; cursor: pointer; font-size: 1.5rem; display: flex; align-items: center; justify-content: center; z-index: 10; transition: all 0.2s; color: #6d4c41; }
.product-detail-close:hover { background: #e0d6ce; transform: rotate(90deg); }
.product-detail-body { display: grid; grid-template-columns: 1fr 1fr; }
.product-detail-image { height: 400px; background-size: cover; background-position: center; border-radius: 20px 0 0 0; position: relative; }
.product-detail-badge { position: absolute; top: 1rem; left: 1rem; padding: 0.5rem 1rem; border-radius: 10px; font-weight: 700; font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.5px; color: white; }
.product-detail-badge.bestseller { background: linear-gradient(135deg, #ff6b6b 0%, #ee5a6f 100%); }
.product-detail-badge.available { background: linear-gradient(135deg, #51cf66 0%, #37b24d 100%); }
.product-detail-badge.limited { background: linear-gradient(135deg, #ffd43b 0%, #f59f00 100%); color: #333; }
.product-detail-info { padding: 2.5rem; display: flex; flex-direction: column; }
.product-detail-title { font-size: 1.8rem; font-weight: 700; color: #4e342e; margin-bottom: 0.75rem; line-height: 1.3; }
.product-detail-meta { display: flex; flex-wrap: wrap; gap: 0.75rem; margin-bottom: 1.25rem; }
.product-detail-meta-item { display: flex; align-items: center; gap: 0.4rem; font-size: 0.85rem; color: #8d6e63; background: #f8f5f1; padding: 0.4rem 0.8rem; border-radius: 8px; }
.product-detail-meta-item i { color: #6d4c41; }
.product-detail-price-section { background: linear-gradient(135deg, #6d4c41 0%, #4e342e 100%); padding: 1.25rem; border-radius: 14px; margin-bottom: 1.5rem; }
.product-detail-price { font-size: 2rem; font-weight: 800; color: white; }
.product-detail-price span { font-size: 0.9rem; font-weight: 400; opacity: 0.8; }
.product-detail-stock { display: flex; align-items: center; gap: 0.5rem; margin-top: 0.5rem; font-size: 0.85rem; color: #a5d6a7; }
.product-detail-stock i { font-size: 1rem; }
.product-detail-desc { color: #795548; line-height: 1.7; margin-bottom: 1.5rem; font-size: 0.95rem; }
.product-detail-features { margin-bottom: 1.5rem; }
.product-detail-features h4 { font-size: 0.9rem; color: #4e342e; margin-bottom: 0.75rem; font-weight: 600; }
.product-detail-features ul { list-style: none; padding: 0; margin: 0; }
.product-detail-features li { display: flex; align-items: center; gap: 0.5rem; font-size: 0.85rem; color: #795548; margin-bottom: 0.5rem; }
.product-detail-features li i { color: #51cf66; font-size: 1rem; }
.product-detail-order-btn { width: 100%; padding: 1rem; background: linear-gradient(135deg, #6d4c41 0%, #4e342e 100%); color: white; border: none; border-radius: 12px; font-weight: 700; font-size: 1rem; cursor: pointer; transition: all 0.3s; font-family: 'Inter', sans-serif; display: flex; align-items: center; justify-content: center; gap: 0.5rem; }
.product-detail-order-btn:hover { transform: translateY(-2px); box-shadow: 0 8px 25px rgba(109,76,65,0.4); }
.product-detail-order-btn:disabled { background: #ccc; cursor: not-allowed; transform: none; box-shadow: none; }

/* REVIEWS */
.reviews-section { padding: 1.5rem 2rem; border-top: 1px solid #efebe9; }
.reviews-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 1rem; }
.reviews-title { font-size: 1rem; font-weight: 700; color: #4e342e; }
.reviews-count { font-size: 0.85rem; color: #8d6e63; }
.reviews-summary { display: flex; align-items: center; gap: 1rem; background: #fff8e1; padding: 1rem; border-radius: 12px; margin-bottom: 1rem; }
.reviews-score { font-size: 2rem; font-weight: 800; color: #ffc107; }
.reviews-stars { display: flex; gap: 2px; }
.reviews-stars i { color: #e0e0e0; font-size: 0.9rem; }
.reviews-bars { flex: 1; }
.reviews-bar-row { display: flex; align-items: center; gap: 0.5rem; font-size: 0.75rem; color: #8d6e63; }
.reviews-bar { flex: 1; height: 6px; background: #e0e0e0; border-radius: 3px; overflow: hidden; }
.reviews-bar-fill { height: 100%; background: #ffc107; border-radius: 3px; }
.review-card { padding: 0.85rem 0; border-bottom: 1px solid #efebe9; }
.review-card:last-child { border-bottom: none; }
.review-header { display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.35rem; }
.review-avatar { width: 28px; height: 28px; border-radius: 50%; background: #6d4c41; color: white; display: flex; align-items: center; justify-content: center; font-size: 0.7rem; font-weight: 600; }
.review-name { font-weight: 600; color: #4e342e; font-size: 0.85rem; }
.review-date { font-size: 0.7rem; color: #a1887f; }
.review-stars { color: #ffc107; font-size: 0.75rem; display: flex; gap: 1px; }
.review-text { color: #795548; font-size: 0.85rem; line-height: 1.4; }
.review-variant { font-size: 0.75rem; color: #8d6e63; margin-top: 0.2rem; }
@media (max-width: 768px) {
    .product-detail-body { grid-template-columns: 1fr; }
    .product-detail-image { height: 220px; border-radius: 16px 16px 0 0; }
}
</style>@endsection
@section('customer-content')
<div style="padding: 1.5rem;">
    <!-- Page Header -->
    <div style="background: linear-gradient(135deg, #6d4c41 0%, #4e342e 100%); border-radius: 16px; padding: 1.5rem 2rem; margin-bottom: 2rem; color: white;">
        <h1 style="font-size: 1.5rem; font-weight: 800; margin-bottom: 0.5rem; display: flex; align-items: center; gap: 0.75rem;">
            Our Products
        </h1>
        <p style="font-size: 0.9rem; opacity: 0.9; margin: 0;">Fresh quail products from our smart IoT-managed farm</p>
    </div>

    <div class="products-grid" id="productsGrid">
        <div class="empty-state">
            <i class="ph-bold ph-spinner" style="animation: spin 1s linear infinite;"></i>
            <p>Loading products...</p>
        </div>
    </div>
</div>

<!-- PRODUCT DETAIL MODAL -->
<div class="product-detail-modal" id="productDetailModal">
    <div class="product-detail-content">
        <button class="product-detail-close" onclick="closeProductDetail()">&times;</button>
        <div class="product-detail-body">
            <div class="product-detail-image" id="detailImage">
                <div class="product-detail-badge" id="detailBadge"></div>
            </div>
            <div class="product-detail-info">
                <h2 class="product-detail-title" id="detailTitle"></h2>
                <div class="product-detail-meta">
                    <div class="product-detail-meta-item">
                        <i class="ph-bold ph-package"></i>
                        <span id="detailUnit"></span>
                    </div>
                    <div class="product-detail-meta-item">
                        <i class="ph-bold ph-storefront"></i>
                        <span id="detailStock"></span>
                    </div>
                </div>
                <div class="product-detail-price-section">
                    <div class="product-detail-price" id="detailPrice"></div>
                    <div class="product-detail-stock">
                        <i class="ph-bold ph-check-circle"></i>
                        <span id="detailStockStatus"></span>
                    </div>
                </div>
                <p class="product-detail-desc" id="detailDesc"></p>
                <div class="product-detail-features">
                    <h4>Product Highlights</h4>
                    <ul id="detailFeatures"></ul>
                </div>
                <button class="product-detail-order-btn" id="detailOrderBtn">
                    <i class="ph-bold ph-shopping-cart"></i>
                    Order Now
                </button>
            </div>
        </div>
        <div class="reviews-section">
            <div class="reviews-header">
                <span class="reviews-title">Customer Reviews</span>
                <span class="reviews-count" id="detailReviewsCount"></span>
            </div>
            <div class="reviews-summary">
                <div class="reviews-score">4.8</div>
                <div class="reviews-stars" id="detailReviewsStars"></div>
                <div class="reviews-bars" id="detailReviewsBars"></div>
            </div>
            <div id="detailReviewsList"></div>

            <!-- Add Review Form -->
            <div style="margin-top: 1.5rem; padding-top: 1.5rem; border-top: 1px solid #efebe9;">
                <h4 style="font-size: 0.95rem; color: #4e342e; margin-bottom: 1rem;">Write a Review</h4>
                <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.75rem;">
                    <span style="font-size: 0.85rem; color: #8d6e63;">Your Rating:</span>
                    <div id="reviewStarsInput" style="display: flex; gap: 3px;">
                        <i class="ph-bold ph-star" style="color: #e0e0e0; cursor: pointer; font-size: 1.1rem;" data-rating="1" onclick="setReviewRating(1)"></i>
                        <i class="ph-bold ph-star" style="color: #e0e0e0; cursor: pointer; font-size: 1.1rem;" data-rating="2" onclick="setReviewRating(2)"></i>
                        <i class="ph-bold ph-star" style="color: #e0e0e0; cursor: pointer; font-size: 1.1rem;" data-rating="3" onclick="setReviewRating(3)"></i>
                        <i class="ph-bold ph-star" style="color: #e0e0e0; cursor: pointer; font-size: 1.1rem;" data-rating="4" onclick="setReviewRating(4)"></i>
                        <i class="ph-bold ph-star" style="color: #e0e0e0; cursor: pointer; font-size: 1.1rem;" data-rating="5" onclick="setReviewRating(5)"></i>
                    </div>
                </div>
                <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                    <textarea id="reviewCommentInput" placeholder="Share your experience with this product..." style="width: 100%; padding: 0.75rem; border: 2px solid rgba(161,136,127,0.2); border-radius: 10px; font-family: 'Inter', sans-serif; font-size: 0.85rem; resize: vertical; min-height: 80px; color: #4e342e;" onfocus="this.style.borderColor='#6d4c41';" onblur="this.style.borderColor='rgba(161,136,127,0.2)';"></textarea>
                    <button onclick="submitReview()" id="submitReviewBtn" style="padding: 0.75rem 1.5rem; background: #6d4c41; color: white; border: none; border-radius: 8px; font-weight: 600; font-size: 0.85rem; cursor: pointer; font-family: 'Inter', sans-serif; width: 100%;" onmouseover="this.style.background='#5d4037';" onmouseout="this.style.background='#6d4c41';">Submit Review</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Product reviews data
const productReviews = {
    'fresh-quail-eggs': [
        { name: 'Maria S.', avatar: 'M', date: '2 days ago', rating: 5, text: 'Super fresh eggs! Laging sariwa ang delivery. Highly recommended!', variant: '24pcs Tray' },
        { name: 'Juan P.', avatar: 'J', date: '1 week ago', rating: 5, text: 'Ang sarap ng itlog ng quail dito. Masarap mag scramble eggs.' },
        { name: 'Ana L.', avatar: 'A', date: '2 weeks ago', rating: 4, text: 'Okay naman, medyo marami pang late deliveries dati pero ngayon mas okay na.' },
        { name: 'Carlos D.', avatar: 'C', date: '3 weeks ago', rating: 5, text: 'Best quality quail eggs in town! Order na namin to lagi.' }
    ],
    'live-quail': [
        { name: 'Pedro M.', avatar: 'P', date: '1 week ago', rating: 5, text: 'Malulusog ang quail na natanggap ko. Healthy pa at active. Salamat!', variant: 'Live Adult' },
        { name: 'Lisa R.', avatar: 'L', date: '2 weeks ago', rating: 5, text: 'Great for breeding! May kasamang care instructions na malinaw.' },
        { name: 'Rico T.', avatar: 'R', date: '1 month ago', rating: 4, text: 'Descent quality quails. Medyo nabawasan sa travel pero overall okay naman.' }
    ],
    'dressed-quail': [
        { name: 'Chef Marco.', avatar: 'M', date: '3 days ago', rating: 5, text: 'Super linis ng dressing! Perfect for restaurant use. Masarap sa grill.', variant: 'Vacuum Sealed' },
        { name: 'Sarah G.', avatar: 'S', date: '5 days ago', rating: 5, text: 'Super convenient! Dressed na sya so pwede na agad lutuin. Masarap!', variant: '2pcs Pack' },
        { name: 'Manny V.', avatar: 'M', date: '1 week ago', rating: 5, text: 'Ginamit ko sa sisig. Ang sarap! Delivery pa mismo sa bahay.' },
        { name: 'Ellen K.', avatar: 'E', date: '2 weeks ago', rating: 4, text: 'Okay naman overall. Medyo mas mataas ang price pero worth it naman.' },
        { name: 'Tony B.', avatar: 'T', date: '3 weeks ago', rating: 5, text: 'Best dressed quail I\'ve tried! Wala kang maramdaman ang lasa.' }
    ]
};

// Global variables for review submission
let selectedReviewRating = 0;
let currentReviewSlug = '';

function showProductDetail(slug, name, price, imageUrl, stock = 10, unit = 'piece') {
    currentReviewSlug = slug;
    selectedReviewRating = 0;

    const modal = document.getElementById('productDetailModal');

    // Set basic info
    document.getElementById('detailTitle').textContent = name;
    document.getElementById('detailPrice').innerHTML = '₱' + price + ' <span>/ ' + unit + '</span>';
    document.getElementById('detailImage').style.backgroundImage = "url('" + imageUrl + "')";

    // Set unit and stock info
    document.getElementById('detailUnit').textContent = unit;
    document.getElementById('detailStock').textContent = stock + ' available';
    document.getElementById('detailStockStatus').textContent = stock > 0 ? 'In Stock - Ready to Order' : 'Out of Stock';

    // Set badge based on index
    const badges = ['bestseller', 'available', 'limited'];
    const badgeLabels = ['Bestseller', 'Available Today', 'Limited Stock'];
    const badgeIndex = ['quail_eggs', 'live_quail', 'dressed_quail'].indexOf(slug);
    const badgeClass = badges[badgeIndex >= 0 ? badgeIndex : 1];
    const badgeLabel = badgeLabels[badgeIndex >= 0 ? badgeIndex : 1];
    document.getElementById('detailBadge').className = 'product-detail-badge ' + badgeClass;
    document.getElementById('detailBadge').textContent = badgeLabel;

    // Get product description based on slug
    const descriptions = {
        'fresh-quail-eggs': 'Premium fresh quail eggs collected daily from our farm. Each tray contains 24 pieces of carefully selected, high-quality eggs delivered within 24-48 hours.',
        'live-quail': 'Healthy, active quail birds perfect for breeding or raising. All birds are health-checked and ready for transport with proper care instructions provided.',
        'dressed-quail': 'Freshly cleaned and dressed quail ready for cooking. Professionally processed with proper hygiene standards and vacuum-sealed packaging.'
    };
    document.getElementById('detailDesc').textContent = descriptions[slug] || 'High quality product from Escalona\'s Farm.';

    // Set product features
    const features = {
        'fresh-quail-eggs': [
            'Fresh eggs collected daily',
            '24 pieces per tray',
            'Delivered within 24-48 hours',
            'Farm-fresh guarantee'
        ],
        'live-quail': [
            'Healthy, active birds',
            'Health-checked before delivery',
            'Care instructions included',
            'Perfect for breeding'
        ],
        'dressed-quail': [
            'Professionally processed',
            'Vacuum-sealed packaging',
            'Hygiene standards certified',
            'Ready to cook'
        ]
    };
    const featureList = features[slug] || ['High quality product', 'Farm fresh', 'Properly handled'];
    document.getElementById('detailFeatures').innerHTML = featureList.map(f => '<li><i class="ph-bold ph-check-circle"></i> ' + f + '</li>').join('');

    // Set order button
    const orderBtn = document.getElementById('detailOrderBtn');
    if (stock <= 0) {
        orderBtn.disabled = true;
        orderBtn.innerHTML = '<i class="ph-bold ph-warning"></i> Out of Stock';
    } else {
        orderBtn.disabled = false;
        orderBtn.innerHTML = '<i class="ph-bold ph-shopping-cart"></i> Order Now';
        orderBtn.onclick = function() {
            closeProductDetail();
            showOrderModal(slug, name, price);
        };
    }

    // Show stars
    let starsHtml = '';
    for (let i = 1; i <= 5; i++) {
        starsHtml += '<i class="ph-bold ph-star" style="' + (i <= 5 ? 'color:#ffc107;' : 'color:#e0e0e0;') + '"></i>';
    }
    document.getElementById('detailReviewsStars').innerHTML = starsHtml;

    // Show rating bars
    let barsHtml = '';
    [5, 4, 3, 2, 1].forEach(stars => {
        const pct = stars === 5 ? 75 : stars === 4 ? 15 : stars === 3 ? 6 : stars === 2 ? 3 : 1;
        barsHtml += '<div class="reviews-bar-row"><span>' + stars + '</span><i class="ph-bold ph-star" style="color:#ffc107;font-size:0.65rem;"></i><div class="reviews-bar"><div class="reviews-bar-fill" style="width:' + pct + '%;"></div></div><span>' + pct + '%</span></div>';
    });
    document.getElementById('detailReviewsBars').innerHTML = barsHtml;

    // Show reviews from database (with fallback to default reviews)
    loadProductReviews(slug).then(() => {
        // If no reviews from DB, show default reviews
        if (document.getElementById('detailReviewsList').innerHTML.trim() === '' ||
            document.getElementById('detailReviewsList').querySelector('.review-card') === null) {
            const defaultReviews = productReviews[slug] || [];
            let reviewsHtml = '';
            defaultReviews.forEach(review => {
                let rStars = '';
                for (let i = 1; i <= 5; i++) {
                    rStars += '<i class="ph-bold ph-star" style="' + (i <= review.rating ? 'color:#ffc107;' : 'color:#e0e0e0;') + '"></i>';
                }
                reviewsHtml += '<div class="review-card"><div class="review-header"><div class="review-avatar">' + review.avatar + '</div><div><div class="review-name">' + review.name + '</div><div class="review-date">' + review.date + '</div></div><div class="review-stars">' + rStars + '</div></div><div class="review-text">' + review.text + '</div>' + (review.variant ? '<div class="review-variant">Variant: ' + review.variant + '</div>' : '') + '</div>';
            });
            document.getElementById('detailReviewsList').innerHTML = reviewsHtml || '<p style="color:#8d6e63;text-align:center;padding:1rem;">No reviews yet</p>';
            document.getElementById('detailReviewsCount').textContent = defaultReviews.length + ' reviews';
        }
    });

    modal.classList.add('active');
    document.body.style.overflow = 'hidden';
}

function closeProductDetail() {
    document.getElementById('productDetailModal').classList.remove('active');
    document.body.style.overflow = 'auto';
}

// Close modal on outside click
document.getElementById('productDetailModal').addEventListener('click', function(e) {
    if (e.target === this) closeProductDetail();
});

function setReviewRating(rating) {
    selectedReviewRating = rating;
    document.querySelectorAll('#reviewStarsInput i').forEach((star, index) => {
        star.style.color = index < rating ? '#ffc107' : '#e0e0e0';
    });
}

async function submitReview() {
    if (selectedReviewRating === 0) {
        alert('Please select a rating before submitting.');
        return;
    }

    const comment = document.getElementById('reviewCommentInput').value.trim();
    const button = document.getElementById('submitReviewBtn');
    button.disabled = true;
    button.innerHTML = 'Submitting...';

    try {
        const response = await fetch('/reviews', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            },
            body: JSON.stringify({
                product_slug: currentReviewSlug,
                rating: selectedReviewRating,
                comment: comment
            })
        });

        const data = await response.json();
        if (data.success) {
            // Add review to list with data from server
            const newReview = {
                name: '{{ auth("customer")->user()->name }}',
                avatar: '{{ substr(auth("customer")->user()->name, 0, 1) }}',
                date: 'Just now',
                rating: selectedReviewRating,
                text: comment || 'No comment',
                admin_reply: null
            };

            const reviewsList = document.getElementById('detailReviewsList');
            let rStars = '';
            for (let i = 1; i <= 5; i++) {
                rStars += '<i class="ph-bold ph-star" style="' + (i <= newReview.rating ? 'color:#ffc107;' : 'color:#e0e0e0;') + '"></i>';
            }
            let reviewHtml = '<div class="review-card" style="background:#f8fff8;"><div class="review-header"><div class="review-avatar" style="background:#37b24d;">' + newReview.avatar + '</div><div><div class="review-name" style="color:#37b24d;">' + newReview.name + ' (You)</div><div class="review-date">' + newReview.date + '</div></div><div class="review-stars">' + rStars + '</div></div><div class="review-text">' + newReview.text + '</div></div>';
            reviewsList.insertAdjacentHTML('afterbegin', reviewHtml);

            // Show success modal
            showReviewSuccessModal();

            // Reset form after modal closes
            setTimeout(() => {
                selectedReviewRating = 0;
                document.querySelectorAll('#reviewStarsInput i').forEach(star => star.style.color = '#e0e0e0');
                document.getElementById('reviewCommentInput').value = '';
                button.disabled = false;
                button.innerHTML = 'Submit Review';
            }, 500);
        } else {
            alert('Failed to submit review');
            button.disabled = false;
            button.innerHTML = 'Submit Review';
        }
    } catch (error) {
        alert('Error submitting review');
        button.disabled = false;
        button.innerHTML = 'Submit Review';
    }
}

function showReviewSuccessModal() {
    const overlay = document.createElement('div');
    overlay.className = 'modal-overlay show';
    overlay.innerHTML = `
        <div class="modal" style="max-width: 450px;">
            <div class="modal-icon" style="background: linear-gradient(135deg, #51cf66 0%, #37b24d 100%);">
                <i class="ph-bold ph-check-circle"></i>
            </div>
            <h3 class="modal-title">Review Submitted!</h3>
            <div style="text-align: center; margin-bottom: 1.5rem;">
                <p style="font-size: 0.95rem; color: #4e342e;">Thank you for your feedback. Your review helps us improve our products and services.</p>
            </div>
            <div class="modal-buttons" style="gap: 1rem;">
                <button onclick="this.closest('.modal-overlay').remove(); document.body.style.overflow='auto';" class="modal-btn primary" style="flex: 1;">Continue Shopping</button>
            </div>
        </div>
    `;
    document.body.appendChild(overlay);
    document.body.style.overflow = 'hidden';

    overlay.addEventListener('click', function(e) {
        if (e.target === overlay) {
            overlay.remove();
            document.body.style.overflow = 'auto';
        }
    });
}

// Load reviews from database
async function loadProductReviews(slug) {
    try {
        const response = await fetch('/api/reviews/' + slug);
        const data = await response.json();
        if (data.success && data.reviews.length > 0) {
            let reviewsHtml = '';
            data.reviews.forEach(review => {
                let rStars = '';
                for (let i = 1; i <= 5; i++) {
                    rStars += '<i class="ph-bold ph-star" style="' + (i <= review.rating ? 'color:#ffc107;' : 'color:#e0e0e0;') + '"></i>';
                }
                let adminReplyHtml = '';
                if (review.admin_reply) {
                    adminReplyHtml = '<div class="review-reply" style="background:#f0f7f0;border-left:4px solid #51cf66;padding:0.75rem;margin-top:0.75rem;border-radius:0 6px 6px 0;"><div style="font-size:0.75rem;font-weight:600;color:#2e7d32;margin-bottom:0.25rem;"><i class="ph-bold ph-check-circle"></i> Admin Reply</div><div style="font-size:0.8rem;color:#4e342e;">' + review.admin_reply + '</div></div>';
                }
                reviewsHtml += '<div class="review-card"><div class="review-header"><div class="review-avatar" style="background:#6d4c41;">' + (review.customer.name.charAt(0).toUpperCase()) + '</div><div><div class="review-name">' + review.customer.name + '</div><div class="review-date">' + new Date(review.created_at).toLocaleDateString() + '</div></div><div class="review-stars">' + rStars + '</div></div><div class="review-text">' + (review.comment || 'No comment') + '</div>' + adminReplyHtml + '</div>';
            });
            document.getElementById('detailReviewsList').innerHTML = reviewsHtml;
            document.getElementById('detailReviewsCount').textContent = data.reviews.length + ' reviews';
        }
    } catch (error) {
        console.log('Using default reviews');
    }
}

@if(session('order_number'))
window.addEventListener('DOMContentLoaded', function() {
    showSuccessModal('{{ session('order_number') }}');
});
@endif

// Handle review reply notification click
@if(request()->has('review_reply'))
window.addEventListener('DOMContentLoaded', function() {
    const productSlug = '{{ request()->input('review_reply') }}';
    // Map product slug to product data (same as in loadProducts)
    const productMap = {
        'fresh-quail-eggs': { name: 'Fresh Quail Eggs', price: 80, stock: 50, unit: 'tray (24 pieces)', image: 'https://www.heritageacresmarket.com/wp-content/uploads/2021/04/quail-eggs-1536x1024.jpg' },
        'live-quail': { name: 'Live Quail', price: 180, stock: 25, unit: 'piece', image: 'https://tse2.mm.bing.net/th/id/OIP.Ge3tGykJY_QCdnTN79DgMwHaE8?rs=1&pid=ImgDetMain&o=7&rm=3' },
        'dressed-quail': { name: 'Dressed Quail', price: 250, stock: 15, unit: 'piece (cleaned)', image: 'https://tse3.mm.bing.net/th/id/OIP.f-7v4Sb2MI3CLuxVd5MfMQHaHa?rs=1&pid=ImgDetMain&o=7&rm=3' }
    };
    const product = productMap[productSlug];
    if (product) {
        showProductDetail(productSlug, product.name, product.price, product.image, product.stock, product.unit);
    }
});
@endif

function showSuccessModal(orderNumber) {
    const overlay = document.createElement('div');
    overlay.className = 'modal-overlay show';
    overlay.innerHTML = `
        <div class="modal" style="max-width: 500px;">
            <button class="modal-close" onclick="this.closest('.modal-overlay').remove(); document.body.style.overflow='auto'; window.location.href='{{ route('customer.orders') }}';">
                <i class="ph-bold ph-x"></i>
            </button>
            <div class="modal-icon" style="background: linear-gradient(135deg, #51cf66 0%, #37b24d 100%);">
                <i class="ph-bold ph-check-circle"></i>
            </div>
            <h3 class="modal-title">Order Placed Successfully!</h3>
            <div style="text-align: center; margin-bottom: 2rem;">
                <p style="font-size: 1rem; color: #4e342e; margin-bottom: 1rem;">Your order has been received.</p>
                <div style="background: #f8f5f1; padding: 1rem; border-radius: 10px; border: 2px solid #6d4c41;">
                    <p style="font-size: 0.85rem; color: #8d6e63; margin-bottom: 0.5rem;">Order Number</p>
                    <p style="font-size: 1.5rem; font-weight: 700; color: #6d4c41;">${orderNumber}</p>
                </div>
            </div>
            <div class="modal-buttons" style="gap: 1rem;">
                <a href="{{ route('customer.orders') }}" class="modal-btn primary" style="flex: 1; text-decoration: none; display: inline-block;">View My Orders</a>
            </div>
        </div>
    `;
    document.body.appendChild(overlay);
    document.body.style.overflow = 'hidden';

    overlay.addEventListener('click', function(e) {
        if (e.target === overlay) {
            overlay.remove();
            document.body.style.overflow = 'auto';
            window.location.href = '{{ route('customer.orders') }}';
        }
    });
}

async function loadProducts() {
    try {
        const urlParams = new URLSearchParams(window.location.search);
        const searchQuery = urlParams.get('search') || '';

        const response = await fetch(`/api/products?search=${encodeURIComponent(searchQuery)}`);
        const data = await response.json();
        const products = Array.isArray(data) ? data : (data.products || []);

        const grid = document.getElementById('productsGrid');

        if (products.length === 0) {
            grid.innerHTML = '<div class="empty-state"><i class="ph-bold ph-package"></i><h3>No products found</h3><p>Try searching for something else</p></div>';
            grid.classList.remove('search-mode');
            return;
        }

        // Add search mode class if there's a search query
        if (searchQuery) {
            grid.classList.add('search-mode');
        } else {
            grid.classList.remove('search-mode');
        }
        
        const badges = ['bestseller', 'available', 'limited'];
        const badgeLabels = ['Bestseller', 'Available Today', 'Limited Stock'];
        
        grid.innerHTML = products.map((product, index) => `
            <div class="product-card">
                <div class="product-img">
                    <img src="${product.image_url || 'https://via.placeholder.com/300x200?text=No+Image'}" alt="${product.name}">
                    <div class="product-badge ${badges[index % 3]}">${badgeLabels[index % 3]}</div>
                </div>
                <div class="product-content">
                    <div class="product-title">
                        <h3>${product.name}</h3>
                        <span class="product-price">₱${product.price}</span>
                    </div>
                    <p class="product-desc">${product.unit} - ${product.stock > 0 ? 'In Stock' : 'Out of Stock'}</p>
                    <div style="display: flex; gap: 0.5rem; margin-top: 0.75rem;">
                        <button onclick="showProductDetail('${product.slug}', '${product.name}', ${product.price}, '${product.image_url || ''}', ${product.stock}, '${product.unit}')"
                                style="flex: 1; padding: 0.6rem; border: 2px solid rgba(161,136,127,0.2); color: #8d6e63; background: white; border-radius: 8px; font-weight: 600; font-size: 0.85rem; cursor: pointer; transition: all 0.3s; font-family: 'Inter', sans-serif;"
                                onmouseover="this.style.borderColor='#a1887f'; this.style.background='#f8f5f1';"
                                onmouseout="this.style.borderColor='rgba(161,136,127,0.2)'; this.style.background='white';">
                            View Details
                        </button>
                        <button onclick="orderProduct('${product.slug}', '${product.name}', ${product.price})"
                                ${product.stock <= 0 ? 'disabled' : ''}
                                style="flex: 1; padding: 0.6rem; border: 2px solid rgba(161,136,127,0.2); color: ${product.stock > 0 ? '#6d4c41' : '#a1887f'}; background: transparent; border-radius: 8px; font-weight: 600; font-size: 0.85rem; cursor: ${product.stock > 0 ? 'pointer' : 'not-allowed'}; transition: all 0.3s; font-family: 'Inter', sans-serif; ${product.stock <= 0 ? 'opacity: 0.5;' : ''}"
                                onmouseover="this.style.borderColor='#6d4c41'; this.style.background='#f8f5f1';"
                                onmouseout="this.style.borderColor='rgba(161,136,127,0.2)'; this.style.background='transparent';">
                            ${product.stock > 0 ? 'Order Now' : 'Out of Stock'}
                        </button>
                    </div>
                </div>
            </div>
        `).join('');
    } catch (error) {
        console.error('Error loading products:', error);
        // Fallback to default products
        const defaultProducts = [
            { id: 1, name: 'Fresh Quail Eggs', slug: 'quail_eggs', unit: 'tray (24 pieces)', price: 80, stock: 50, image_url: 'https://www.instacart.com/company/wp-content/uploads/2023/01/quail-eggs.jpg' },
            { id: 2, name: 'Live Quail', slug: 'live_quail', unit: 'piece', price: 180, stock: 25, image_url: 'https://media.istockphoto.com/id/1289671737/photo/young-quail-isolated-on-white-background.jpg' },
            { id: 3, name: 'Dressed Quail', slug: 'dressed_quail', unit: 'piece (cleaned)', price: 250, stock: 15, image_url: 'http://wbldc.in/wp-content/uploads/2021/03/quail.jpg' }
        ];
        displayProducts(defaultProducts);
    }
}

function displayProducts(products) {
    const grid = document.getElementById('productsGrid');
    const badges = ['bestseller', 'available', 'limited'];
    const badgeLabels = ['Bestseller', 'Available Today', 'Limited Stock'];

    grid.innerHTML = products.map((product, index) => `
        <div class="product-card">
            <div class="product-img">
                <img src="${product.image_url || 'https://via.placeholder.com/300x200?text=No+Image'}" alt="${product.name}">
                <div class="product-badge ${badges[index % 3]}">${badgeLabels[index % 3]}</div>
            </div>
            <div class="product-content">
                <div class="product-title">
                    <h3>${product.name}</h3>
                    <span class="product-price">₱${product.price}</span>
                </div>
                <p class="product-desc">${product.unit} - ${product.stock > 0 ? 'In Stock' : 'Out of Stock'}</p>
                <div style="display: flex; gap: 0.5rem; margin-top: 0.75rem;">
                    <button onclick="showProductDetail('${product.slug}', '${product.name}', ${product.price}, '${product.image_url || ''}', ${product.stock}, '${product.unit}')"
                            style="flex: 1; padding: 0.6rem; border: 2px solid rgba(161,136,127,0.2); color: #8d6e63; background: white; border-radius: 8px; font-weight: 600; font-size: 0.85rem; cursor: pointer; transition: all 0.3s; font-family: 'Inter', sans-serif;"
                            onmouseover="this.style.borderColor='#a1887f'; this.style.background='#f8f5f1';"
                            onmouseout="this.style.borderColor='rgba(161,136,127,0.2)'; this.style.background='white';">
                        View Details
                    </button>
                    <button onclick="orderProduct('${product.slug}', '${product.name}', ${product.price})"
                            ${product.stock <= 0 ? 'disabled' : ''}
                            style="flex: 1; padding: 0.6rem; border: 2px solid rgba(161,136,127,0.2); color: ${product.stock > 0 ? '#6d4c41' : '#a1887f'}; background: transparent; border-radius: 8px; font-weight: 600; font-size: 0.85rem; cursor: ${product.stock > 0 ? 'pointer' : 'not-allowed'}; transition: all 0.3s; font-family: 'Inter', sans-serif; ${product.stock <= 0 ? 'opacity: 0.5;' : ''}"
                            onmouseover="this.style.borderColor='#6d4c41'; this.style.background='#f8f5f1';"
                            onmouseout="this.style.borderColor='rgba(161,136,127,0.2)'; this.style.background='transparent';">
                        ${product.stock > 0 ? 'Order Now' : 'Out of Stock'}
                    </button>
                </div>
            </div>
        </div>
    `).join('');
}

function orderProduct(slug, name, price) {
    showOrderModal(slug, name, price);
}

function showOrderModal(slug, name, price) {
    const customer = {
        name: '{{ auth("customer")->user()->name }}',
        phone: '{{ auth("customer")->user()->phone }}',
        address: '{{ auth("customer")->user()->address }}, {{ auth("customer")->user()->barangay }}, {{ auth("customer")->user()->municipality }}'
    };
    
    const overlay = document.createElement('div');
    overlay.className = 'modal-overlay show';
    overlay.innerHTML = `
        <div class="modal" style="max-width: 650px;">
            <button class="modal-close" onclick="this.closest('.modal-overlay').remove(); document.body.style='auto';">
                <i class="ph-bold ph-x"></i>
            </button>
            <div class="modal-icon">
                <i class="ph-bold ph-shopping-cart"></i>
            </div>
            <h3 class="modal-title">Place Your Order</h3>
            <form method="POST" action="{{ route('order.store') }}" enctype="multipart/form-data" style="text-align: left;">
                @csrf
                <input type="hidden" name="product" value="${slug}">
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem; margin-bottom: 1.25rem;">
                    <div>
                        <label style="display: block; font-weight: 600; color: #4e342e; margin-bottom: 0.6rem; font-size: 0.9rem;">Customer Name</label>
                        <input type="text" value="${customer.name}" readonly style="width: 100%; padding: 0.85rem; border: 2px solid rgba(161,136,127,0.15); border-radius: 10px; background: #faf8f6; font-family: 'Inter', sans-serif; color: #4e342e; font-weight: 500; font-size: 0.95rem;">
                    </div>
                    <div>
                        <label style="display: block; font-weight: 600; color: #4e342e; margin-bottom: 0.6rem; font-size: 0.9rem;">Contact Number</label>
                        <input type="text" value="${customer.phone}" readonly style="width: 100%; padding: 0.85rem; border: 2px solid rgba(161,136,127,0.15); border-radius: 10px; background: #faf8f6; font-family: 'Inter', sans-serif; color: #4e342e; font-weight: 500; font-size: 0.95rem;">
                    </div>
                </div>
                
                <div style="margin-bottom: 1.25rem;">
                    <label style="display: block; font-weight: 600; color: #4e342e; margin-bottom: 0.6rem; font-size: 0.9rem;">Delivery Location</label>
                    <input type="text" name="address" value="${customer.address}" required style="width: 100%; padding: 0.85rem; border: 2px solid rgba(161,136,127,0.2); border-radius: 10px; font-family: 'Inter', sans-serif; color: #4e342e; font-size: 0.95rem; transition: border-color 0.2s;" onfocus="this.style.borderColor='#6d4c41'" onblur="this.style.borderColor='rgba(161,136,127,0.2)'">
                </div>
                
                <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1.25rem; margin-bottom: 1.25rem;">
                    <div>
                        <label style="display: block; font-weight: 600; color: #4e342e; margin-bottom: 0.6rem; font-size: 0.9rem;">Product</label>
                        <input type="text" value="${name}" readonly style="width: 100%; padding: 0.85rem; border: 2px solid rgba(161,136,127,0.15); border-radius: 10px; background: #faf8f6; font-family: 'Inter', sans-serif; color: #4e342e; font-weight: 500; font-size: 0.95rem;">
                    </div>
                    <div>
                        <label style="display: block; font-weight: 600; color: #4e342e; margin-bottom: 0.6rem; font-size: 0.9rem;">Quantity</label>
                        <input type="number" name="quantity" min="1" value="1" required style="width: 100%; padding: 0.85rem; border: 2px solid rgba(161,136,127,0.2); border-radius: 10px; font-family: 'Inter', sans-serif; color: #4e342e; font-weight: 500; font-size: 0.95rem; transition: border-color 0.2s;" onfocus="this.style.borderColor='#6d4c41'" onblur="this.style.borderColor='rgba(161,136,127,0.2)'">
                    </div>
                </div>
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem; margin-bottom: 1.25rem;">
                    <div>
                        <label style="display: block; font-weight: 600; color: #4e342e; margin-bottom: 0.6rem; font-size: 0.9rem;">Order Type</label>
                        <select name="order_type" required style="width: 100%; padding: 0.85rem; border: 2px solid rgba(161,136,127,0.2); border-radius: 10px; font-family: 'Inter', sans-serif; color: #4e342e; font-weight: 500; font-size: 0.95rem; background: white; cursor: pointer; transition: border-color 0.2s;" onfocus="this.style.borderColor='#6d4c41'" onblur="this.style.borderColor='rgba(161,136,127,0.2)'">
                            <option value="pickup">Pickup</option>
                            <option value="delivery">Delivery</option>
                        </select>
                    </div>
                    <div>
                        <label style="display: block; font-weight: 600; color: #4e342e; margin-bottom: 0.6rem; font-size: 0.9rem;">Preferred Date</label>
                        <input type="date" name="preferred_date" required style="width: 100%; padding: 0.85rem; border: 2px solid rgba(161,136,127,0.2); border-radius: 10px; font-family: 'Inter', sans-serif; color: #4e342e; font-size: 0.95rem; transition: border-color 0.2s;" onfocus="this.style.borderColor='#6d4c41'" onblur="this.style.borderColor='rgba(161,136,127,0.2)'">
                    </div>
                </div>
                
                <div style="margin-bottom: 2rem;">
                    <label style="display: block; font-weight: 600; color: #4e342e; margin-bottom: 0.6rem; font-size: 0.9rem;">Payment Proof (Optional)</label>
                    <div style="background: #f8f5f1; padding: 1rem; border-radius: 10px; margin-bottom: 0.75rem; border-left: 4px solid #6d4c41;">
                        <p style="font-size: 0.9rem; color: #4e342e; margin-bottom: 0.25rem; font-weight: 600;">GCash Number: <span style="color: #6d4c41;">09171234567</span></p>
                        <p style="font-size: 0.85rem; color: #8d6e63; margin: 0;">Send payment and upload screenshot below</p>
                    </div>
                    <div style="position: relative;">
                        <input type="file" name="gcash_proof" accept="image/*" style="width: 100%; padding: 0.85rem; border: 2px dashed rgba(161,136,127,0.3); border-radius: 10px; font-family: 'Inter', sans-serif; color: #4e342e; font-size: 0.9rem; cursor: pointer; transition: all 0.2s;" onmouseover="this.style.borderColor='#6d4c41'; this.style.background='#faf8f6'" onmouseout="this.style.borderColor='rgba(161,136,127,0.3)'; this.style.background='white'">
                    </div>
                </div>
                
                <div class="modal-buttons" style="gap: 1rem;">
                    <button type="button" onclick="this.closest('.modal-overlay').remove(); document.body.style.overflow='auto';" class="modal-btn secondary" style="flex: 1;">Cancel</button>
                    <button type="submit" class="modal-btn primary" style="flex: 1;">Place Order</button>
                </div>
            </form>
        </div>
    `;
    document.body.appendChild(overlay);
    document.body.style.overflow = 'hidden';
    
    overlay.addEventListener('click', function(e) {
        if (e.target === overlay) {
            overlay.remove();
            document.body.style.overflow = 'auto';
        }
    });
}

loadProducts();
</script>
@endsection