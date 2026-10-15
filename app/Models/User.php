<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens; 
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Contracts\Auth\MustVerifyEmail;

#[Fillable(['name', 'email', 'password','role','is_active'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens,HasFactory, Notifiable,HasUuids;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
    // saving goals
    public function savingGoals()
    {
        return $this->hasMany(SavingGoal::class);
    }
    // user profile
    public function profile()
    {
        return $this->hasOne(UserProfile::class);
    }

    // product sales
    public function productSales()
    {
        return $this->hasMany(ProductSale::class);
    }
    // user
    public function deposits()
    {
        return $this->hasMany(Deposit::class);
    }
    // password reset notification
    public function sendPasswordResetNotification($token): void
{
    $url = 'http://localhost:5173/reset-password?token='
        . urlencode($token)
        . '&email=' . urlencode($this->email);

    $this->notify(
        new \App\Notifications\CustomResetPassword($token, $url)
    );
}
}
