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
        Schema::create('attribute_groups', function (Blueprint $table) {
            $table->id();

            $table->string('code')->index(); // system identifier (seo, dimensions etc.)
            $table->boolean('is_active')->default(true);

            // ownership (как у Attribute)
            $table->string('owner_type')->nullable()->index();
            $table->unsignedBigInteger('owner_id')->nullable()->index();

            $table->unsignedBigInteger('created_by')->nullable();

            $table->timestamps();

            $table->index(['owner_type', 'owner_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attribute_groups');
    }
};
