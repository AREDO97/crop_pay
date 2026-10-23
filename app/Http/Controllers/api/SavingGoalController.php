<?php

namespace App\Http\Controllers\api;
use App\Services\YoolaSmsService;

use App\Http\Controllers\Controller;
use App\Models\SavingGoal;
use App\Models\User;
use App\Models\UserProfile;
use App\Notifications\savingGoalNotification;
use Illuminate\Http\Request;

class SavingGoalController extends Controller
{
    // create saving goal 
    public function create(Request $request,YoolaSmsService $sms)
    {
        // logged in user
        $user = $request->user();
        // validation
        $request->validate([
            'target_amount'=>'required|sometimes',
            'category_id'  => 'required'
        ]);
        // create goal
        $goal = SavingGoal::create([
            'user_id'       => $user->id,
            'category_id'   => $request->category_id,
            'target_amount' =>$request->target_amount,
        ]);
        // insert contact into profile
        $profile = $user->profile;
        // send sms to user
            $sms->send(
                $profile->phone,
                'Your saving goal has been created, proceed to make your first deposit'
            );
        // notify admins
        $admins = User::whereIn('role',['admin','super_admin'])->get();
        foreach($admins as $admin)
            {
                $admin->notify(
                    new savingGoalNotification($user->name)
                );
            }
        // response
        return response()->json([
            'message'     =>'Saving goal created successifully',
            'saving_goal' =>$goal,
            'profile'     => $profile
        ]);
    }
    // return user saving goals
    public function UserSavingGoals(Request $request)
    {
        $user = $request->user();
        $savingGoals = $user->savingGoals;
        // response
        return response()->json($savingGoals);
    }
    // update saving goal
    public function update(Request $request,SavingGoal $goal)
    {
        // current user
        $user = $request->user();
        // check ownership
        if ($user->id !== $goal->user_id)
            {
                abort(403,'Unauthorized action');
            }
            // validation
            $request->validate([
                'category_id' =>'required'
            ]);
            // update
            $goal->update([
            'category_id'   => $request->category_id ?? $goal->category_id,
            ]);
            // response
            return response()->json([
                'message'     =>'Saving goal updated successifully',
                'saving_goal' => $goal
            ]);
    }
}
