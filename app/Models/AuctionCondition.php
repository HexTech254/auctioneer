<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuctionCondition extends Model
{
    use HasFactory;

    protected $fillable = ['id', 'condition1', 'condition2', 'condition3', 'condition4', 'condition5', 'condition6', 'condition7'];

    public function auction()
    {
        return $this->belongsTo(Auction::class);
    }
}
