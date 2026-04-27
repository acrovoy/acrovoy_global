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
        Schema::create('rfq_requirements', function (Blueprint $table) {
            $table->id();

            $table->foreignId('rfq_id')
                ->constrained('rfqs')
                ->cascadeOnDelete();

            $table->string('key');              // material, size, voltage
            $table->text('value')->nullable();  // значение

            $table->string('type')->nullable(); // text / number / boolean
            $table->boolean('is_required')->default(false);

            $table->timestamps();

            $table->index(['rfq_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rfq_requirements');
    }
};
