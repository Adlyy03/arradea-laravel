<?php

namespace App\Http\Controllers;

use App\Services\SellerModeService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;

class ModeController extends BaseController
{
    protected SellerModeService $modeService;

    public function __construct(SellerModeService $modeService)
    {
        $this->middleware('auth');
        $this->modeService = $modeService;
    }

    /**
     * Switch user mode between buyer and seller.
     */
    public function switch(Request $request)
    {
        $request->validate([
            'mode' => ['required', 'in:buyer,seller'],
        ]);

        $user = $request->user();
        $targetMode = $request->input('mode');

        $result = $this->modeService->switchMode($user, $targetMode);

        if ($request->expectsJson()) {
            return response()->json($result, $result['success'] ? 200 : 400);
        }

        if ($result['success']) {
            return back()->with('success', $result['message']);
        }

        return back()->withErrors(['mode' => $result['message']]);
    }

    /**
     * Get current mode information.
     */
    public function info(Request $request)
    {
        $user = $request->user();
        $info = $this->modeService->getModeInfo($user);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'data' => $info,
            ]);
        }

        return view('mode.info', compact('info'));
    }
}
