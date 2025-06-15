<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('auctions', function (Blueprint $table) {
            if (!Schema::hasColumn('auctions', 'auction_date')) {
                $table->dateTime('auction_date');
            }
            // If you want to add image fields for multiple images
            $table->string('image2')->nullable();
            $table->string('image3')->nullable();
        });
    }

    public function down()
    {
        Schema::table('auctions', function (Blueprint $table) {
            $table->dropColumn('auction_date');
            $table->dropColumn(['image2', 'image3']);
        });
    }
}; 