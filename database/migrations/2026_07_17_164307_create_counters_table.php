<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('counters', function (Blueprint $table) {
            $table->id();

            $table->string('counter_name',100);
            $table->string('counter_code',20);

            $table->boolean('status')->default(true);

            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();

            $table->unique('counter_name');
            $table->unique('counter_code');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('counters');
    }
};