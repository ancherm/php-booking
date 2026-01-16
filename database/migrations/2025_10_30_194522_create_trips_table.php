<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('trips', function (Blueprint $table) {
            $table->id();
            $table->foreignId('route_id')->constrained('routes')->onDelete('cascade');
            $table->date('date');
            $table->integer('free_places')->default(0);
            $table->timestamps();
            
            $table->unique(['route_id', 'date'], 'uniq_trip');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trips');
    }
};
