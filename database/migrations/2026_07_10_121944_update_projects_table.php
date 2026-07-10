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
        Schema::table('projects', function (Blueprint $table) {

            $table->string('buyer_type')->nullable()->after('buyer_id');
            $table->foreignId('created_by')->nullable()->after('buyer_type');

            $table->string('visibility_type')->default('private')->after('status');

            $table->timestamp('published_at')->nullable()->after('visibility_type');
            $table->timestamp('closed_at')->nullable()->after('published_at');

            $table->softDeletes();

            $table->dropColumn('category_id');
        });
    }

    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {

            $table->foreignId('category_id')->nullable()->after('description');

            $table->dropColumn([
                'buyer_type',
                'created_by',
                'visibility_type',
                'published_at',
                'closed_at',
                'deleted_at',
            ]);
        });
    }
};
