<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserProfile extends Model
{
    // allowed
    protected $fillable = [
        'user_id',
        'district',
        'village',
        'bio',
        'phone'
    ];
    // user
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
