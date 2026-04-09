<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class AuthWebController extends Controller
{
    /**
     * Handle Web Login with Role-based Redirects.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $request->remember)) {
            $request->session()->regenerate();

            $user = Auth::user();
            return $this->redirectUser($user);
        }

        return back()->withErrors([
            'email' => 'Kredensial yang Anda berikan tidak cocok dengan data kami.',
        ])->onlyInput('email');
    }

    /**
     * Handle Web Registration with Role-based Redirects.
     */
    public function register(Request $request)
    {
        $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => 'buyer',
        ]);

        Auth::login($user);

        return $this->redirectUser($user);
    }

    /**
     * Logic for Role-based Redirection.
     */
    protected function redirectUser($user)
    {
        if ($user->role === 'admin') {
            return redirect('/admin/dashboard')->with('success', 'Selamat datang kembali, Admin!');
        }

        if ($user->role === 'seller') {
            return redirect('/seller/dashboard')->with('success', 'Selamat berjualan di Arradea!');
        }

        return redirect('/')->with('success', 'Selamat datang di Arradea, ' . explode(' ', $user->name)[0] . '!');
    }

    /**
     * Handle Web Logout.
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('success', 'Sampai jumpa kembali!');
    }
}
