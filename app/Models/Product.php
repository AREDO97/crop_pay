<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    // allowed
    protected $fillable = [
        'name',
        'description',
        'is_active',
        'unit'
    ];
}
