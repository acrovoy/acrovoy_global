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
        Schema::table('suppliers', function (Blueprint $table) {

            $table->string('supplierable_type')
                ->after('user_id');

            $table->unsignedBigInteger('supplierable_id')
                ->after('supplierable_type');

            $table->boolean('is_published')
                ->default(false)
                ->after('status');

            $table->index([
                'supplierable_type',
                'supplierable_id',
            ], 'suppliers_supplierable_index');
        });
    }

    public function down(): void
    {
        Schema::table('suppliers', function (Blueprint $table) {

            $table->dropIndex('suppliers_supplierable_index');

            $table->dropColumn([
                'supplierable_type',
                'supplierable_id',
                'is_published',
            ]);
        });
    }
};
