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
         Schema::rename('product_    variations', 'product_variations');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
