<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    // obtain user profile
    public function index(Request $request)
    {
        $user = $request->user();
        // user profile
        $profile = $user->profile;
        // response
        return response()->json($profile);
    }
    // update profile
    public function update(Request $request)
    {
        $user = $request->user();
        $profile = $user->profile;
        // update profile
        $profile->update([
            'phone'    => $request->phone ?? $profile->phone,
            'district' => $request->district ?? $profile->district,
            'village'  => $request->village ?? $profile->village,
            'bio'      => $request->bio ?? $profile->bio
        ]);
        // response
        return response()->json([
            'message' => "Profile updated successifully",
            'profile' => $profile
        ]);
    }
}
