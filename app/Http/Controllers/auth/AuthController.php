<?php

namespace App\Http\Controllers\auth;
use App\Models\User;
use App\Models\AuditLog;
use App\Http\Controllers\Controller;
use App\Mail\WelcomeEmail;
use App\Models\UserProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Str; 
use Illuminate\Support\Facades\Password;
class AuthController extends Controller
{
     // create user
public function register(Request $request)
{
    //  Validation
    $request->validate([
        'name'     => ['required', 'string', 'max:255'],
        'email'    => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
        'password' => ['required', 'min:6'], 
        'phone'    => ['required', 'string', 'max:15'],
    ]);

    // Insert User into db
    $user = User::create([
        'name'     => $request->name,
        'email'    => strtolower(trim($request->email)),
        'password' => Hash::make($request->password)
    ]);

    $profile = UserProfile::create([
        'user_id' => $user->id,
        'phone'   => $request->phone
    ]);
    // email verification
    // $user->sendEmailVerificationNotification();
    // Issue Token 
    $token = $user->createToken('auth-token');
    // log action $userId,$action,$body
  $logs = AuditLog::logAction(
        $request,
        $user->id,
        'Account Creation',
         [
        'message' => 'User created an account successfully',
        'name'    => $user->name,
        'email'   => $user->email,
    ]
    );
    // response
    return response()->json([
        'message' => 'Account created successfully',
        'user'    => $user,
        'token'   => $token,
        'logs'    =>$logs,
        'profile' => $profile,
    ], 201);
}


    // login
    public function login(Request $request)
{
    //  Validation
    $request->validate([
        'email'    => 'required|email',
        'password' => 'required',
        'remember' =>'sometimes|boolean'
    ]);

    //  Fetch user from DB
$user = User::where('email', $request->email)->first();

//  Checking if user exists
if (!$user) {
    // log action
      $logs = AuditLog::logAction(
        $request,
        null,
        'Login Failure',
         [
        'message' => 'User failed to log into account',
    ]
    );
    return response()->json([
        'message' => 'Invalid credentials',
        'logs'    =>$logs
    ], 401);
    
}


    //  Checking user existence AND verifying password
    if (!$user || !Hash::check($request->password, $user->password)) {
        // log action
     AuditLog::logAction(
            $request,
            $user->id,
            'Login Failure',
            [
                'message' => 'Login failed - incorrect password',
                'email'   => $user->email,
            ]
        );
        return response()->json([
            'message' => 'Invalid email or password'
        ], 401);
    }
    // generate remember me token
    $expiresAt=$request->boolean('remember')
    ?  now()->addDays(30)
    : now()->addMinutes(30);

    // Issue token to user
    $token = $user->createToken('auth-token'
    ,
    [],
    $expiresAt );

   // log action
     $logs = AuditLog::logAction(
        $request,
        $user->id,
        'Account login',
         [
        'message' => 'User logged into account successfully',
        'name'    => $user->name,
        'email'   => $user->email,
    ]
    );
    //response
    return response()->json([
        'token' => $token,
        'user'  => $user,
        'logs'  =>$logs
    ], 200);
}
    // logout logic
    public function logout(Request $request)
{
    $user=$request->user();
    // log action
      $logs = AuditLog::logAction(
        $request,
        $user->id,
        'Account Logout',
         [
        'message' => 'User logged out of account ',
        'name'    => $user->name,
        'email'   => $user->email,
    ]
    );
    // delete current user token
    $request->user()->currentAccessToken()->delete();

    return response()->json([
        'message' => 'Logged out successfully',
        'logs'    =>$logs
    ], 200);
}

// get the current logged in user
public function getMe(Request $request)
{
    $user=$request->user();
    // response
    return response()->json($user);
}

// google oauth

public function google()
{
    // creating a redirect to google
    return Socialite::driver('google')->stateless()->redirect();
}
// google callback
// google callback
public function googleCallback()
{
    try {
        // Retrieve the user from Google
        $googleUser = Socialite::driver('google')->stateless()->user();

        // Find or create the user in the database
        $user = User::updateOrCreate([
            'email' => $googleUser->getEmail(),
        ], [
            'name'      => $googleUser->getName(),
            'google_id' => $googleUser->getId(),
            'password'  => bcrypt(Str::random(16)),
        ]);

        // Issue token to user
        $token = $user->createToken('auth_token');

        // Return JSON response matching your manual login/signup structure
        return response()->json([
            'status' => 'success',
            'token'  => $token,
            'user'   => $user
        ], 200);

    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'Google authentication failed.',
            'error' => $e->getMessage()
        ], 422);
    }
}

// forgot password endpoint
public function forgotPassword(Request $request)
{
    $request->validate([
        'email' => ['required', 'email'],
    ]);

    $status = Password::sendResetLink(
        $request->only('email')
    );

    if ($status === Password::RESET_LINK_SENT) {
        return response()->json([
            'message' => 'Password reset link sent successfully.'
        ]);
    }

    return response()->json([
        'message' => 'Unable to send password reset link.'
    ], 422);
}
}




