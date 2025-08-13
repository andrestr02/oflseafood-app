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
        Schema::table('purchases', function (Blueprint $table) {
            $table->integer('total_price')->default(0)->change();
            $table->integer('amount_paid')->default(0)->change();
            $table->integer('amount_due')->default(0)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purchases', function (Blueprint $table) {
            $table->integer('price_per_kg')->default(0)->change();
            $table->integer('total_price')->default(0)->change();
        });
    }
};
