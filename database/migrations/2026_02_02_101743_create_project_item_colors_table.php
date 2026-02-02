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
        Schema::create('project_item_colors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_item_id')->constrained()->cascadeOnDelete();
            $table->string('color')->nullable();
            $table->string('texture')->nullable();
            $table->string('texture_path')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_item_colors');
    }
};
