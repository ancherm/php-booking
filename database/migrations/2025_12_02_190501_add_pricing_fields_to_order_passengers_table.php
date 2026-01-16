<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('order_passengers', function (Blueprint $table) {
            $table->boolean('with_pet')->default(false)->after('passenger_id');
            $table->decimal('price', 10, 2)->default(0)->after('with_pet');
        });
    }

    public function down(): void
    {
        Schema::table('order_passengers', function (Blueprint $table) {
            $table->dropColumn(['with_pet', 'price']);
        });
    }
};
