<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterRequest;
use App\Models\AccessCode;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Register a new user and return a Sanctum token.
     */
    public function register(RegisterRequest $request)
    {
        $accessCode = AccessCode::where('code', trim((string) $request->access_code))
            ->where('is_active', true)
            ->first();

        if (! $accessCode) {
            return response()->json([
                'success' => false,
                'message' => 'Akses ditolak. Kode tidak valid',
            ], 422);
        }

        $user = User::create([
            'name'           => $request->name,
            'phone'          => $request->phone,
            'wilayah'        => 'Arradea',
            'access_code_id' => $accessCode->id,
            'password'       => Hash::make($request->password),
            'is_seller'      => false,
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Registration successful.',
            'data'    => [
                'user'  => $user,
                'token' => $token,
            ],
        ], 201);
    }

    /**
     * Login and return a Sanctum token.
     *
     * @throws ValidationException
     */
    public function login(Request $request)
    {
        $request->validate([
            'phone'    => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $user = User::where('phone', $request->phone)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'phone' => ['Kredensial yang Anda berikan tidak cocok.'],
            ]);
        }

        if (strcasecmp((string) $user->wilayah, 'Arradea') !== 0 || ! optional($user->accessCode)->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'Akses ditolak. Akun Anda tidak memenuhi syarat wilayah Arradea.',
            ], 403);
        }

        // Revoke previous tokens to keep only the latest session alive
        $user->tokens()->delete();

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Login successful.',
            'data'    => [
                'user'  => $user,
                'token' => $token,
            ],
        ]);
    }

    /**
     * Revoke the current token (logout).
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logged out successfully.',
        ]);
    }

    /**
     * Return the authenticated user profile.
     */
    public function profile(Request $request)
    {
        $user = $request->user()->load('store');

        return response()->json([
            'success' => true,
            'data'    => $user,
        ]);
    }

    /**
     * Toggle seller mode for authenticated user.
     */
    public function toggleSellerMode(Request $request)
    {
        $validated = $request->validate([
            'enable' => ['nullable', 'boolean'],
            'store_name' => ['nullable', 'string', 'max:255'],
            'store_description' => ['nullable', 'string', 'max:2000'],
            'store_address' => ['nullable', 'string', 'max:500'],
        ]);

        $user = $request->user();

        if ($user->role === 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Admin account cannot toggle seller mode.',
            ], 422);
        }

        $enable = array_key_exists('enable', $validated)
            ? (bool) $validated['enable']
            : ! (bool) $user->is_seller;

        if ($enable) {
            $user->update([
                'is_seller' => true,
                'seller_status' => 'approved',
                'seller_applied_at' => $user->seller_applied_at ?: now(),
                'seller_approved_at' => now(),
                'seller_rejected_at' => null,
                'seller_rejection_reason' => null,
            ]);

            $storeData = [
                'name' => $validated['store_name'] ?? ($user->store->name ?? ('Toko ' . $user->name)),
                'description' => $validated['store_description'] ?? ($user->store->description ?? 'Selamat datang di toko kami.'),
                'address' => $validated['store_address'] ?? ($user->store->address ?? null),
            ];

            if ($user->store) {
                $user->store->update($storeData);
            } else {
                $user->store()->create($storeData);
            }

            return response()->json([
                'success' => true,
                'message' => 'Seller mode activated successfully.',
                'data' => $user->fresh()->load('store'),
            ]);
        }

        $user->update([
            'is_seller' => false,
            'seller_status' => 'none',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Seller mode deactivated successfully.',
            'data' => $user->fresh()->load('store'),
        ]);
    }
}
