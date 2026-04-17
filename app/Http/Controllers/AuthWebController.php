<?php

namespace App\Http\Controllers;


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
            'phone'    => ['required', 'string'],
            'password' => ['required'],
            'g-recaptcha-response' => ['required'],
        ], [
            'g-recaptcha-response.required' => 'Silakan centang captcha terlebih dahulu.',
        ]);

        $credentials = $request->only(['phone', 'password']);

        $throttleKey = $this->throttleKey($request);

        if (RateLimiter::tooManyAttempts($throttleKey, 7)) {
            $seconds = RateLimiter::availableIn($throttleKey);

            return back()->withErrors([
                'phone' => 'Terlalu banyak percobaan login. Coba lagi dalam '.$seconds.' detik.',
            ])->onlyInput('phone');
        }

        if (! $this->verifyCaptcha($request->input('g-recaptcha-response'), $request->ip())) {
            RateLimiter::hit($throttleKey, 60);

            return back()->withErrors([
                'captcha' => 'Captcha gagal. Pastikan Anda bukan robot dan coba lagi.',
            ])->onlyInput('phone');
        }

        if (Auth::attempt($credentials, $request->remember)) {
            $request->session()->regenerate();
            RateLimiter::clear($throttleKey);

            $user = Auth::user();

            // Pastikan nomor HP sudah diverifikasi
            if (! $user->phone_verified_at) {
                Auth::logout();
                return back()->withErrors([
                    'phone' => 'Akun Anda belum diverifikasi. Silakan klik link verifikasi di WhatsApp kamu.',
                ])->onlyInput('phone');
            }

            // Pastikan akun sudah disetujui admin (access code harus aktif)
            $accessCode = $user->accessCode;
            if (! $accessCode || ! $accessCode->is_active) {
                Auth::logout();
                return back()->withErrors([
                    'phone' => 'Akun Anda belum disetujui oleh admin. Silakan tunggu persetujuan lebih lanjut.',
                ])->onlyInput('phone');
            }

            if (! $this->isUserEligibleForAccess($user)) {
                Auth::logout();
                return back()->withErrors([
                    'phone' => 'Akses ditolak. Akun Anda tidak memenuhi syarat wilayah Arradea.',
                ])->onlyInput('phone');
            }

            $ipWarning = $this->buildIpWarning($request->ip());
            if ($ipWarning) {
                session()->flash('warning', $ipWarning);
            }

            return $this->redirectUser($user);
        }

        RateLimiter::hit($throttleKey, 60);

        return back()->withErrors([
            'phone' => 'Kredensial yang Anda berikan tidak cocok dengan data kami.',
        ])->onlyInput('phone');
    }

    /**
     * Handle Web Registration.
     */
    public function register(Request $request)
    {
        $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'phone'    => ['required', 'string', 'max:20', 'unique:users,phone'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ], [
            'phone.unique' => 'Nomor HP sudah terdaftar.',
        ]);

        $user = User::create([
            'name'           => $request->name,
            'phone'          => $request->phone,
            'wilayah'        => 'Arradea',
            'access_code_id' => null,
            'password'       => Hash::make($request->password),
            'is_seller'      => false,
        ]);

        // Generate & kirim OTP via WhatsApp (user belum login)
        $otp = \App\Models\Otp::createForPhone($request->phone);

        Http::withHeaders(['Authorization' => env('FONNTE_TOKEN')])
            ->post('https://api.fonnte.com/send', [
                'target'  => $request->phone,
                'message' => "Halo {$user->name}!\n\nKode verifikasi nomor HP kamu untuk daftar di Arradea:\n\n*{$otp->code}*\n\n_Kode berlaku 10 menit. Jangan bagikan kode ini ke siapa pun._",
            ]);

        // Simpan phone di session untuk halaman verifikasi
        session(['register_phone' => $request->phone]);

        return redirect()->route('verification.phone.notice');
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

        return redirect('/')->with('success', '✅ Nomor HP berhasil diverifikasi!');
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
     * Build unique login throttle key by phone + IP.
     */
    protected function throttleKey(Request $request): string
    {
        return Str::transliterate(Str::lower((string) $request->input('phone'))).'|'.$request->ip();
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
