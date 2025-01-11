<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Auction extends Model
{
    use HasFactory;

    protected $fillable = [
        'condition1',
        'condition2',
        'condition3',
        'condition4',
        'condition5',
        'condition6',
        'condition7',
    ];


}
