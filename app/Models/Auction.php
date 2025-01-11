<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Auction extends Model
{
    use HasFactory;

    protected $fillable = [
        'image',
        'title',
        'description',
        'auction_date',
        'conditions',
    ];

    protected $casts = [
        'conditions' => 'array',
    ];


}
