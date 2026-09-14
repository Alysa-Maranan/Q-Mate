<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Create Account | Escalona's Farm</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
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
        .main-content { flex: 1; display: flex; align-items: center; justify-content: center; padding: 2.5rem 1rem; }
        .register-container { width: 100%; max-width: 440px; }
        .register-header { text-align: center; margin-bottom: 1.5rem; }
        .register-logo { width: 80px; height: 80px; border-radius: 20px; background: #fffdfa; border: 3px solid #a1887f; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 16px rgba(141,110,99,0.12); margin: 0 auto 1rem; overflow: hidden; }
        .register-logo img { width: 100%; height: 100%; object-fit: cover; }
        .register-title { font-size: 1.6rem; font-weight: 800; color: #6d4c41; margin-bottom: 0.4rem; }
        .register-subtitle { font-size: 0.9rem; color: #a1887f; }

        /* FORM */
        .register-card { background: #fffdfa; border-radius: 20px; box-shadow: 0 0 40px rgba(141,110,99,0.12), 0 10px 30px rgba(141,110,99,0.10); border: 2px solid #a1887f; padding: 2rem; }
        .form-group { margin-bottom: 1.25rem; }
        .form-label { display: block; font-size: 0.875rem; font-weight: 600; color: #6d4c41; margin-bottom: 0.4rem; }
        .form-input-wrapper { display: flex; align-items: center; gap: 0.75rem; background: #f5eee6; border: 1.5px solid #a1887f; padding: 0.75rem 1rem; border-radius: 12px; }
        .form-input { flex: 1; border: none; outline: none; background: transparent; font-size: 0.95rem; color: #1f2937; font-family: inherit; }
        .form-input::placeholder { color: #a1887f; }
        .form-checkbox-row { display: flex; align-items: flex-start; gap: 0.5rem; margin-bottom: 1.5rem; }
        .form-checkbox-row input { width: 16px; height: 16px; margin-top: 2px; accent-color: #a1887f; cursor: pointer; }
        .form-checkbox-row label { font-size: 0.85rem; color: #a1887f; line-height: 1.4; }
        .form-checkbox-row label span { font-weight: 600; color: #6d4c41; }
        .btn-google { display: flex; align-items: center; justify-content: center; gap: 0.75rem; width: 100%; padding: 0.75rem 1rem; background: #fff; color: #374151; border: 2px solid #e5e7eb; border-radius: 12px; font-size: 0.95rem; font-weight: 600; text-decoration: none; cursor: pointer; transition: all 0.2s; margin-bottom: 1rem; }
        .btn-google:hover { background: #f9fafb; border-color: #d1d5db; }
        .btn-google img { width: 1.25em; height: 1.25em; }
        .btn-register { width: 100%; padding: 0.75rem 1rem; background: #6d4c41; color: #fff; border: none; border-radius: 12px; font-size: 1rem; font-weight: 600; cursor: pointer; box-shadow: 0 4px 12px rgba(109,76,65,0.25); transition: all 0.2s; font-family: inherit; margin-bottom: 1rem; }
        .btn-register:hover { background: #5d4037; transform: translateY(-2px); }
        .form-divider { text-align: center; margin-top: 1rem; }
        .form-divider-text { font-size: 0.875rem; color: #a1887f; margin-bottom: 0.6rem; }
        .btn-login { display: block; width: 100%; padding: 0.75rem 1rem; background: transparent; color: #6d4c41; border: 2px solid #a1887f; border-radius: 12px; font-size: 0.95rem; font-weight: 600; text-decoration: none; text-align: center; transition: all 0.2s; }
        .btn-login:hover { background: #efebe9; }
        .help-link { text-align: center; margin-top: 1.25rem; font-size: 0.875rem; color: #a1887f; }
        .help-link a { color: #6d4c41; text-decoration: underline; font-weight: 500; }
        .icon-emoji { font-size: 1.2rem; }

        /* FOOTER */
        .footer { background: #4e342e; color: #ffe0b2; padding: 2.5rem 2rem 1.5rem; margin-top: auto; }
        .footer-inner { max-width: 1200px; margin: 0 auto; display: grid; grid-template-columns: 2fr 1fr 1fr 1.5fr; gap: 2.5rem; padding-bottom: 2rem; border-bottom: 1px solid rgba(255,224,178,0.15); }
        .footer-brand-name { font-size: 1.2rem; font-weight: 800; color: #ffe0b2; margin-bottom: 0.5rem; }
        .footer-brand-desc { font-size: 0.85rem; color: rgba(255,224,178,0.7); line-height: 1.6; margin-bottom: 1rem; }
        .footer-social { display: flex; gap: 0.75rem; }
        .footer-social a { width: 34px; height: 34px; background: rgba(255,224,178,0.1); border-radius: 8px; display: flex; align-items: center; justify-content: center; text-decoration: none; font-size: 1rem; transition: background 0.2s; }
        .footer-social a:hover { background: rgba(255,224,178,0.25); }
        .footer-col-title { font-size: 0.85rem; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; color: #ffe0b2; margin-bottom: 0.85rem; }
        .footer-col a { display: block; color: rgba(255,224,178,0.7); text-decoration: none; font-size: 0.85rem; margin-bottom: 0.5rem; transition: color 0.2s; }
        .footer-col a:hover { color: #fffdfa; }
        .footer-contact-line { font-size: 0.85rem; color: rgba(255,224,178,0.7); margin-bottom: 0.5rem; display: flex; align-items: flex-start; gap: 0.5rem; }
        .footer-contact-line a { color: rgba(255,224,178,0.85); text-decoration: none; }
        .footer-contact-line a:hover { color: #fffdfa; }
        .footer-bottom { max-width: 1200px; margin: 1.25rem auto 0; text-align: center; font-size: 0.8rem; color: rgba(255,224,178,0.5); }

        @media (max-width: 960px) { 
            .footer-inner { grid-template-columns: 1fr 1fr; } 
            .nav { padding: 1rem 1.5rem; }
        }
        @media (max-width: 600px) { 
            .footer-inner { grid-template-columns: 1fr; }
            .nav { padding: 0.75rem 1rem; }
            .nav-logo span { font-size: 1.1rem; }
            .nav-links a { padding: 0.5rem 1rem; font-size: 0.85rem; }
            .register-card { padding: 1.5rem; }
            .main-content { padding: 1.5rem 1rem; }
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
        <a href="{{ url('/') }}">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" width="16" height="16" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Back to Home
        </a>
    </div>
</nav>

<!-- MAIN CONTENT -->
<main class="main-content">
    <div class="register-container">
        <div class="register-header">
            <div class="register-logo">
                <img src="https://th.bing.com/th/id/R.2f6964b62e896f5f94f40390e330a1f8?rik=xmu%2bEOduizkGYw&riu=http%3a%2f%2f1.bp.blogspot.com%2f-unYxCWFMHHg%2fVkwfiPzQihI%2fAAAAAAAAq2w%2fhxBh0S_eU3A%2fs1600%2fQuail-Bird-Eggs-(4).jpg&ehk=NmS88wB52fy2LF8VygVGin9Yh5Nn9arRYJNlwN1WaGQ%3d&risl=&pid=ImgRaw&r=0" alt="Logo">
            </div>
            <h1 class="register-title">Create Account</h1>
            <p class="register-subtitle">Join the smart quail farming system</p>
        </div>

        <div class="register-card">
            <form method="POST" action="{{ route('register.submit') }}">
                @csrf

                <div class="form-group">
                    <label for="fullname" class="form-label">Full Name</label>
                    <div class="form-input-wrapper">
                        <span class="icon-emoji">👤</span>
                        <input id="fullname" name="fullname" type="text" class="form-input" required placeholder="Juan Dela Cruz" />
                    </div>
                </div>

                <div class="form-group">
                    <label for="email" class="form-label">Email Address</label>
                    <div class="form-input-wrapper">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="#a1887f" width="20" height="20" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" /></svg>
                        <input id="email" name="email" type="email" class="form-input" required placeholder="your@email.com" />
                    </div>
                </div>

                <div class="form-group">
                    <label for="phone" class="form-label">Phone Number</label>
                    <div class="form-input-wrapper">
                        <span class="icon-emoji">📞</span>
                        <input id="phone" name="phone" type="text" class="form-input" required placeholder="+63 912 345 6789" />
                    </div>
                </div>

                <div class="form-group">
                    <label for="password" class="form-label">Password</label>
                    <div class="form-input-wrapper">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="#a1887f" width="20" height="20"><path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/></svg>
                        <input id="password" name="password" type="password" class="form-input" required placeholder="••••••••" />
                        <svg onclick="togglePassword('password')" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="#a1887f" width="20" height="20" stroke-width="2" style="cursor:pointer;"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                    </div>
                </div>

                <div class="form-group">
                    <label for="confirm_password" class="form-label">Confirm Password</label>
                    <div class="form-input-wrapper">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="#a1887f" width="20" height="20"><path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/></svg>
                        <input id="confirm_password" name="password_confirmation" type="password" class="form-input" required placeholder="••••••••" />
                        <svg onclick="togglePassword('confirm_password')" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="#a1887f" width="20" height="20" stroke-width="2" style="cursor:pointer;"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                    </div>
                </div>

                <div class="form-checkbox-row">
                    <input type="checkbox" required>
                    <label>I agree to the <span>Terms of Service</span> and <span>Privacy Policy</span></label>
                </div>

                <button type="submit" class="btn-register">Create Account</button>

                <a href="{{ route('login') }}" class="btn-login">Login to Existing Account</a>
            </form>
        </div>

        <div class="help-link">
            Need help? <a href="{{ route('contact.support') }}">Contact Support</a>
        </div>
    </div>
</main>

<!-- FOOTER -->
<footer class="footer">
    <div class="footer-inner">
        <div>
            <div class="footer-brand-name">Escalona's Farm</div>
            <div class="footer-brand-desc">An IoT-powered Smart Quail Management System designed to make farm life more efficient and produce higher quality yields.</div>
        </div>
        <div class="footer-col">
            <div class="footer-col-title">Quick Links</div>
            <a href="{{ url('/') }}">Home</a>
            <a href="{{ url('/#about') }}">About</a>
            <a href="{{ route('products') }}">Shop</a>
            <a href="{{ route('contact.support') }}">Contact</a>
        </div>
        <div class="footer-col">
            <div class="footer-col-title">Account</div>
            <a href="{{ route('login') }}">Login</a>
            <a href="{{ route('register') }}">Sign Up</a>
            <a href="#">Guest Tracking</a>
        </div>
        <div class="footer-col">
            <div class="footer-col-title">Contact Info</div>
            <div class="footer-contact-line">📍 Pagkakaisa, Naujan, Or. Mindoro</div>
            <div class="footer-contact-line">📞 +63 917 123 4567</div>
            <div class="footer-contact-line">📧 escalona.farm@gmail.com</div>
        </div>
    </div>
    <div class="footer-bottom">&copy; {{ date('Y') }} Escalona's Farm | Capstone SQUIFM. All rights reserved.</div>
</footer>

<script>
function togglePassword(id) {
    const input = document.getElementById(id);
    input.type = input.type === "password" ? "text" : "password";
}
</script>
</body>
</html>
