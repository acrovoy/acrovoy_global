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
    Schema::rename(
        'product_shipping_dimensions',
        'shipping_dimensions'
    );
}

public function down(): void
{
    Schema::rename(
        'shipping_dimensions',
        'product_shipping_dimensions'
    );
}
};
