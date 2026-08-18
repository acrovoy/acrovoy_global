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
        Schema::create('units', function (Blueprint $table) {
            $table->id();

            $table->string('code', 50)->unique();
            $table->string('symbol', 50);

            $table->string('unit_group', 50)->index();

            $table->decimal('conversion_factor', 20, 12)->default(1);
            $table->decimal('conversion_offset', 20, 12)->default(0);

            $table->boolean('is_base')->default(false);
            $table->boolean('is_active')->default(true);

            $table->unsignedInteger('sort_order')->default(0);

            $table->timestamps();

            $table->index(['unit_group', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('units');
    }
};
