<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('auctions', function (Blueprint $table) {
            if (!Schema::hasColumn('auctions', 'image2')) {
                $table->string('image2')->nullable();
            }
            if (!Schema::hasColumn('auctions', 'image3')) {
                $table->string('image3')->nullable();
            }
        });
    }

    public function down()
    {
        Schema::table('auctions', function (Blueprint $table) {
            $table->dropColumn(['image2', 'image3']);
        });
    }
}; 