<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('login');
    }

    public function login(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|email',
                'password' => 'required',
            ]);

            \Log::info('LOGIN STEP 1: Validation passed');

            $result = Auth::guard('web')->attempt(
                [
                    'email' => $request->email,
                    'password' => $request->password,
                ],
                (bool) $request->remember
            );

            \Log::info('LOGIN STEP 2: Auth attempt finished', [
                'result' => $result
            ]);

            if ($result) {
                \Log::info('LOGIN STEP 3: Authentication successful');

                $request->session()->regenerate();

                \Log::info('LOGIN STEP 4: Session regenerated');

                return redirect()->intended('/dashboard');
            }

            \Log::info('LOGIN STEP 5: Invalid credentials');

            return back()->withErrors([
                'email' => 'The provided credentials do not match our records.',
            ]);

        } catch (\Throwable $e) {

            \Log::error('LOGIN ERROR', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            return response()->json([
                'error' => 'Login failed',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function logout(Request $request)
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}