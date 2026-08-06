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
        Schema::create('collections', function (Blueprint $table) {

            $table->id();

            // Owner (Admin, Supplier, Buyer)
            $table->morphs('owner');

            $table->string('slug')->unique();

            $table->string('cover_image')->nullable();

            $table->enum('type', [
                'platform',
                'supplier',
                'buyer',
            ])->default('platform');

            $table->enum('visibility', [
                'public',
                'private',
                'draft',
            ])->default('public');

            $table->boolean('is_featured')->default(false);

            $table->unsignedInteger('sort_order')->default(0);

            $table->timestamp('published_at')->nullable();

            $table->timestamps();

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('collections');
    }
};
