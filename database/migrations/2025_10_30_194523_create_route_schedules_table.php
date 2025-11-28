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
        Schema::create('route_schedules', function (Blueprint $table) {
            $table->foreignId('route_id')->primary()->constrained('routes')->onDelete('cascade');
            $table->date('from_date');
            $table->date('to_date');
            $table->string('period'); // например: "daily", "even", "odd", "mon,wed,fri"
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('route_schedules');
    }
};
