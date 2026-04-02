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
        Schema::table('categories', function (Blueprint $table) {

            $table->boolean('is_selectable')
                ->default(false)
                ->after('level')
                ->index();

            $table->boolean('is_leaf')
                ->default(false)
                ->after('is_selectable')
                ->index();

            $table->string('path')
                ->nullable()
                ->after('slug')
                ->index();

            $table->unsignedInteger('children_count')
                ->default(0)
                ->after('parent_id');

            $table->boolean('attributes_loaded')
                ->default(false)
                ->after('is_leaf');

        });
    }

    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {

            $table->dropColumn([
                'is_selectable',
                'is_leaf',
                'path',
                'children_count',
                'attributes_loaded'
            ]);

        });
    }
};
