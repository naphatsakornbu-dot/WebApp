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
        Schema::create('player_matches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->unique()->constrained('bookings')->onDelete('cascade');
            $table->integer('slots_needed')->default(2);
            $table->integer('slots_filled')->default(0);
            $table->enum('skill_level', ['beginner', 'intermediate', 'advanced', 'any'])->default('any');
            $table->text('details')->nullable();
            $table->enum('status', ['open', 'full', 'cancelled'])->default('open');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('player_matches');
    }
};
