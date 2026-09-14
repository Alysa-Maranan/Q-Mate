<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Customer Login | Escalona's Farm</title>
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
        .form-link:hover { color: #6d4c41; }
        .btn-login { width: 100%; padding: 0.875rem 1rem; background: #6d4c41; color: #fff; border: none; border-radius: 12px; font-size: 1rem; font-weight: 600; cursor: pointer; box-shadow: 0 4px 12px rgba(109,76,65,0.25); transition: all 0.2s; font-family: inherit; }
        .btn-login:hover { background: #5d4037; transform: translateY(-2px); }
        .form-divider { text-align: center; margin: 1.5rem 0; }
        .form-divider-text { font-size: 0.9rem; color: #a1887f; margin-bottom: 0.75rem; }
        .btn-register { display: block; width: 100%; padding: 0.875rem 1rem; background: transparent; color: #6d4c41; border: 2px solid #a1887f; border-radius: 12px; font-size: 0.95rem; font-weight: 600; text-decoration: none; text-align: center; transition: all 0.2s; }
        .btn-register:hover { background: #efebe9; }
        .help-link { text-align: center; margin-top: 1.5rem; font-size: 0.875rem; color: #a1887f; }
        .help-link a { color: #6d4c41; text-decoration: underline; font-weight: 500; }
        
        .form-icon { width: 20px; height: 20px; flex-shrink: 0; color: #a1887f; }

        /* ALERTS */
        .alert { border-radius: 12px; padding: 1rem; margin-bottom: 1rem; text-align: center; }
        .alert-error { background: #fee2e2; border: 2px solid #ef4444; }
        .alert-error p { color: #dc2626; font-weight: 600; margin: 0; }
        .alert-success { background: #d4edda; border: 2px solid #28a745; }
        .alert-success p { color: #155724; font-weight: 600; margin: 0; }

        /* FOOTER */
        .footer { background: #4e342e; color: #fff; padding: 2.5rem 2rem 1.5rem; margin-top: auto; }
        .footer-inner { max-width: 1200px; margin: 0 auto; display: grid; grid-template-columns: 2fr 1fr 1fr 1.5fr; gap: 2.5rem; padding-bottom: 2rem; border-bottom: 1px solid rgba(255,255,255,0.1); }
        .footer-brand-name { font-size: 1.2rem; font-weight: 800; color: #fff; margin-bottom: 0.5rem; }
        .footer-brand-desc { font-size: 0.85rem; color: rgba(255,255,255,0.7); line-height: 1.6; margin-bottom: 1rem; }
        .footer-social { display: flex; gap: 0.75rem; }
        .footer-social a { width: 34px; height: 34px; background: rgba(255,255,255,0.1); border-radius: 8px; display: flex; align-items: center; justify-content: center; text-decoration: none; font-size: 1rem; transition: background 0.2s; color: #fff; }
        .footer-social a:hover { background: rgba(255,255,255,0.25); }
        .footer-col-title { font-size: 0.85rem; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; color: #fff; margin-bottom: 0.85rem; }
        .footer-col a { display: block; color: rgba(255,255,255,0.7); text-decoration: none; font-size: 0.85rem; margin-bottom: 0.5rem; transition: color 0.2s; }
        .footer-col a:hover { color: #6d4c41; }
        .footer-contact-line { font-size: 0.85rem; color: rgba(255,255,255,0.7); margin-bottom: 0.5rem; display: flex; align-items: flex-start; gap: 0.5rem; }
        .footer-contact-line a { color: rgba(255,255,255,0.7); text-decoration: none; }
        .footer-contact-line a:hover { color: #6d4c41; }
        .footer-bottom { max-width: 1200px; margin: 1.25rem auto 0; text-align: center; font-size: 0.8rem; color: rgba(255,255,255,0.5); }

        @media (max-width: 960px) { 
            .footer-inner { grid-template-columns: 1fr 1fr; } 
            .nav { padding: 1rem 1.5rem; }
        }
        @media (max-width: 600px) { 
            .footer-inner { grid-template-columns: 1fr; }
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
            <h1 class="login-title">Customer Login</h1>
            <p class="login-subtitle">Welcome back to Escalona's Farm</p>
        </div>

        @if($errors->any())
        <div class="alert alert-error">
            <p>❌ {{ $errors->first() }}</p>
        </div>
        @endif

        @if(session('success'))
        <div class="alert alert-success">
            <p>✅ {{ session('success') }}</p>
        </div>
        @endif

        <div class="login-card">
            <form method="POST" action="{{ route('customer.login') }}">
                @csrf
                <div class="form-group">
                    <label for="email" class="form-label">Email Address</label>
                    <div class="form-input-wrapper">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" width="20" height="20" stroke-width="2" class="form-icon"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" /></svg>
                        <input id="email" name="email" type="email" class="form-input" required placeholder="juan@example.com" value="{{ old('email') }}" />
                    </div>
                </div>

                <div class="form-group">
                    <label for="password" class="form-label">Password</label>
                    <div class="form-input-wrapper">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" width="20" height="20" class="form-icon"><path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/></svg>
                        <input id="password" name="password" type="password" class="form-input" required placeholder="••••••••" />
                        <svg onclick="togglePassword('password')" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" width="20" height="20" stroke-width="2" style="cursor:pointer;" class="form-icon"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                    </div>
                </div>

                <div class="form-row">
                    <label class="form-checkbox">
                        <input type="checkbox" name="remember" value="1"> Remember me
                    </label>
                    <a href="#" class="form-link">Forgot password?</a>
                </div>

                <button type="submit" class="btn-login">Login</button>

                <div class="form-divider">
                    <p class="form-divider-text">Don't have an account?</p>
                    <a href="{{ route('customer.register') }}" class="btn-register">Create Customer Account</a>
                </div>
            </form>
        </div>

        <div class="help-link">
            Need help? <a href="{{ url('/contact-support') }}">Contact Support</a>
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
            <a href="{{ url('/') }}">Home</a>
            <a href="{{ url('/#about') }}">About</a>
            <a href="{{ route('contact.support') }}">Contact</a>
        </div>
        <div class="footer-col">
            <div class="footer-col-title">Account</div>
            <a href="{{ route('customer.login') }}">Login</a>
            <a href="{{ route('customer.register') }}">Sign Up</a>
        </div>
        <div class="footer-col">
            <div class="footer-col-title">Contact Info</div>
            <div class="footer-contact-line"><i class="ph-fill ph-map-pin" style="color:#6d4c41;"></i> Pagkakaisa, Naujan, Or. Mindoro</div>
            <div class="footer-contact-line"><i class="ph-fill ph-phone" style="color:#6d4c41;"></i> +63 917 123 4567</div>
            <div class="footer-contact-line"><i class="ph-fill ph-envelope-simple" style="color:#6d4c41;"></i> escalona.farm@gmail.com</div>
        </div>
    </div>
    <div class="footer-bottom">&copy; {{ date('Y') }} Escalona's Farm | Capstone SQUIFM. All rights reserved.</div>
</footer>

<script>
function togglePassword(fieldId) {
    const field = document.getElementById(fieldId);
    field.type = field.type === 'password' ? 'text' : 'password';
}
</script>

</body>
</html>