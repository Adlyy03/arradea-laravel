<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\URL;

class PhoneVerificationController extends Controller
{
    public function send(Request $request)
    {
        $user = $request->user();

        if ($user->phone_verified_at) {
            return redirect('/dashboard')->with('status', 'Nomor HP sudah terverifikasi.');
        }

        $verifyUrl = URL::temporarySignedRoute(
            'verification.phone.verify',
            now()->addMinutes(60),
            [
                'id'   => $user->id,
                'hash' => sha1($user->phone),
            ]
        );

        $this->sendWhatsApp(
            $user->phone,
            "Halo {$user->name}!\n\nKlik link berikut untuk verifikasi nomor HP kamu:\n\n{$verifyUrl}\n\n_Link berlaku 60 menit._"
        );

        return back()->with('status', 'Link verifikasi sudah dikirim ke WhatsApp kamu!');
    }

    public function verify(Request $request, $id, $hash)
{
    $user = User::findOrFail($id);

    if (!hash_equals(sha1($user->phone), $hash)) {
        abort(403, 'Link verifikasi tidak valid.');
    }

    if ($user->phone_verified_at) {
        return redirect('/')->with('success', 'Nomor HP sudah terverifikasi.');
    }

    $user->update(['phone_verified_at' => now()]);

    if ($user->role === 'admin') {
        return redirect('/admin/dashboard')->with('success', '✅ Nomor HP berhasil diverifikasi!');
    }

    if ($user->is_seller) {
        return redirect('/seller/dashboard')->with('success', '✅ Nomor HP berhasil diverifikasi!');
    }

    return redirect('/')->with('success', 'Selamat datang di Arradea, ' . explode(' ', $user->name)[0] . '!');
}

    private function sendWhatsApp(string $phone, string $message): void
    {
        Http::withHeaders(['Authorization' => env('FONNTE_TOKEN')])
            ->post('https://api.fonnte.com/send', [
                'target'  => $phone,
                'message' => $message,
            ]);
    }
}