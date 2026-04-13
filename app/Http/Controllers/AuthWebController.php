<?php

namespace App\Http\Controllers;

use App\Models\AccessCode;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;

class AuthWebController extends Controller
{
    /**
     * Handle Web Login with Role-based Redirects.
     */
    public function login(Request $request)
    {
        $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
            'g-recaptcha-response' => ['required'],
        ], [
            'g-recaptcha-response.required' => 'Silakan centang captcha terlebih dahulu.',
        ]);

        $credentials = $request->only(['email', 'password']);

        $throttleKey = $this->throttleKey($request);

        if (RateLimiter::tooManyAttempts($throttleKey, 7)) {
            $seconds = RateLimiter::availableIn($throttleKey);

            return back()->withErrors([
                'email' => 'Terlalu banyak percobaan login. Coba lagi dalam '.$seconds.' detik.',
            ])->onlyInput('email');
        }

        if (! $this->verifyCaptcha($request->input('g-recaptcha-response'), $request->ip())) {
            RateLimiter::hit($throttleKey, 60);

            return back()->withErrors([
                'captcha' => 'Captcha gagal. Pastikan Anda bukan robot dan coba lagi.',
            ])->onlyInput('email');
        }

        if (Auth::attempt($credentials, $request->remember)) {
            $request->session()->regenerate();
            RateLimiter::clear($throttleKey);

            $user = Auth::user();

            if (! $this->isUserEligibleForAccess($user)) {
                Auth::logout();
                return back()->withErrors([
                    'email' => 'Akses ditolak. Akun Anda tidak memenuhi syarat wilayah Arradea.',
                ])->onlyInput('email');
            }

            $ipWarning = $this->buildIpWarning($request->ip());
            if ($ipWarning) {
                session()->flash('warning', $ipWarning);
            }

            return $this->redirectUser($user);
        }

        RateLimiter::hit($throttleKey, 60);

        return back()->withErrors([
            'email' => 'Kredensial yang Anda berikan tidak cocok dengan data kami.',
        ])->onlyInput('email');
    }

    /**
     * Handle Web Registration.
     */
    public function register(Request $request)
    {
        $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Password::defaults()],
            'access_code' => ['required', 'string', 'max:100'],
            'g-recaptcha-response' => ['required'],
        ], [
            'g-recaptcha-response.required' => 'Silakan centang captcha terlebih dahulu.',
            'access_code.required' => 'Akses ditolak. Kode tidak valid',
        ]);

        $accessCode = AccessCode::where('code', trim((string) $request->access_code))
            ->where('is_active', true)
            ->first();

        if (! $accessCode) {
            return back()->withErrors([
                'access_code' => 'Akses ditolak. Kode tidak valid',
            ])->withInput($request->except(['password', 'password_confirmation', 'g-recaptcha-response']));
        }

        if (! $this->verifyCaptcha($request->input('g-recaptcha-response'), $request->ip())) {
            return back()->withErrors([
                'captcha' => 'Captcha gagal. Mohon ulangi verifikasi.',
            ])->withInput($request->except(['password', 'password_confirmation', 'g-recaptcha-response']));
        }

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'wilayah'  => 'Arradea',
            'access_code_id' => $accessCode->id,
            'password' => Hash::make($request->password),
            'is_seller' => false,
        ]);

        Auth::login($user);

        return $this->redirectUser($user);
    }

    /**
     * Redirect user after authentication.
     */
    protected function redirectUser($user)
    {
        if ($user->role === 'admin') {
            return redirect('/admin/dashboard')->with('success', 'Selamat datang kembali, Admin!');
        }

        if ($user->is_seller) {
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

    /**
     * Verify Google reCAPTCHA token with API.
     */
    protected function verifyCaptcha(?string $token, ?string $ip = null): bool
    {
        if (app()->environment('testing')) {
            return true;
        }

        if (! $token) {
            return false;
        }

        $secret = config('services.recaptcha.secret_key');

        if (! $secret) {
            return false;
        }

        try {
            $response = Http::asForm()
                ->timeout(10)
                ->post('https://www.google.com/recaptcha/api/siteverify', [
                    'secret' => $secret,
                    'response' => $token,
                    'remoteip' => $ip,
                ]);

            if (! $response->ok()) {
                return false;
            }

            $result = $response->json();

            if (! (bool) data_get($result, 'success', false)) {
                return false;
            }

            return true;
        } catch (\Throwable $exception) {
            report($exception);

            return false;
        }
    }

    /**
     * Build unique login throttle key by email + IP.
     */
    protected function throttleKey(Request $request): string
    {
        return Str::transliterate(Str::lower((string) $request->input('email'))).'|'.$request->ip();
    }

    protected function isUserEligibleForAccess(User $user): bool
    {
        return strcasecmp((string) $user->wilayah, 'Arradea') === 0
            && (bool) optional($user->accessCode)->is_active;
    }

    protected function buildIpWarning(?string $ip): ?string
    {
        if (! $ip || app()->environment('testing')) {
            return null;
        }

        if (str_starts_with($ip, '127.') || $ip === '::1' || str_starts_with($ip, '192.168.') || str_starts_with($ip, '10.') || preg_match('/^172\.(1[6-9]|2\d|3[01])\./', $ip)) {
            return null;
        }

        return 'Peringatan: IP Anda terdeteksi di luar jaringan lokal. Pastikan Anda tetap warga Komplek Arradea.';
    }
}
