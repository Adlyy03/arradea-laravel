<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function edit(): View
    {
        return view('profile');
    }

    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:30', Rule::unique('users', 'phone')->ignore($user->id)],
            'current_password' => ['nullable', 'required_with:password', 'current_password'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ], [
            'current_password.current_password' => 'Password saat ini tidak sesuai.',
            'current_password.required_with' => 'Password saat ini wajib diisi untuk ganti password.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        $payload = [
            'name' => $validated['name'],
            'phone' => $validated['phone'],
        ];

        if (! empty($validated['password'])) {
            $payload['password'] = $validated['password'];
        }

        $user->update($payload);

        return back()->with('success', 'Akun berhasil diperbarui.');
    }

    public function destroy(Request $request): RedirectResponse
    {
        $request->validate([
            'delete_password' => ['required', 'string'],
            'delete_confirmation' => ['required', 'string', 'in:HAPUS AKUN'],
        ], [
            'delete_password.required' => 'Masukkan password untuk menghapus akun.',
            'delete_confirmation.required' => 'Ketik HAPUS AKUN untuk konfirmasi.',
            'delete_confirmation.in' => 'Teks konfirmasi tidak sesuai. Ketik persis: HAPUS AKUN.',
        ]);

        $user = $request->user();

        if (! Hash::check($request->input('delete_password'), $user->password)) {
            return back()->withErrors(['delete_password' => 'Password salah. Akun tidak dihapus.']);
        }

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('success', 'Akun berhasil dihapus.');
    }
}
