<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Auction extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'auction_date',
        'image',
        'image2',
        'image3',
        'conditions'
    ];

    protected $casts = [
        'auction_date' => 'datetime',
        'conditions' => 'array'
    ];

    protected $dates = [
        'auction_date',
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
