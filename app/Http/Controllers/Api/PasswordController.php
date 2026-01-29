<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Models\User;
use App\Mail\PasswordResetMail;
use App\Jobs\SendPasswordResetEmail;

class PasswordController extends Controller
{
    /**
     * Change password when user is logged in (requires current password)
     */
    public function changePassword(Request $request)
    {
        $user = $request->user();

        $request->validate([
            'current_password' => 'required|current_password',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $token = bin2hex(random_bytes(32));

        DB::table('password_resets')->insert([
            'user_id' => $user->id,
            'token' => $token,
            'type' => 'change',  // Distinguish between change and reset
            'new_password' => Hash::make($request->password),
            'expires_at' => now()->addHour(),
        ]);

        // Queue email
        SendPasswordResetEmail::dispatch($user, $token, 'change');

        return response()->json([
            'success' => true,
            'message' => 'Password change confirmation email sent.'
        ]);
    }

    /**
     * Forgot password - request password reset
     */
    public function forgotPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        $user = User::where('email', $request->email)->first();

        // Check if there's a recent request (prevent spam)
        $recentRequest = DB::table('password_resets')
            ->where('user_id', $user->id)
            ->where('type', 'reset')
            ->where('created_at', '>', now()->subMinutes(5))
            ->first();

        if ($recentRequest) {
            return response()->json([
                'success' => false,
                'message' => 'Please wait 5 minutes before requesting another reset.'
            ], 429);
        }

        $token = bin2hex(random_bytes(32));

        DB::table('password_resets')->insert([
            'user_id' => $user->id,
            'token' => $token,
            'type' => 'reset',  // Mark as reset flow
            'new_password' => null,  // No password yet
            'expires_at' => now()->addHour(),
        ]);

        // Queue email
        SendPasswordResetEmail::dispatch($user, $token, 'reset');

        return response()->json([
            'success' => true,
            'message' => 'Password reset email sent. Please check your inbox.'
        ]);
    }

    /**
     * Reset password with token (for forgot password flow)
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $tokenRecord = DB::table('password_resets')
            ->where('token', $request->token)
            ->where('type', 'reset')  // Only for reset flow
            ->where('expires_at', '>', now())
            ->first();

        if (!$tokenRecord) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid or expired token.'
            ], 400);
        }

        $user = User::find($tokenRecord->user_id);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found.'
            ], 404);
        }

        // Update password
        $user->password = Hash::make($request->password);
        $user->save();

        // Delete token
        DB::table('password_resets')->where('token', $request->token)->delete();

        // Invalidate all tokens (log out everywhere)
        $user->tokens()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Password reset successfully. Please log in with your new password.',
            'requires_login' => true
        ]);
    }

    /**
     * Confirm password change (for logged-in user change)
     */
    public function confirmPasswordChange($token)
    {
        $tokenRecord = DB::table('password_resets')
            ->where('token', $token)
            ->where('type', 'change')  // Only for change flow
            ->where('expires_at', '>', now())
            ->first();

        if (!$tokenRecord) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid or expired token.'
            ], 400);
        }

        $user = User::find($tokenRecord->user_id);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found.'
            ], 404);
        }

        // Update password from stored hash
        $user->password = $tokenRecord->new_password;
        $user->save();

        // Delete token
        DB::table('password_resets')->where('token', $token)->delete();

        // Invalidate all tokens except current (if any)
        $user->tokens()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Password changed successfully.',
            'requires_login' => true
        ]);
    }
}
