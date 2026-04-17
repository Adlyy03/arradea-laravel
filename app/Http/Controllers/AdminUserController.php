<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\AccessCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminUserController extends Controller
{
    /**
     * Show all users with filter & search
     */
    public function index(Request $request)
    {
        $query = User::query();

        // Filter by status
        if ($request->filled('status')) {
            match ($request->status) {
                'pending' => $query->where('seller_status', 'pending'),
                'approved' => $query->where('seller_status', 'approved'),
                'rejected' => $query->where('seller_status', 'rejected'),
                default => null,
            };
        }

        // Filter by type
        if ($request->filled('type')) {
            match ($request->type) {
                'buyer' => $query->where('is_seller', false),
                'seller' => $query->where('is_seller', true),
                'admin' => $query->where('role', 'admin'),
                default => null,
            };
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $users = $query->paginate(15);

        return view('admin.users-verification', compact('users'));
    }

    /**
     * Show all users on a live map.
     */
    public function mapUsers()
    {
        $users = User::query()
            ->where('role', 'seller')
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->with(['accessCode', 'store'])
            ->orderByDesc('id')
            ->get(['id', 'name', 'role', 'latitude', 'longitude', 'access_code_id', 'store_status', 'open_time', 'close_time', 'auto_schedule'])
            ->map(function (User $user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'store_name' => $user->store?->name ?: $user->name,
                    'role' => $user->role,
                    'store_status' => $user->store_status ?? 'closed',
                    'open_time' => $user->open_time,
                    'close_time' => $user->close_time,
                    'auto_schedule' => (bool) ($user->auto_schedule ?? true),
                    'is_verified' => (bool) ($user->accessCode?->is_active ?? false),
                    'latitude' => (float) $user->latitude,
                    'longitude' => (float) $user->longitude,
                ];
            })
            ->values();

        return view('admin.map-users', [
            'mapUsers' => $users,
        ]);
    }

    /**
     * Show user details with location
     */
    public function show(User $user)
    {
        return response()->json([
            'id' => $user->id,
            'name' => $user->name,
            'phone' => $user->phone,
            'wilayah' => $user->wilayah,
            'is_seller' => $user->is_seller,
            'seller_status' => $user->seller_status,
            'seller_applied_at' => $user->seller_applied_at?->format('d M Y H:i'),
            'seller_approved_at' => $user->seller_approved_at?->format('d M Y H:i'),
            'seller_rejected_at' => $user->seller_rejected_at?->format('d M Y H:i'),
            'seller_rejection_reason' => $user->seller_rejection_reason,
            'latitude' => $user->latitude ?? -6.1753,
            'longitude' => $user->longitude ?? 106.8249,
            'created_at' => $user->created_at->format('d M Y'),
            'access_code' => $user->accessCode?->code,
            'access_code_active' => $user->accessCode?->is_active,
        ]);
    }

    /**
     * Approve user as seller
     */
    public function approve(Request $request, User $user)
    {
        try {
            DB::beginTransaction();

            // Update user seller status
            $user->update([
                'seller_status' => 'approved',
                'seller_approved_at' => now(),
                'seller_rejection_reason' => null,
            ]);

            // Create or activate access code
            if ($user->accessCode) {
                $user->accessCode->update(['is_active' => true]);
                $code = $user->accessCode->code;
            } else {
                $accessCode = AccessCode::create([
                    'code' => strtoupper('ARRADEA-' . substr(md5(uniqid()), 0, 8)),
                    'is_active' => true,
                ]);
                $user->update(['access_code_id' => $accessCode->id]);
                $code = $accessCode->code;
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'User berhasil disetujui sebagai seller',
                'access_code' => $code,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyetujui user: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Reject user as seller
     */
    public function reject(Request $request, User $user)
    {
        $request->validate([
            'reason' => ['required', 'string', 'min:10', 'max:500'],
        ], [
            'reason.required' => 'Alasan penolakan harus diisi',
            'reason.min' => 'Alasan minimal 10 karakter',
        ]);

        try {
            DB::beginTransaction();

            $user->update([
                'seller_status' => 'rejected',
                'seller_rejected_at' => now(),
                'seller_rejection_reason' => $request->reason,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'User berhasil ditolak',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal menolak user: ' . $e->getMessage(),
            ], 500);
        }
    }
}
