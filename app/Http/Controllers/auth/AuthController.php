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

        // Issue token to user (matches your manual login structure)
        $tokenResult = $user->createToken('auth_token');

        $tokenData = [
            'accessToken' => [
                'name' => 'auth_token',
                'abilities' => ['*'],
                'tokenable_id' => $user->id,
                'tokenable_type' => get_class($user),
            ],
            'plainTextToken' => $tokenResult->plainTextToken
        ];

        // Return a lightweight HTML snippet that mimics successful client-side login storage
       $html = "
        <html>
            <head><title>Logging in with Google...</title></head>
            <body>
                <script>
                    const token = '{$tokenResult->plainTextToken}';
                    const userData = " . json_encode($user) . ";
                    
                    // Save all possible token variations your frontend might be looking for
                    localStorage.setItem('auth_token', token);
                    localStorage.setItem('token', token);
                    localStorage.setItem('ACCESS_TOKEN', token);
                    localStorage.setItem('user', JSON.stringify(userData));
                    
                    // Also set a cookie just in case they use cookies for Sanctum
                    document.cookie = 'auth_token=' + token + '; path=/; domain=.vercel.app; secure';

                    // Redirect straight to dashboard
                    window.location.href = 'https://hunter-ten-phi.vercel.app/dashboard';
                </script>
                <p>Google authentication successful. Redirecting...</p>
            </body>
        </html>
        ";

        return response($html, 200)->header('Content-Type', 'text/html');

        return response($html, 200)->header('Content-Type', 'text/html');

    } catch (\Exception $e) {
        // If anything fails, route back to login with an error flag
        return redirect()->away('https://hunter-ten-phi.vercel.app/login?error=google_failed');
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




