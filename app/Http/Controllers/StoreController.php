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
                'message' => 'You do not have a store yet.',
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
            'message' => 'Store created successfully.',
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
                'message' => 'You do not have a store to update.',
            ], 404);
        }

        $store->update($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Store updated successfully.',
            'data'    => $store,
        ]);
    }
}
