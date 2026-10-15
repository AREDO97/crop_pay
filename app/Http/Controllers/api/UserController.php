<?php

namespace App\Http\Controllers\api;
use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\AuditLog;

class UserController extends Controller
{
  // all users
  public function index()
  {
    $users=User::latest()->paginate(10);
    return response()->json($users);
  }
  //access one user
  public function oneUser(Request $request ,User $user)
  {
    // check user role
    $user = $request->user();
    if($user->role !== 'admin' && $user->role !== 'super_admin')
        {
            abort(403,'Unauthorised action');
        }
    return response()->json($user);
  }
// update user info 
public function update(Request $request,User $user)
{
    $allowedUser=$request->user();
    if($allowedUser->id !== $user->id && $user->role !== "super_admin"){
        abort(403,'Unauthorised action');
    }
    //validation
    $request->validate([
        'name' =>'string|max:45',
        'email'=>'email',
        'phone'=>'string|max:15'
    ]);
    $update=$user->update([
        'name' =>$request->name ?? $user->name,
        'email'=>$request->email ?? $user->email,
        'role' =>$request->role ?? $user->role,
    ]);

    return [
        'message'=>'user profile updated',
        'profile'=>$user
    ];
}
// soft delete
public function softDelete(Request $request,User $user)
{
    // check user role
    $user = $request->user();
    if($user->role !== 'admin' && $user->role !== 'super_admin')
        {
            abort(403,'Unauthorised action');
        }
    $user->update([
        'is_active'=>false
    ]);

    // log action
     $logs = AuditLog::logAction(
        $request,
        $user->id,
        'User Suspension',
         [
        'message' => 'User Suspension successful',
    ]
    );
//response
    return [
        'message'=>'User suspended successifuuly',
        'user'   =>$user
    ];
}

// unsuspend user
public function unsuspend(Request $request,User $user)
{

    $admin=$request->user();
    if($admin->role !== 'admin' && $admin->role !== 'super_admin'){
        abort(403,'Unauthorised action');
    }
    $user->update([
        'status'=>'active'
    ]);

   // log action
     $logs = AuditLog::logAction(
        $request,
        $user->id,
        'User Unsuspension',
         [
        'message' => 'User Unsuspension successful',
    ]
    );

    return response()->json([
        'message'=>'User unsuspend successiful',
        'user'   =>$user
    ]);

}



// change role to admin
public function makeAdmin(Request $request,User $user)
{
    $admin=$request->user();
    if($admin->role !== 'admin' && $admin->role !== 'super_admin'){
        abort(403,'Unauthorised action');
    }
    $user->update([
        'role'=>'admin'
    ]); 
    // get new user role
    $role=$user->role;
     
           // log action
     $logs = AuditLog::logAction(
        $request,
        $user->id,
        'Admin Creation',
         [
        'message' => 'User role promoted to Admin',
    ]
    );

    return [
        'message'=>'user role updated to admin',
        'role'   =>$role
    ];
}
// demote role to user

public function demoteAdmin(Request $request,User $user)
{
     $admin=$request->user();
    if($admin->role !== 'admin' && $admin->role !== 'super_admin'){
        abort(403,'Unauthorised');
    }
    $user->update([
        'role'=>'user'
    ]); 


    $role=$user->role;

    // log action
     $logs = AuditLog::logAction(
        $request,
        $user->id,
        'Admin Demotion',
         [
        'message' => 'Amin Demotion successful',
    ]
    );
// response

    return [
        'message'=>'Admin demoted to normal user ',
        'role'   =>$role
    ];
}

// user activity
    public function userActivity(Request $request)
    {
        $user=$request->user();
        $logs=$user->logs;
        // response
        return response()->json([
            'user'=>$user,
            'logs'=>$logs
        ]);
    }
}
