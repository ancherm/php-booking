<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('places', function (Blueprint $table) {
            $table->id();
            $table->foreignId('trip_id')->constrained('trips')->onDelete('cascade');
            $table->integer('number_place');
            $table->foreignId('passenger_id')->nullable()->constrained('passengers')->onDelete('cascade');
            $table->timestamps();
            
            $table->unique(['trip_id', 'number_place'], 'uniq_place');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('places');
    }
};
