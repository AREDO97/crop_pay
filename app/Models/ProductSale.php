<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens; 
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class ProductSale extends Model
{
    use HasApiTokens,HasFactory,HasUuids;
    // allowed 
    protected $fillable = [
        'user_id',
        'product_id',
        'quantity',
        'expected_unit_price',
        'expected_total_amount',
        'location',
        'status',
        'unit',
        'ready_at_date',
        'brief_notes'
    ];
    // user
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
