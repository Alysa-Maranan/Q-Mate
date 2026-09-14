<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Reset Password | Escalona's Farm</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Inter', sans-serif; background: #fffdfa; color: #4e342e; min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 2rem; }
        .container { width: 100%; max-width: 420px; }
        .card { background: #fffdfa; border-radius: 20px; box-shadow: 0 0 40px rgba(141,110,99,0.12); border: 2px solid #a1887f; padding: 2.5rem; text-align: center; }
        .title { font-size: 1.5rem; font-weight: 700; color: #6d4c41; margin-bottom: 0.5rem; }
        .subtitle { font-size: 0.9rem; color: #a1887f; margin-bottom: 2rem; }
        .form-group { margin-bottom: 1.5rem; text-align: left; }
        .form-label { display: block; font-size: 0.9rem; font-weight: 600; color: #6d4c41; margin-bottom: 0.5rem; }
        .form-input { width: 100%; padding: 0.875rem; border: 1.5px solid #a1887f; border-radius: 12px; font-size: 1rem; background: #f5eee6; }
        .btn { width: 100%; padding: 0.875rem; background: #6d4c41; color: #fff; border: none; border-radius: 12px; font-size: 1rem; font-weight: 600; cursor: pointer; margin-bottom: 1rem; }
        .btn:hover { background: #5d4037; }
        .link { color: #6d4c41; text-decoration: none; font-size: 0.9rem; }
        .link:hover { text-decoration: underline; }
        .alert { padding: 1rem; border-radius: 12px; margin-bottom: 1rem; }
        .alert-error { background: #fee2e2; border: 2px solid #ef4444; color: #dc2626; }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <h1 class="title">Reset Password</h1>
            <p class="subtitle">Enter your new password below.</p>

            @if ($errors->any())
                <div class="alert alert-error">{{ $errors->first() }}</div>
            @endif

            <form method="POST" action="{{ route('password.update') }}">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">

                <div class="form-group">
                    <label for="email" class="form-label">Email Address</label>
                    <input id="email" name="email" type="email" class="form-input" required value="{{ $email ?? old('email') }}" readonly>
                </div>

                <div class="form-group">
                    <label for="password" class="form-label">New Password</label>
                    <input id="password" name="password" type="password" class="form-input" required placeholder="••••••••">
                </div>

                <div class="form-group">
                    <label for="password_confirmation" class="form-label">Confirm Password</label>
                    <input id="password_confirmation" name="password_confirmation" type="password" class="form-input" required placeholder="••••••••">
                </div>

                <button type="submit" class="btn">Reset Password</button>
            </form>

            <a href="{{ route('login') }}" class="link">← Back to Login</a>
        </div>
    </div>
</body>
</html>