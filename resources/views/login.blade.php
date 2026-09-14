
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login | Escalona's Farm</title>
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
        .main-content { flex: 1; display: flex; align-items: center; justify-content: center; padding: 3rem 1rem; }
        .login-container { width: 100%; max-width: 420px; }
        .login-header { text-align: center; margin-bottom: 2rem; }
        .login-logo { width: 90px; height: 90px; border-radius: 22px; background: #fffdfa; border: 3px solid #a1887f; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 16px rgba(141,110,99,0.12); margin: 0 auto 1.25rem; overflow: hidden; }
        .login-logo img { width: 100%; height: 100%; object-fit: cover; }
        .login-title { font-size: 1.75rem; font-weight: 800; color: #6d4c41; margin-bottom: 0.5rem; }
        .login-subtitle { font-size: 0.95rem; color: #a1887f; }

        /* FORM */
        .login-card { background: #fffdfa; border-radius: 20px; box-shadow: 0 0 40px rgba(141,110,99,0.12), 0 10px 30px rgba(141,110,99,0.10); border: 2px solid #a1887f; padding: 2.5rem; }
        .form-group { margin-bottom: 1.5rem; }
        .form-label { display: block; font-size: 0.9rem; font-weight: 600; color: #6d4c41; margin-bottom: 0.5rem; }
        .form-input-wrapper { display: flex; align-items: center; gap: 0.75rem; background: #f5eee6; border: 1.5px solid #a1887f; padding: 0.875rem 1rem; border-radius: 12px; }
        .form-input { flex: 1; border: none; outline: none; background: transparent; font-size: 1rem; color: #1f2937; font-family: inherit; }
        .form-input::placeholder { color: #a1887f; }
        .form-row { display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.75rem; }
        .form-checkbox { display: flex; align-items: center; font-size: 0.875rem; color: #a1887f; cursor: pointer; }
        .form-checkbox input { width: 16px; height: 16px; margin-right: 0.5rem; accent-color: #a1887f; cursor: pointer; }
        .form-link { font-size: 0.875rem; color: #a1887f; text-decoration: none; }
        .password-toggle { background: none; border: none; cursor: pointer; padding: 0.5rem; display: flex; align-items: center; justify-content: center; transition: all 0.2s; }
        .form-link:hover { color: #6d4c41; }
        .btn-login { width: 100%; padding: 0.875rem 1rem; background: #6d4c41; color: #fff; border: none; border-radius: 12px; font-size: 1rem; font-weight: 600; cursor: pointer; box-shadow: 0 4px 12px rgba(109,76,65,0.25); transition: all 0.2s; font-family: inherit; }
        .btn-login:hover { background: #5d4037; transform: translateY(-2px); }
        .btn-google { display: flex; align-items: center; justify-content: center; gap: 0.75rem; width: 100%; padding: 0.875rem 1rem; background: #fff; color: #374151; border: 2px solid #e5e7eb; border-radius: 12px; font-size: 0.95rem; font-weight: 600; text-decoration: none; cursor: pointer; transition: all 0.2s; margin-top: 1rem; }
        .btn-google:hover { background: #f9fafb; border-color: #d1d5db; }
        .btn-google img { width: 1.25em; height: 1.25em; }
        .form-divider { text-align: center; margin: 1.5rem 0; }
        .form-divider-text { font-size: 0.9rem; color: #a1887f; margin-bottom: 0.75rem; }
        .btn-register { display: block; width: 100%; padding: 0.875rem 1rem; background: transparent; color: #6d4c41; border: 2px solid #a1887f; border-radius: 12px; font-size: 0.95rem; font-weight: 600; text-decoration: none; text-align: center; transition: all 0.2s; }
        .btn-register:hover { background: #efebe9; }
        .help-link { text-align: center; margin-top: 1.5rem; font-size: 0.875rem; color: #a1887f; }
        .help-link a { color: #6d4c41; text-decoration: underline; font-weight: 500; }

        /* ALERTS */
        .alert { border-radius: 12px; padding: 1rem; margin-bottom: 1rem; text-align: center; }
        .alert-success { background: #f5eee6; border: 2px solid #a1887f; }
        .alert-success p { color: #6d4c41; font-weight: 600; margin: 0; }
        .alert-error { background: #fee2e2; border: 2px solid #ef4444; }
        .alert-error p { color: #dc2626; font-weight: 600; margin: 0; }

        /* FOOTER */
        .footer { background: #4e342e; color: #fff; padding: 4rem 5% 2rem; margin-top: 4rem; }
        .footer-grid { display: grid; grid-template-columns: 2fr 1fr 1fr 1.5fr; gap: 3rem; margin-bottom: 3rem; max-width: 1400px; margin-left: auto; margin-right: auto;}
        .footer-brand h3 { font-size: 1.5rem; font-weight: 800; color: #fff; margin-bottom: 1rem; }
        .footer-brand p { color: rgba(255,255,255,0.7); line-height: 1.6; max-width: 300px; }
        .footer h4 { color: white; margin-bottom: 1.5rem; font-size: 1.1rem; }
        .footer-links { display: flex; flex-direction: column; gap: 0.75rem; }
        .footer-links a { color: rgba(255,255,255,0.7); text-decoration: none; }
        .footer-links a:hover { color: #ffe0b2; }
        .footer-contact { list-style: none; padding: 0; }
        .footer-contact li { display: flex; gap: 0.75rem; margin-bottom: 1rem; color: rgba(255,255,255,0.7); }
        .footer-contact i { color: #ffe0b2; font-size: 1.2rem; }
        .footer-bottom { text-align: center; padding-top: 2rem; border-top: 1px solid rgba(255,255,255,0.1); color: rgba(255,255,255,0.5); font-size: 0.9rem; }

        @media (max-width: 960px) { 
            .footer-grid { grid-template-columns: 1fr 1fr; }
            .nav { padding: 1rem 1.5rem; }
        }
        @media (max-width: 600px) { 
            .footer-grid { grid-template-columns: 1fr; }
            .nav { padding: 0.75rem 1rem; }
            .nav-logo span { font-size: 1.1rem; }
            .nav-links a { padding: 0.5rem 1rem; font-size: 0.85rem; }
            .login-card { padding: 1.75rem; }
            .main-content { padding: 2rem 1rem; }
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
    <div class="login-container">
        <div class="login-header">
            <div class="login-logo">
                <img src="https://th.bing.com/th/id/R.2f6964b62e896f5f94f40390e330a1f8?rik=xmu%2bEOduizkGYw&riu=http%3a%2f%2f1.bp.blogspot.com%2f-unYxCWFMHHg%2fVkwfiPzQihI%2fAAAAAAAAq2w%2fhxBh0S_eU3A%2fs1600%2fQuail-Bird-Eggs-(4).jpg&ehk=NmS88wB52fy2LF8VygVGin9Yh5Nn9arRYJNlwN1WaGQ%3d&risl=&pid=ImgRaw&r=0" alt="Logo">
            </div>
            <h1 class="login-title">Welcome Back!</h1>
            <p class="login-subtitle">Please login to continue to your dashboard</p>
        </div>

        @if(session('success'))
        <div class="alert alert-success">
            <p>✅ {{ session('success') }}</p>
        </div>
        @endif

        @if($errors->any())
        <div class="alert alert-error">
            <p>❌ {{ $errors->first() }}</p>
        </div>
        @endif

        <div class="login-card">
            <form method="POST" action="{{ route('login') }}">
                @csrf
                <div class="form-group">
                    <label for="email" class="form-label">Email Address</label>
                    <div class="form-input-wrapper">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="#a1887f" width="20" height="20" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" /></svg>
                        <input id="email" name="email" type="email" class="form-input" required placeholder="your@email.com" />
                    </div>
                </div>

                <div class="form-group">
                    <label for="password" class="form-label">Password</label>
                    <div class="form-input-wrapper">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="#a1887f" width="20" height="20"><path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/></svg>
                        <input id="password" name="password" type="password" class="form-input" required placeholder="••••••••" />
                        <button type="button" class="password-toggle" onclick="togglePassword()">
                            <svg id="eyeIcon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="#a1887f" stroke-width="2" width="20" height="20">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                <circle cx="12" cy="12" r="3"></circle>
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="form-row">
                    <label class="form-checkbox">
                        <input type="checkbox" name="remember" value="1"> Remember me
                    </label>
                    @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="form-link">Forgot password?</a>
                    @endif
                </div>

                <button type="submit" class="btn-login">Login</button>


                <div class="form-divider">
                    <p class="form-divider-text">Don't have an account?</p>
                    <a href="{{ route('register') }}" class="btn-register">Create New Account</a>
                </div>
            </form>
        </div>

        <div class="help-link">
            Need help? <a href="{{ route('contact.support') }}">Contact Support</a>
        </div>
    </div>
</main>

<!-- FOOTER -->
<footer class="footer">
    <div class="footer-grid">
        <div class="footer-brand">
              <h3>Escalona's Farm</h3>
              <p>Our Intelligent Quail Management System (SQUIFM) automatically monitors conditions ensures that our birds live in the optimal environment, resulting in healthier livestock and vastly superior products.</p>
        </div>
        <div>
            <h4>Quick Links</h4>
            <div class="footer-links">
                <a href="{{ url('/') }}">Home</a>
                <a href="{{ url('/#about') }}">About</a>
                <a href="{{ route('products') }}">Shop</a>
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

<script>
function togglePassword() {
    const passwordInput = document.getElementById('password');
    const eyeIcon = document.getElementById('eyeIcon');
    
    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        eyeIcon.innerHTML = '<path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1 4.24 4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line>';
    } else {
        passwordInput.type = 'password';
        eyeIcon.innerHTML = '<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle>';
    }
}
</script>

</body>
</html>