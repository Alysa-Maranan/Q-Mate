<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Contact Support | Escalona's Farm</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Inter', sans-serif; background: #fffdfa; color: #4e342e; min-height: 100vh; display: flex; flex-direction: column; }

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

        /* MAIN */
        .main-content { flex: 1; padding: 3rem 2rem; }
        .container { max-width: 950px; margin: 0 auto; }
        .page-header { text-align: center; margin-bottom: 2rem; }
        .page-logo { width: 80px; height: 80px; border-radius: 20px; background: #fffdfa; border: 3px solid #a1887f; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 16px rgba(141,110,99,0.12); margin: 0 auto 1rem; overflow: hidden; }
        .page-logo img { width: 100%; height: 100%; object-fit: cover; }
        .page-title { font-size: 1.75rem; font-weight: 800; color: #6d4c41; margin-bottom: 0.5rem; }
        .page-subtitle { font-size: 0.95rem; color: #a1887f; }

        /* LAYOUT */
        .content-grid { display: grid; grid-template-columns: 1fr 2fr; gap: 1.5rem; }
        .info-cards { display: flex; flex-direction: column; gap: 1rem; }
        .info-card { background: #fffdfa; border-radius: 16px; box-shadow: 0 4px 20px rgba(141,110,99,0.08); border: 2px solid #d7ccc8; padding: 1rem; transition: all 0.3s ease; cursor: pointer; }
        .info-card:hover { transform: translateY(-3px); box-shadow: 0 8px 28px rgba(141,110,99,0.15); border-color: #a1887f; }
        .info-card-inner { display: flex; align-items: center; gap: 0.75rem; }
        .info-card-icon { width: 44px; height: 44px; background: #d7ccc8; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.25rem; flex-shrink: 0; }
        .info-card-content h3 { font-weight: 600; color: #6d4c41; margin: 0; font-size: 0.9rem; }
        .info-card-content p { font-size: 0.8rem; color: #a1887f; margin: 0.25rem 0 0 0; line-height: 1.4; }
        .info-card.highlight { background: linear-gradient(135deg, #a1887f 0%, #6d4c41 100%); border-color: #6d4c41; }
        .info-card.highlight .info-card-icon { background: rgba(255,255,255,0.2); }
        .info-card.highlight h3, .info-card.highlight p { color: #fff; }

        /* FORM */
        .form-card { background: #fffdfa; border-radius: 20px; box-shadow: 0 4px 30px rgba(141,110,99,0.10); border: 2px solid #a1887f; padding: 2rem; }
        .form-title { font-size: 1.25rem; font-weight: 700; color: #6d4c41; margin-bottom: 0.25rem; }
        .form-subtitle { font-size: 0.9rem; color: #a1887f; margin-bottom: 1.5rem; }
        .form-group { margin-bottom: 1.25rem; }
        .form-label { display: block; font-size: 0.875rem; font-weight: 600; color: #6d4c41; margin-bottom: 0.4rem; }
        .form-input-wrapper { background: #f5eee6; border: 1.5px solid #a1887f; padding: 0.75rem 1rem; border-radius: 12px; }
        .form-input { width: 100%; border: none; outline: none; background: transparent; font-size: 0.95rem; color: #1f2937; font-family: inherit; }
        .form-input::placeholder { color: #a1887f; }
        textarea.form-input { resize: none; min-height: 100px; }
        .btn-submit { width: 100%; padding: 0.875rem 1rem; background: #6d4c41; color: #fff; border: none; border-radius: 12px; font-size: 1rem; font-weight: 600; cursor: pointer; box-shadow: 0 4px 12px rgba(109,76,65,0.25); transition: all 0.2s; font-family: inherit; display: inline-flex; align-items: center; justify-content: center; gap: 0.5rem; }
        .btn-submit:hover { background: #5d4037; transform: translateY(-2px); }
        .btn-back { display: block; width: 100%; margin-top: 1rem; padding: 0.75rem 1rem; background: transparent; color: #6d4c41; border: 2px solid #a1887f; border-radius: 12px; font-size: 0.95rem; font-weight: 600; text-decoration: none; text-align: center; transition: all 0.2s; }
        .btn-back:hover { background: #efebe9; }

        /* ALERTS */
        .alert { border-radius: 12px; padding: 1rem; margin-bottom: 1rem; }
        .alert-success { background: #f5eee6; border: 2px solid #a1887f; text-align: center; }
        .alert-success h2 { font-size: 1.25rem; font-weight: 700; color: #6d4c41; margin: 0.5rem 0; }
        .alert-success p { color: #a1887f; margin: 0 0 1rem 0; font-size: 0.9rem; }
        .alert-error { background: #fee2e2; border: 2px solid #ef4444; }
        .alert-error p { color: #dc2626; font-weight: 500; margin: 0; font-size: 0.85rem; }
        .success-icon { width: 64px; height: 64px; background: #d7ccc8; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto; }
        .btn-another { padding: 0.6rem 1.5rem; background: #a1887f; color: #fff; border: none; border-radius: 10px; font-size: 0.9rem; font-weight: 600; cursor: pointer; transition: all 0.2s; }
        .btn-another:hover { background: #6d4c41; }

        /* FOOTER */
        .footer { background: #4e342e; color: #fff; padding: 3rem 5% 1.5rem; margin-top: auto; }
        .footer-inner { max-width: 1400px; margin: 0 auto; display: grid; grid-template-columns: 2fr 1fr 1fr 1.5fr; gap: 2.5rem; padding-bottom: 2rem; border-bottom: 1px solid rgba(255,255,255,0.1); }
        .footer-brand-name { font-size: 1.5rem; font-weight: 800; color: #fff; margin-bottom: 0.75rem; }
        .footer-brand-desc { font-size: 0.9rem; color: rgba(255,255,255,0.7); line-height: 1.6; max-width: 300px; }
        .footer-col-title { color: white; margin-bottom: 1rem; font-size: 1.1rem; font-weight: 600; }
        .footer-links { display: flex; flex-direction: column; gap: 0.75rem; }
        .footer-col a { display: block; color: rgba(255,255,255,0.7); text-decoration: none; font-size: 0.9rem; transition: color 0.2s; }
        .footer-col a:hover { color: #6d4c41; }
        .footer-contact { list-style: none; padding: 0; margin: 0; }
        .footer-contact li { display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.75rem; color: rgba(255,255,255,0.7); font-size: 0.9rem; }
        .footer-contact i { color: #6d4c41; font-size: 1.2rem; }
        .footer-bottom { text-align: center; padding-top: 1.5rem; border-top: 1px solid rgba(255,255,255,0.1); color: rgba(255,255,255,0.5); font-size: 0.9rem; max-width: 1400px; margin: 0 auto; }

        @media (max-width: 960px) { 
            .footer-inner { grid-template-columns: 1fr 1fr; } 
            .nav { padding: 1rem 1.5rem; }
        }
        @media (max-width: 768px) {
            .content-grid { grid-template-columns: 1fr; }
            .info-cards { order: 2; }
        }
        @media (max-width: 600px) { 
            .footer-inner { grid-template-columns: 1fr; }
            .nav { padding: 0.75rem 1rem; }
            .nav-logo span { font-size: 1.1rem; }
            .nav-links a { padding: 0.5rem 1rem; font-size: 0.85rem; }
            .main-content { padding: 2rem 1rem; }
            .form-card { padding: 1.5rem; }
        }
    </style>
</head>
<body>

<!-- HEADER -->
<nav class="nav">
    <a href="{{ url('/') }}" class="nav-logo">
        <img src="https://th.bing.com/th/id/R.2f6964b62e896f5f94f40390e330a1f8?rik=xmu%2bEOduizkGYw&riu=http%3a%2f%2f1.bp.blogspot.com%2f-unYxCWFMHHg%2fVkwfiPzQihI%2fAAAAAAAAq2w%2fhxBh0S_eU3A%2fs1600%2fQuail-Bird-Eggs-(4).jpg&ehk=NmS88wB52fy2LF8VygVGin9Yh5Nn9arRYJNlwN1WaGQ%3d&risl=&pid=ImgRaw&r=0" alt="Logo">
        <span>Escalona's Farm</span>
    </a>
    <div class="nav-links">
        <a href="{{ route('customer.dashboard') }}">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" width="16" height="16" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Back to Dashboard
        </a>
    </div>
</nav>

<!-- MAIN CONTENT -->
<main class="main-content">
    <div class="container">
        <div class="page-header">
            <div class="page-logo">
                <img src="https://th.bing.com/th/id/R.2f6964b62e896f5f94f40390e330a1f8?rik=xmu%2bEOduizkGYw&riu=http%3a%2f%2f1.bp.blogspot.com%2f-unYxCWFMHHg%2fVkwfiPzQihI%2fAAAAAAAAq2w%2fhxBh0S_eU3A%2fs1600%2fQuail-Bird-Eggs-(4).jpg&ehk=NmS88wB52fy2LF8VygVGin9Yh5Nn9arRYJNlwN1WaGQ%3d&risl=&pid=ImgRaw&r=0" alt="Logo">
            </div>
            <h1 class="page-title">Contact Support</h1>
            <p class="page-subtitle">We're here to help! Reach out to us anytime</p>
        </div>

        <div class="content-grid">
            <!-- Left Info Cards -->
            <div class="info-cards">
                <div class="info-card">
                    <div class="info-card-inner">
                        <div class="info-card-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="#6d4c41" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                            </svg>
                        </div>
                        <div class="info-card-content">
                            <h3>Phone</h3>
                            <p>Mon–Fri 8AM–5PM<br><strong>+63 912 345 6789</strong></p>
                        </div>
                    </div>
                </div>

                <div class="info-card">
                    <div class="info-card-inner">
                        <div class="info-card-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="#6d4c41" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <div class="info-card-content">
                            <h3>Email</h3>
                            <p>We reply within 24 hours<br><strong>support@escalonafarm.com</strong></p>
                        </div>
                    </div>
                </div>

                <div class="info-card">
                    <div class="info-card-inner">
                        <div class="info-card-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="#6d4c41" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </div>
                        <div class="info-card-content">
                            <h3>Office</h3>
                            <p>Pagkakaisa, Naujan, Oriental Mindoro</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Form -->
            <div>
                <div class="form-card">
                    @if(session('success'))
                    <div class="alert alert-success">
                        <div class="success-icon">
                            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#a1887f" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                        </div>
                        <h2>Message Sent!</h2>
                        <p>Natanggap na namin ang inyong mensahe.<br>Susugotin namin kayo sa lalong madaling panahon.</p>
                        <button onclick="location.reload()" class="btn-another">Send Another Message</button>
                    </div>
                    @else
                    <h2 class="form-title">Send us a Message</h2>
                    <p class="form-subtitle">Fill out the form below and we'll respond as soon as possible</p>

                    @if($errors->any())
                    <div class="alert alert-error">
                        @foreach($errors->all() as $error)
                            <p>⚠️ {{ $error }}</p>
                        @endforeach
                    </div>
                    @endif

                    <form method="POST" action="{{ route('contact.store') }}">
                        @csrf

                        <div class="form-group">
                            <label class="form-label">Full Name</label>
                            <div class="form-input-wrapper">
                                <input type="text" name="fullname" class="form-input" value="{{ old('fullname') }}" placeholder="Juan Dela Cruz" />
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Email Address</label>
                            <div class="form-input-wrapper">
                                <input type="email" name="email" class="form-input" value="{{ old('email') }}" placeholder="your@email.com" />
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Subject</label>
                            <div class="form-input-wrapper">
                                <input type="text" name="subject" class="form-input" value="{{ old('subject') }}" placeholder="What can we help you with?" />
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Message</label>
                            <div class="form-input-wrapper">
                                <textarea name="message" class="form-input" placeholder="Please describe your issue or question in detail...">{{ old('message') }}</textarea>
                            </div>
                        </div>

                        <button type="submit" class="btn-submit">
                            ✉️ Send Message
                        </button>
                    </form>
                    @endif
                </div>

                <a href="{{ route('login') }}" class="btn-back">← Back to Login</a>
            </div>
        </div>
    </div>
</main>

<!-- FOOTER -->
<footer class="footer">
    <div class="footer-inner">
        <div>
            <div class="footer-brand-name">Escalona's Farm</div>
            <div class="footer-brand-desc">Our Intelligent Quail Management System (SQUIFM) automatically monitors conditions ensures that our birds live in the optimal environment, resulting in healthier livestock and vastly superior products.</div>
        </div>
        <div class="footer-col">
            <div class="footer-col-title">Quick Links</div>
            <div class="footer-links">
                <a href="{{ url('/') }}">Home</a>
                <a href="{{ url('/#about') }}">About</a>
                <a href="{{ route('contact.support') }}">Contact</a>
            </div>
        </div>
        <div class="footer-col">
            <div class="footer-col-title">Account</div>
            <div class="footer-links">
                <a href="{{ route('customer.login') }}">Login</a>
                <a href="{{ route('customer.register') }}">Sign Up</a>
            </div>
        </div>
        <div class="footer-col">
            <div class="footer-col-title">Contact Info</div>
            <ul class="footer-contact">
                <li><i class="ph-fill ph-map-pin"></i> Pagkakaisa, Naujan, Or. Mindoro</li>
                <li><i class="ph-fill ph-phone"></i> +63 917 123 4567</li>
                <li><i class="ph-fill ph-envelope-simple"></i> escalona.farm@gmail.com</li>
            </ul>
        </div>
    </div>
    <div class="footer-bottom">&copy; {{ date('Y') }} Escalona's Farm | Capstone SQUIFM. All rights reserved.</div>
</footer>

</body>
</html>
