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
        Schema::create('media', function (Blueprint $table) {
    $table->id();
    $table->uuid('uuid')->unique();

    $table->string('model_type');
    $table->unsignedBigInteger('model_id');

    $table->string('collection')->nullable();

    $table->string('file_name');
    $table->string('file_path');
    $table->string('cdn_url')->nullable();

    $table->string('mime_type');
    $table->string('extension');

    $table->bigInteger('size_bytes');

    $table->integer('width')->nullable();
    $table->integer('height')->nullable();

    $table->string('checksum_hash')->nullable();

    $table->boolean('is_private')->default(false);
    $table->boolean('is_primary')->default(false);

    $table->string('processing_status')->default('pending');

    $table->integer('views_count')->default(0);
    $table->integer('unique_views_count')->default(0);

    $table->softDeletes();
    $table->timestamps();

    $table->index(['model_type', 'model_id']);
    $table->index('uuid');
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('media');
    }
};
