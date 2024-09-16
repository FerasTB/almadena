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
        Schema::create('trips', function (Blueprint $table) {
            $table->id();
            $table->time('departure_time');
            $table->decimal('passenger_cost', 8, 2);
            $table->text('note')->nullable();
            $table->date('trip_day');
            $table->time('trip_time');
            $table->foreignId('template_id')->constrained(); // Relation to Template table
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trips');
    }
};
