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
        Schema::create('supplier_profiles', function (Blueprint $table) {

    $table->id();

    $table->foreignId('supplier_id')
        ->constrained()
        ->cascadeOnDelete();

    $table->text('about_us_description')->nullable();
    $table->text('manufacturing_description')->nullable();

    $table->integer('factory_area')->nullable();
    $table->integer('production_lines')->nullable();
    $table->integer('monthly_capacity')->nullable();
    $table->integer('lead_time_days')->nullable();

    $table->decimal('annual_export_revenue', 15, 2)->nullable();
    $table->decimal('registration_capital', 15, 2)->nullable();

    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('supplier_profiles');
    }
};
