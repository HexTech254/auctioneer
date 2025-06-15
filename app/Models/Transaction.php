<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'auction_id',
        'amount',
        'transaction_id',
        'status',
        'transaction_details'
    ];

    protected $attributes = [
        'status' => 'pending',
    ];

    protected $casts = [
        'transaction_details' => 'array'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function auction()
    {
        return $this->belongsTo(Auction::class);
    }
}

