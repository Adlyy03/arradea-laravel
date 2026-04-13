<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    /**
     * GET /api/admin/sellers
     * Admin: list all sellers with their store.
     */
    public function sellers()
    {
        $sellers = User::where('is_seller', true)
            ->with('store')
            ->latest()
            ->paginate(15);

        return response()->json([
            'success' => true,
            'data'    => $sellers,
        ]);
    }

    /**
     * PUT /api/admin/sellers/{user}
     * Admin: edit a seller (name, email, seller flag).
     */
    public function updateSeller(Request $request, User $user)
    {
        if (! $user->is_seller) {
            return response()->json([
                'success' => false,
                'message' => 'User is not a seller.',
            ], 422);
        }

        $validated = $request->validate([
            'name'      => ['sometimes', 'string', 'max:255'],
            'email'     => ['sometimes', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'is_seller' => ['sometimes', 'boolean'],
        ]);

        if (array_key_exists('is_seller', $validated)) {
            if ((bool) $validated['is_seller']) {
                $validated['seller_status'] = 'approved';
                $validated['seller_approved_at'] = now();
                $validated['seller_rejected_at'] = null;
                $validated['seller_rejection_reason'] = null;
            } else {
                $validated['seller_status'] = 'none';
            }
        }

        $user->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Seller updated successfully.',
            'data'    => $user,
        ]);
    }

    /**
     * DELETE /api/admin/sellers/{user}
     * Admin: delete (deactivate) a seller account.
     * Cascades to store and products via DB constraints.
     */
    public function destroySeller(User $user)
    {
        if (! $user->is_seller) {
            return response()->json([
                'success' => false,
                'message' => 'User is not a seller.',
            ], 422);
        }

        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'Seller deleted successfully.',
        ]);
    }
}
