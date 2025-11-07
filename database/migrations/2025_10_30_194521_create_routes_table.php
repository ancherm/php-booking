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
        Schema::create('routes', function (Blueprint $table) {
            $table->id();
            $table->integer('bus_id');
            $table->string('from_station');
            $table->string('to_station');
            $table->time('start');
            $table->integer('duration'); // в минутах
            $table->decimal('price', 10, 2);
            $table->boolean('approved')->default(false);
            $table->timestamps();
            
            $table->foreign('bus_id')->references('id')->on('buses');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('routes');
    }
};
