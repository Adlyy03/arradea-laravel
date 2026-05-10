<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRequest;
use App\Models\Store;
use Illuminate\Http\Request;

class StoreController extends Controller
{
    /**
     * GET /api/store
     * Seller: view own store.
     */
    public function show(Request $request)
    {
        $store = $request->user()->store;

        if (! $store) {
            return response()->json([
                'success' => false,
                'message' => 'Anda belum memiliki toko.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data'    => $store,
        ]);
    }

    /**
     * POST /api/store
     * Seller: create a store (one per seller).
     */
    public function store(StoreRequest $request)
    {
        $user = $request->user();

        if ($user->store) {
            return response()->json([
                'success' => false,
                'message' => 'You already have a store.',
            ], 422);
        }

        $store = $user->store()->create($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Toko berhasil dibuat.',
            'data'    => $store,
        ], 201);
    }

    /**
     * PUT /api/store
     * Seller: update own store.
     */
    public function update(StoreRequest $request)
    {
        $store = $request->user()->store;

        if (! $store) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki toko untuk diperbarui.',
            ], 404);
        }

        $store->update($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Toko berhasil diperbarui.',
            'data'    => $store,
        ]);
    }
}
