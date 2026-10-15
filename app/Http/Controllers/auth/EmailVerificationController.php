<?php

namespace App\Http\Controllers\auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\Request;

class EmailVerificationController extends Controller
{
    public function verify(Request $request, $id, $hash)
    {
        // The signed middleware validates the URL signature.
        $user = User::findOrFail($id);

        // Confirm that the link matches this user's email.
        if (! hash_equals(
            $hash,
            sha1($user->getEmailForVerification())
        )) {
            return response()->json([
                'message' => 'Invalid verification link.',
            ], 403);
        }

        // Avoid processing the same verification repeatedly.
        if ($user->hasVerifiedEmail()) {
            return response()->json([
                'message' => 'Email already verified.',
            ]);
        }

        // Save the verification timestamp.
        if ($user->markEmailAsVerified()) {
            event(new Verified($user));
        }

        return response()->json([
            'message' => 'Email verified successfully.',
        ]);
    }

    public function resend(Request $request)
    {
        $user = $request->user();

        if ($user->hasVerifiedEmail()) {
            return response()->json([
                'message' => 'Email already verified.',
            ], 400);
        }

        $user->sendEmailVerificationNotification();

        return response()->json([
            'message' => 'Verification email sent successfully.',
        ]);
    }
}
