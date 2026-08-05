<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('stock_items', function (Blueprint $table) {
            $table->unsignedTinyInteger('status')
                ->default(1)
                ->comment('1=>Available, 2=>Counter Transferred, 3=>Branch Transferred, 4=>Reserved, 5=>Sold, 6=>Damaged, 7=>Under Repair, 8=>Disposed')
                ->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stock_items', function (Blueprint $table) {
            $table->unsignedTinyInteger('status')
                ->default(1)
                ->comment(null)
                ->change();
        });
    }
};
