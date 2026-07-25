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
        Schema::table('branches', function (Blueprint $table) {
            $table->foreignId('country_id')
                ->nullable()
                ->after('address')
                ->constrained('countries')
                ->onDelete('set null');

            $table->foreignId('state_id')
                ->nullable()
                ->after('country_id')
                ->constrained('states')
                ->onDelete('set null');

            $table->foreignId('city_id')
                ->nullable()
                ->after('state_id')
                ->constrained('cities')
                ->onDelete('set null');

            $table->string('pincode', 10)->nullable()->after('city_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('branches', function (Blueprint $table) {
            $table->dropForeign(['country_id']);
            $table->dropForeign(['state_id']);
            $table->dropForeign(['city_id']);
            $table->dropColumn(['country_id', 'state_id', 'city_id', 'pincode']);
        });
    }
};
