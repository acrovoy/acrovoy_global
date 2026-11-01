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
        Schema::create('posts', function (Blueprint $table) {
            $table->id();

            // Author
            $table->foreignId('user_id')
                ->constrained('users')
                ->restrictOnDelete();

            // Optional company authorship
            $table->string('company_type')->nullable();
            $table->unsignedBigInteger('company_id')->nullable();

            // Category
            $table->foreignId('post_category_id')
                ->constrained('post_categories')
                ->restrictOnDelete();

            // Moderation
            $table->string('status', 30)->default('draft');
            $table->text('reject_reason')->nullable();

            // Engagement counters
            $table->unsignedInteger('views_count')->default(0);
            $table->unsignedInteger('likes_count')->default(0);
            $table->unsignedInteger('comments_count')->default(0);

            // Publication
            $table->timestamp('published_at')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index('status');
            $table->index('published_at');
            $table->index(['company_type', 'company_id']);
            $table->index(['post_category_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};
