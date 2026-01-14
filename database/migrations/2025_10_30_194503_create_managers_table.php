<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('managers', function (Blueprint $table) {
            $table->foreignId('id')->primary()->constrained('users')->onDelete('cascade');
            $table->string('department', 50)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('managers');
    }
};


