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
        Schema::create('post_comments', function (Blueprint $table) {
            $table->id();

            $table->foreignId('post_id')
                ->constrained('posts')
                ->cascadeOnDelete();

            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            // Parent comment for future replies
            $table->foreignId('parent_id')
                ->nullable()
                ->constrained('post_comments')
                ->nullOnDelete();

            $table->text('content');

            $table->string('status', 20)->default('published');

            $table->timestamps();
            $table->softDeletes();

            $table->index(['post_id', 'status']);
            $table->index(['parent_id', 'status']);
            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('post_comments');
    }
};
