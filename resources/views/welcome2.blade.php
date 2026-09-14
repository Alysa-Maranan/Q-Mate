<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Escalona's Farm | Smart Quail Management</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Poppins:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <!-- Phosphor Icons for modern sleek icons -->
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <style>
        :root {
            --primary: #6d4c41;
            --primary-dark: #4e342e;
            --primary-light: #a1887f;
            --accent: #6d4c41; /* Brown matching login */
            --accent-hover: #5d4037;
            --bg-base: #fffdfa;
            --bg-alt: #f8f5f1;
            --text-main: #2c2724;
            --text-muted: #795548;
            --border-color: rgba(161, 136, 127, 0.2);
        }
        
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { 
            font-family: 'Inter', sans-serif; 
            background: var(--bg-base); 
            color: var(--text-main); 
            scroll-behavior: smooth; 
            overflow-x: hidden; 
        }
        h1, h2, h3, h4, h5, h6 { font-family: 'Poppins', sans-serif; color: var(--primary-dark); }
        a { text-decoration: none; transition: 0.3s; }

        /* ANIMATIONS */
        @keyframes fadeInUp { from { opacity: 0; transform: translateY(30px); } to { opacity: 1; transform: translateY(0); } }
        .animate-fade-up { animation: fadeInUp 0.8s ease-out forwards; }
        .delay-1 { animation-delay: 0.2s; opacity: 0; }
        .delay-2 { animation-delay: 0.4s; opacity: 0; }

        /* NAV */
        .nav {
            background: rgba(255, 253, 250, 0.95);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid var(--border-color);
            padding: 1rem 5%;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 1000;
        }
        .nav-logo { display: flex; align-items: center; gap: 0.75rem; }
        .nav-logo img { width: 45px; height: 45px; border-radius: 12px; object-fit: cover; border: 2px solid var(--primary-light); }
        .nav-logo span { font-size: 1.4rem; font-weight: 800; color: var(--primary); letter-spacing: -0.5px; }
        
        .nav-links { display: flex; gap: 1rem; align-items: center; }
        .nav-links a.nav-item { padding: 0.5rem 1rem; font-weight: 600; font-size: 0.95rem; color: var(--primary); }
        .nav-links a.nav-item:hover { color: var(--accent); }
        
        .btn { display: inline-flex; align-items: center; justify-content: center; gap: 0.5rem; padding: 0.75rem 1.5rem; border-radius: 10px; font-weight: 600; font-size: 0.95rem; cursor: pointer; transition: all 0.3s ease; }
        .btn-outline { border: 2px solid var(--border-color); color: var(--primary); background: transparent; }
        .btn-outline:hover { border-color: var(--primary); background: var(--bg-alt); }
        .btn-primary { background: var(--accent); color: white; border: none; box-shadow: 0 4px 15px rgba(109, 76, 65, 0.3); }
        .btn-primary:hover { background: var(--accent-hover); transform: translateY(-2px); box-shadow: 0 6px 20px rgba(109, 76, 65, 0.4); }

        .hamburger { display: none; flex-direction: column; cursor: pointer; gap: 5px; background: none; border: none; }
        .hamburger span { width: 25px; height: 3px; background: var(--primary); transition: 0.3s; }

        /* HERO */
        .hero { 
            padding: 6rem 5% 5rem;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 4rem;
            align-items: center;
            max-width: 1400px;
            margin: 0 auto;
            position: relative;
        }
        .hero-badge {
            display: inline-flex; align-items: center; gap: 0.5rem;
            background: rgba(109, 76, 65, 0.1); color: var(--accent-hover);
            padding: 0.5rem 1rem; border-radius: 50px;
            font-weight: 600; font-size: 0.85rem; margin-bottom: 1.5rem;
        }
        .hero h1 { font-size: clamp(2.5rem, 4vw, 4rem); line-height: 1.1; margin-bottom: 1.5rem; }
        .hero h1 span { color: var(--accent); }
        .hero p { font-size: 1.1rem; color: var(--text-muted); line-height: 1.7; margin-bottom: 2rem; max-width: 90%; }
        .hero-buttons { display: flex; gap: 1rem; }
        
        .hero-images { position: relative; display: flex; justify-content: flex-end; }
        .hero-img-main { width: 90%; max-width: 500px; border-radius: 20px; box-shadow: 0 20px 40px rgba(109, 76, 65, 0.15); object-fit: cover; aspect-ratio: 4/5; }
        .hero-card { 
            position: absolute; bottom: 30px; left: -20px; 
            background: white; padding: 1rem 1.5rem; border-radius: 12px; 
            box-shadow: 0 10px 30px rgba(0,0,0,0.1); 
            display: flex; align-items: center; gap: 1rem;
            animation: fadeInUp 1s ease-out 0.6s forwards; opacity: 0;
        }
        .hero-card i { font-size: 2rem; color: var(--accent); }
        .hero-card div { display: flex; flex-direction: column; }
        .hero-card span:nth-child(1) { font-weight: 700; color: var(--primary-dark); }
        .hero-card span:nth-child(2) { font-size: 0.85rem; color: var(--text-muted); }

        /* STATS / FEATURES */
        .features { background: var(--primary-dark); padding: 3rem 5%; color: white; display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 2rem; }
        .feature-item { display: flex; align-items: center; gap: 1rem; }
        .feature-item i { font-size: 2.5rem; color: var(--accent); }
        .feature-item h4 { color: white; margin-bottom: 0.25rem; }
        .feature-item p { color: rgba(255,255,255,0.7); font-size: 0.9rem; }

        /* SECTION HEADERS */
        .section { padding: 5rem 5%; max-width: 1400px; margin: 0 auto; }
        .section-header { text-align: center; margin-bottom: 3.5rem; max-width: 600px; margin-left: auto; margin-right: auto; }
        .section-header h2 { font-size: 2.2rem; margin-bottom: 1rem; }
        .section-header p { color: var(--text-muted); font-size: 1.05rem; line-height: 1.6; }

        /* PRODUCTS & CUSTOMER CTA */
        .customer-cta { background: var(--bg-base); border: 2px dashed var(--primary-light); padding: 3rem 5%; border-radius: 20px; text-align: center; margin-top: 4rem; box-shadow: 0 10px 30px rgba(109,76,65,0.05); }
        .customer-cta h3 { font-size: 1.8rem; margin-bottom: 0.5rem; color: var(--primary-dark); }
        .customer-cta p { color: var(--text-muted); font-size: 1.05rem; margin-bottom: 2rem; max-width: 600px; margin-left: auto; margin-right: auto; line-height: 1.6; }
        .customer-cta-buttons { display: flex; gap: 1rem; justify-content: center; }

        /* PRODUCTS */
        .products-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 2rem; align-items: stretch; }
        .product-card { 
            background: white; border-radius: 16px; overflow: hidden; 
            border: 1px solid var(--border-color); 
            border-bottom: 5px solid var(--primary); /* Brown side/bottom design */
            transition: all 0.3s ease; 
            display: flex; flex-direction: column;
        }
        .product-card:hover { transform: translateY(-5px); box-shadow: 0 15px 35px rgba(109, 76, 65, 0.15); border-bottom-color: var(--accent); }
        .product-img { height: 220px; overflow: hidden; position: relative; flex-shrink: 0; }
        .product-img img { width: 100%; height: 100%; object-fit: cover; transition: transform 0.5s ease; }
        .product-card:hover .product-img img { transform: scale(1.05); }
        .product-badge { position: absolute; top: 12px; right: 12px; padding: 0.4rem 0.9rem; border-radius: 8px; font-weight: 700; font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.5px; box-shadow: 0 4px 12px rgba(0,0,0,0.15); }
        .product-badge.bestseller { background: linear-gradient(135deg, #ff6b6b 0%, #ee5a6f 100%); color: white; }
        .product-badge.available { background: linear-gradient(135deg, #51cf66 0%, #37b24d 100%); color: white; }
        .product-badge.limited { background: linear-gradient(135deg, #ffd43b 0%, #f59f00 100%); color: #333; }
        
        .product-content { padding: 1.5rem; display: flex; flex-direction: column; flex-grow: 1; }
        .product-title { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 0.5rem; }
        .product-title h3 { font-size: 1.25rem; }
        .product-price { font-weight: 700; color: var(--accent); font-size: 1.2rem; background: rgba(109,76,65,0.1); padding: 0.3rem 0.8rem; border-radius: 8px; white-space: nowrap; }
        .product-desc { color: var(--text-muted); font-size: 0.95rem; line-height: 1.5; margin-bottom: 1.5rem; flex-grow: 1; }
        .product-action { width: 100%; margin-top: auto; }

        /* ABOUT / INFO GRID */
        .about-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 4rem; align-items: center; }
        .about-img { border-radius: 20px; overflow: hidden; aspect-ratio: 4/3; box-shadow: 0 15px 35px rgba(0,0,0,0.1); }
        .about-img img { width: 100%; height: 100%; object-fit: cover; }
        .about-text h3 { font-size: 1.8rem; margin-bottom: 1rem; }
        .about-text p { color: var(--text-muted); line-height: 1.7; margin-bottom: 1.5rem; }
        .about-list { display: flex; flex-direction: column; gap: 1rem; }
        .about-list-item { display: flex; align-items: center; gap: 0.75rem; color: var(--primary-dark); font-weight: 500; }
        .about-list-item i { color: var(--accent); font-size: 1.25rem; }

        /* DELIVERY GRID */
        .delivery-grid { display: grid; grid-template-columns: 1fr 1.2fr; gap: 3rem; align-items: stretch; }
        .delivery-info { display: flex; flex-direction: column; gap: 1.5rem; }
        .delivery-card { background: white; padding: 1.5rem; border-radius: 12px; display: flex; gap: 1.2rem; border: 1px solid var(--border-color); box-shadow: 0 5px 15px rgba(0,0,0,0.03); transition: all 0.3s ease; }
        .delivery-card:hover { transform: translateY(-3px); box-shadow: 0 10px 25px rgba(0,0,0,0.05); border-color: var(--accent); }
        .delivery-icon i { font-size: 2.2rem; color: var(--accent); }
        .delivery-card h4 { font-size: 1.2rem; margin-bottom: 0.25rem; }
        .delivery-time { display: inline-block; font-size: 0.85rem; font-weight: 700; color: var(--accent-hover); background: rgba(109,76,65,0.1); padding: 0.2rem 0.6rem; border-radius: 4px; margin-bottom: 0.5rem; }
        .delivery-card p { color: var(--text-muted); font-size: 0.95rem; line-height: 1.5; }
        .delivery-map-wrapper { width: 100%; height: 100%; min-height: 450px; border-radius: 16px; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.08); border: 1px solid var(--border-color); }

        @media (max-width: 992px) {
            .delivery-grid { grid-template-columns: 1fr; }
            .delivery-map-wrapper { min-height: 350px; }
        }

        /* FOOTER */
        .footer { background: var(--primary-dark); color: #fff; padding: 4rem 5% 2rem; margin-top: 4rem; }
        .footer-grid { display: grid; grid-template-columns: 2fr 1fr 1fr 1.5fr; gap: 3rem; margin-bottom: 3rem; max-width: 1400px; margin-left: auto; margin-right: auto;}
        .footer-brand h3 { font-size: 1.5rem; font-weight: 800; color: #fff; margin-bottom: 1rem; }
        .footer-brand p { color: rgba(255,255,255,0.7); line-height: 1.6; max-width: 300px; }
        .footer h4 { color: white; margin-bottom: 1.5rem; font-size: 1.1rem; }
        .footer-links { display: flex; flex-direction: column; gap: 0.75rem; }
        .footer-links a { color: rgba(255,255,255,0.7); }
        .footer-links a:hover { color: var(--accent); }
        .footer-contact li { display: flex; gap: 0.75rem; margin-bottom: 1rem; color: rgba(255,255,255,0.7); }
        .footer-contact i { color: var(--accent); font-size: 1.2rem; }
        .footer-bottom { text-align: center; padding-top: 2rem; border-top: 1px solid rgba(255,255,255,0.1); color: rgba(255,255,255,0.5); font-size: 0.9rem; }

        /* ACCOUNT MODAL */
        .modal-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.6);
            z-index: 2000;
            align-items: center;
            justify-content: center;
            backdrop-filter: blur(5px);
        }
        .modal-overlay.show {
            display: flex;
        }
        .modal {
            background: white;
            padding: 2.5rem;
            border-radius: 20px;
            width: 90%;
            max-width: 500px;
            box-shadow: 0 25px 60px rgba(0, 0, 0, 0.3);
            text-align: center;
            position: relative;
            animation: modalSlideIn 0.3s ease-out;
        }
        @keyframes modalSlideIn {
            from { opacity: 0; transform: translateY(-30px) scale(0.95); }
            to { opacity: 1; transform: translateY(0) scale(1); }
        }
        .modal-close {
            position: absolute;
            top: 1rem;
            right: 1rem;
            background: none;
            border: none;
            font-size: 1.5rem;
            color: #8d6e63;
            cursor: pointer;
            padding: 0.5rem;
            border-radius: 50%;
            transition: all 0.2s;
        }
        .modal-close:hover {
            background: #f5f0eb;
            color: var(--primary-dark);
        }
        .modal-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            font-size: 2.5rem;
            color: white;
            box-shadow: 0 8px 25px rgba(109, 76, 65, 0.3);
        }
        .modal-title {
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--primary-dark);
            margin-bottom: 1rem;
        }
        .modal-message {
            color: var(--text-muted);
            font-size: 1.05rem;
            line-height: 1.6;
            margin-bottom: 2rem;
        }
        .modal-buttons {
            display: flex;
            gap: 1rem;
            justify-content: center;
            flex-wrap: wrap;
        }
        .modal-btn {
            padding: 0.85rem 1.8rem;
            border: none;
            border-radius: 12px;
            font-weight: 600;
            font-size: 0.95rem;
            cursor: pointer;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }
        .modal-btn.primary {
            background: var(--accent);
            color: white;
            box-shadow: 0 4px 15px rgba(109, 76, 65, 0.3);
        }
        .modal-btn.primary:hover {
            background: var(--accent-hover);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(109, 76, 65, 0.4);
        }
        .modal-btn.secondary {
            background: #f5f0eb;
            color: var(--primary);
            border: 2px solid var(--border-color);
        }
        .modal-btn.secondary:hover {
            background: #efebe9;
            border-color: var(--primary);
        }

        /* RESPONSIVE */
        @media (max-width: 992px) {
            .hero, .about-grid { grid-template-columns: 1fr; gap: 3rem; text-align: center; }
            .hero-images { justify-content: center; margin-top: 2rem; }
            .hero p { margin: 0 auto 2rem; }
            .hero-buttons { justify-content: center; }
            .hero-card { left: 50%; transform: translateX(-50%); bottom: -20px; }
            .footer-grid { grid-template-columns: 1fr 1fr; }
        }
        @media (max-width: 768px) {
            .nav-links { 
                position: absolute; top: 100%; left: 0; width: 100%; 
                background: white; flex-direction: column; padding: 1.5rem; 
                box-shadow: 0 10px 20px rgba(0,0,0,0.05);
                display: none; border-top: 1px solid var(--border-color);
            }
            .nav-links.active { display: flex; }
            .hamburger { display: flex; }
            .footer-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>

<!-- NAVIGATION -->
<nav class="nav">
    <a href="#" class="nav-logo">
        <img src="https://th.bing.com/th/id/R.2f6964b62e896f5f94f40390e330a1f8?rik=xmu%2bEOduizkGYw&riu=http%3a%2f%2f1.bp.blogspot.com%2f-unYxCWFMHHg%2fVkwfiPzQihI%2fAAAAAAAAq2w%2fhxBh0S_eU3A%2fs1600%2fQuail-Bird-Eggs-(4).jpg&ehk=NmS88wB52fy2LF8VygVGin9Yh5Nn9arRYJNlwN1WaGQ%3d&risl=&pid=ImgRaw&r=0" alt="SQUIFM">
        <span>Escalona's Farm</span>
    </a>
    <button class="hamburger" onclick="toggleMenu()">
        <span></span><span></span><span></span>
    </button>
    <div class="nav-links" id="navLinks">
        <a href="#" class="nav-item">Home</a>
        <a href="#about" class="nav-item">About Us</a>
        <a href="{{ route('contact.support') }}" class="nav-item">Contact</a>
        <a href="{{ route('login') }}" class="btn btn-outline"><i class="ph-bold ph-user-gear"></i> Admin Login</a>
    </div>
</nav>

<!-- HERO SECTION -->
<section class="hero">
    <div class="hero-content animate-fade-up">
        <div class="hero-badge">
            <i class="ph-fill ph-check-circle"></i>
            Smart IoT-Powered Farm Management
        </div>
        <h1>Fresh Quail Products, <br><span>Smarter Farming.</span></h1>
        <p>Experience the finest quail eggs and meat from Oriental Mindoro. Our livestock is monitored closely utilizing modern IoT tech to ensure premium quality straight to your table.</p>
        <div class="hero-buttons">
            <button onclick="showAccountModal()" class="btn btn-primary">
                Order Now <i class="ph-bold ph-arrow-right"></i>
            </button>
            <a href="#about" class="btn btn-outline">
                Learn More
            </a>
        </div>
    </div>
    <div class="hero-images animate-fade-up delay-1">
        <img src="https://www.worldlifeexpectancy.com/images/a/w/b/coturnix-japonica/coturnix-japonica.jpg" alt="Quails at Farm" class="hero-img-main">
        <div class="hero-card">
            <i class="ph-fill ph-plant"></i>
            <div>
                <span>100% Fresh</span>
                <span>Harvested Daily</span>
            </div>
        </div>
    </div>
</section>

<!-- HIGHLIGHTS BAR -->
<section class="features">
    <div class="feature-item">
        <i class="ph ph-cpu"></i>
        <div>
            <h4>Smart Management</h4>
            <p>Monitored via IoT System</p>
        </div>
    </div>
    <div class="feature-item">
        <i class="ph ph-egg"></i>
        <div>
            <h4>Premium Quality</h4>
            <p>Strict Health Standards</p>
        </div>
    </div>
    <div class="feature-item">
        <i class="ph ph-thermometer"></i>
        <div>
            <h4>Automated Care</h4>
            <p>Precision feeding & climate</p>
        </div>
    </div>
    <div class="feature-item">
        <i class="ph ph-truck"></i>
        <div>
            <h4>Fast Delivery</h4>
            <p>Door-to-door fresh supply</p>
        </div>
    </div>
</section>

<!-- ABOUT SECTION -->
<section id="about" class="section bg-alt">
    <div class="about-grid">
        <div class="about-img animate-fade-up">
            <img src="https://static.vecteezy.com/system/resources/thumbnails/008/951/551/small_2x/quails-and-eggs-in-a-cage-on-a-farm-photo.jpg" alt="Farm Infrastructure">
        </div>
        <div class="about-text animate-fade-up delay-1">
            <div class="hero-badge" style="background: rgba(109,76,65,0.1); color: var(--primary);">Our Farm</div>
            <h3>Pioneering Sustainable & Smart Quail Farming</h3>
            <p>Located in the heart of Pagkakaisa, Naujan, Oriental Mindoro, Escalona's Farm bridges traditional farming practices with modern automated IoT technology.</p>
            <p>Our Intelligent Quail Management System (SQUIFM) automatically monitors conditions ensures that our birds live in the optimal environment, resulting in healthier livestock and vastly superior products.</p>
            
            <div class="about-list">
                <div class="about-list-item"><i class="ph-bold ph-check"></i> Sustainable Farming Operations</div>
                <div class="about-list-item"><i class="ph-bold ph-check"></i> Automated Climate & Feeding Control</div>
                <div class="about-list-item"><i class="ph-bold ph-check"></i> Strict Daily Hygiene Checks</div>
            </div>
        </div>
    </div>
</section>

<!-- PRODUCTS SECTION -->
<section id="products" class="section">
    <div class="section-header animate-fade-up">
        <h2>Farm Fresh Products</h2>
        <p>Premium quality direct from our smart farm to your kitchen.</p>
    </div>
    <div class="products-grid">
        <!-- Product 1 -->
        <div class="product-card animate-fade-up">
            <div class="product-img">
                <img src="https://d2gg9evh47fn9z.cloudfront.net/1600px_COLOURBOX7106353.jpg" alt="Quail Eggs">
                <div class="product-badge bestseller">Bestseller</div>
            </div>
            <div class="product-content">
                <div class="product-title">
                    <h3>Fresh Quail Eggs</h3>
                    <span class="product-price">₱160</span>
                </div>
                <p class="product-desc">Premium fresh quail eggs collected daily. Each tray contains 24 carefully selected pieces.</p>
                <button onclick="showAccountModal()" class="btn btn-outline product-action">Order Now</button>
            </div>
        </div>
        <!-- Product 2 -->
        <div class="product-card animate-fade-up delay-1">
            <div class="product-img">
                <img src="https://media.istockphoto.com/id/516262976/photo/coturnix-on-a-white-background.jpg?s=612x612&w=0&k=20&c=mXuboIkTnVA1is46_EqA9rrLk-2gvBGgq-5cjWTLR9U=" alt="Live Quail">
                <div class="product-badge available">Available Today</div>
            </div>
            <div class="product-content">
                <div class="product-title">
                    <h3>Live Quails</h3>
                    <span class="product-price">₱180</span>
                </div>
                <p class="product-desc">Healthy, active birds ready for your own farm. Systematically health-checked via our software.</p>
                <button onclick="showAccountModal()" class="btn btn-outline product-action">Order Now</button>
            </div>
        </div>
        <!-- Product 3 -->
        <div class="product-card animate-fade-up delay-2">
            <div class="product-img">
                <img src="https://wbldc.in/wp-content/uploads/2021/03/quail.jpg" alt="Dressed Quail">
                <div class="product-badge limited">Limited Stock</div>
            </div>
            <div class="product-content">
                <div class="product-title">
                    <h3>Dressed Quail</h3>
                    <span class="product-price">₱250</span>
                </div>
                <p class="product-desc">Professionally cleaned and dressed meat. Perfect for quick and delicious roasting or frying.</p>
                <button onclick="showAccountModal()" class="btn btn-outline product-action">Order Now</button>
            </div>
        </div>
    </div>
</section>

<!-- DELIVERY & LOCATION SECTION -->
<section class="section bg-alt" id="location">
    <div class="section-header animate-fade-up">
        <h2>Delivery & Location</h2>
        <p>Fast, reliable delivery directly from our farm to your area.</p>
    </div>
    <div class="delivery-grid">
        <div class="delivery-info animate-fade-up delay-1">
            <div class="delivery-card">
                <div class="delivery-icon"><i class="ph-fill ph-map-pin"></i></div>
                <div>
                    <h4>Naujan Area</h4>
                    <span class="delivery-time">24-48 hours</span>
                    <p>Poblacion, Pagkakaisa, San Isidro, Mahabang Pulo, Bayanan, Magdalo</p>
                </div>
            </div>
            <div class="delivery-card">
                <div class="delivery-icon"><i class="ph-fill ph-check-circle"></i></div>
                <div>
                    <h4>Socorro Area</h4>
                    <span class="delivery-time">2-3 days</span>
                    <p>Poblacion, Bayan, Lanot, Buenavista</p>
                </div>
            </div>
            <div class="delivery-card">
                <div class="delivery-icon"><i class="ph-fill ph-truck"></i></div>
                <div>
                    <h4>Other Areas</h4>
                    <span class="delivery-time">4-7 days</span>
                    <p>Calapan, Puerto Galera, Victoria</p>
                </div>
            </div>
        </div>
        <div class="delivery-map-wrapper animate-fade-up delay-2">
            <iframe src="https://maps.google.com/maps?q=Pagkakaisa,+Naujan,+Oriental+Mindoro&t=&z=14&ie=UTF8&iwloc=&output=embed" width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
        </div>
    </div>
</section>

<!-- FOOTER SECTION -->
<footer class="footer">
    <div class="footer-grid">
        <div class="footer-brand">
              <h3>Escalona's Farm</h3>
              <p>Our Intelligent Quail Management System (SQUIFM) automatically monitors conditions ensures that our birds live in the optimal environment, resulting in healthier livestock and vastly superior products.</p>
        </div>
        <div>
            <h4>Quick Links</h4>
            <div class="footer-links">
                <a href="#">Home</a>
                <a href="#about">About</a>
                <a href="{{ route('contact.support') }}">Contact</a>
            </div>
        </div>
        <div>
            <h4>Account</h4>
            <div class="footer-links">
                <a href="{{ route('login') }}">Login</a>
                <a href="{{ route('register') }}">Sign Up</a>
                <a href="#">Guest Tracking</a>
            </div>
        </div>
        <div>
            <h4>Contact Info</h4>
            <ul class="footer-contact" style="list-style: none; padding: 0;">
                <li><i class="ph-fill ph-map-pin"></i> Pagkakaisa, Naujan, Or. Mindoro</li>
                <li><i class="ph-fill ph-phone"></i> +63 917 123 4567</li>
                <li><i class="ph-fill ph-envelope-simple"></i> escalona.farm@gmail.com</li>
            </ul>
        </div>
    </div>
    <div class="footer-bottom">
        &copy; {{ date('Y') }} Escalona's Farm | Capstone SQUIFM. All rights reserved.
    </div>
</footer>

<!-- Account Required Modal -->
<div class="modal-overlay" id="accountModal">
    <div class="modal">
        <button class="modal-close" onclick="hideAccountModal()">
            <i class="ph-bold ph-x"></i>
        </button>
        <div class="modal-icon">
            <i class="ph-bold ph-user-plus"></i>
        </div>
        <h3 class="modal-title">Account Required</h3>
        <p class="modal-message">
            To place an order and enjoy our farm fresh products, you'll need to create an account first. This helps us manage your orders and provide better service.
        </p>
        <div class="modal-buttons">
            <a href="{{ route('customer.register') }}" class="modal-btn primary">
                <i class="ph-bold ph-user-plus"></i>
                Create Account
            </a>
            <a href="{{ route('customer.login') }}?redirect_to={{ route('customer.dashboard') }}" class="modal-btn secondary">
                <i class="ph-bold ph-sign-in"></i>
                Login
            </a>
        </div>
    </div>
</div>

<script>
function toggleMenu() {
    const navLinks = document.getElementById('navLinks');
    navLinks.classList.toggle('active');
}

// Fade in animations on scroll
const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if(entry.isIntersecting) {
            entry.target.style.animationPlayState = 'running';
        }
    });
});

document.querySelectorAll('.animate-fade-up').forEach(el => {
    el.style.animationPlayState = 'paused';
    observer.observe(el);
});

// Account Modal Functions
function showAccountModal() {
    document.getElementById('accountModal').classList.add('show');
    document.body.style.overflow = 'hidden';
}

function hideAccountModal() {
    document.getElementById('accountModal').classList.remove('show');
    document.body.style.overflow = 'auto';
}

// Close modal when clicking outside
document.getElementById('accountModal').addEventListener('click', function(e) {
    if (e.target === this) {
        hideAccountModal();
    }
});

// Close modal with Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        hideAccountModal();
    }
});
</script>
</body>
</html>
