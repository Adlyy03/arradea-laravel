<?php

namespace App\Http\Controllers;

use App\Models\FcmToken;
use App\Models\User;
use App\Services\PushNotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class PushNotificationController extends Controller
{
    protected $pushNotificationService;

    public function __construct(PushNotificationService $pushNotificationService)
    {
        $this->pushNotificationService = $pushNotificationService;
    }

    /**
     * Store FCM token
     */
    public function storeToken(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required|string',
            'device_type' => 'nullable|string',
            'device_name' => 'nullable|string',
            'browser' => 'nullable|string',
            'platform' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $user = Auth::user();

            // Check if token already exists
            $fcmToken = FcmToken::where('token', $request->token)->first();

            if ($fcmToken) {
                // Update existing token
                $fcmToken->update([
                    'user_id' => $user->id,
                    'device_type' => $request->device_type ?? 'web',
                    'device_name' => $request->device_name,
                    'browser' => $request->browser,
                    'platform' => $request->platform,
                    'is_active' => true,
                    'last_used_at' => now(),
                ]);
            } else {
                // Create new token
                $fcmToken = FcmToken::create([
                    'user_id' => $user->id,
                    'token' => $request->token,
                    'device_type' => $request->device_type ?? 'web',
                    'device_name' => $request->device_name,
                    'browser' => $request->browser,
                    'platform' => $request->platform,
                    'is_active' => true,
                    'last_used_at' => now(),
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'FCM token stored successfully',
                'data' => $fcmToken,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to store FCM token: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Delete FCM token
     */
    public function deleteToken(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            FcmToken::where('token', $request->token)->delete();

            return response()->json([
                'success' => true,
                'message' => 'FCM token deleted successfully',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete FCM token: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Send test notification
     */
    public function sendTest(Request $request)
    {
        $user = Auth::user();

        $result = $this->pushNotificationService->sendToUser(
            $user,
            'Test Notification',
            'This is a test notification from ' . config('app.name'),
            ['type' => 'test'],
            asset('images/logo.png'),
            url('/')
        );

        return response()->json($result);
    }

    /**
     * Send notification to specific user
     */
    public function sendToUser(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'title' => 'required|string|max:255',
            'body' => 'required|string',
            'data' => 'nullable|array',
            'icon' => 'nullable|url',
            'click_action' => 'nullable|url',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $user = User::find($request->user_id);

        $result = $this->pushNotificationService->sendToUser(
            $user,
            $request->title,
            $request->body,
            $request->data ?? [],
            $request->icon,
            $request->click_action
        );

        return response()->json($result);
    }

    /**
     * Send notification to all users
     */
    public function sendToAll(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'body' => 'required|string',
            'data' => 'nullable|array',
            'icon' => 'nullable|url',
            'click_action' => 'nullable|url',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $result = $this->pushNotificationService->sendToAll(
            $request->title,
            $request->body,
            $request->data ?? [],
            $request->icon,
            $request->click_action
        );

        return response()->json($result);
    }

    /**
     * Get user's FCM tokens
     */
    public function getTokens()
    {
        $user = Auth::user();
        $tokens = $user->fcmTokens()->active()->get();

        return response()->json([
            'success' => true,
            'data' => $tokens,
        ]);
    }

    /**
     * Show notification settings page
     */
    public function settings()
    {
        return view('notifications.settings');
    }

    /**
     * Show notification test page
     */
    public function testPage()
    {
        return view('notifications.test');
    }
}

