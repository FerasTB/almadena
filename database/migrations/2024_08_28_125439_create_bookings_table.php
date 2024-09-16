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
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->enum('status', ['approved', 'pending', 'canceled']);
            $table->foreignId('trip_id')->constrained(); // Relation to Trip table
            $table->foreignId('user_id')->constrained(); // Relation to Trip table
            $table->integer('seat_number'); // Store seat number
            $table->string('payment_code')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
