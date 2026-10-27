<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens; 
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Deposit extends Model
{

    use HasApiTokens,HasFactory, Notifiable,HasUuids;

    // allowed
    protected $fillable = [
            'user_id',
            'saving_goal_id',
            'amount',
            'payment_method',
            'pesapal_tracking_id',
            'phone_number',
            'reference',
            'status'
    ];

    // user
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
