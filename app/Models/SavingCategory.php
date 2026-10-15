<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens; 
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class SavingCategory extends Model
{
        use HasApiTokens,HasFactory, Notifiable;

    // allowed
    protected $fillable = [
        'name',
        'description',
        'is_active'
    ];
   // saving goal
   public function savingGoal()
   {
    return $this->hasMany(SavingGoal::class);
   } 
}
