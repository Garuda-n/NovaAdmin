<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            // 🔥 ADD THIS (IMPORTANT FIX)
            $table->foreignId('login_log_id')
                ->nullable()
                ->constrained('login_logs')
                ->nullOnDelete();

            $table->string('module', 100);
            $table->string('action', 30);
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->text('description')->nullable();

            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();

            $table->json('meta')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};