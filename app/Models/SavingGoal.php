<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens; 
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SavingGoal extends Model
{
    //

      use HasApiTokens,HasFactory, Notifiable,HasUuids;
      protected $fillable = [
        'user_id',
        'category_id',
        'target_amount',
        'current_amount',
        'is_active',
        'description',
        'target_date',
      ];
      // user
      public function user()
      {
        return $this->belongsTo(User::class);
      }
      // saving cateory
      public function savingCategory()
      {
        return $this->belongsTo(SavingCategory::class);
      }
}
