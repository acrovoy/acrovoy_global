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
        Schema::table('material_groups', function (Blueprint $table) {
            $table->boolean('is_custom')
                ->default(false)
                ->after('brand');

            $table->boolean('is_active')
                ->default(true)
                ->after('is_custom');

            $table->string('owner_type')
                ->nullable()
                ->after('is_active');

            $table->unsignedBigInteger('owner_id')
                ->nullable()
                ->after('owner_type');

            $table->index(
                ['owner_type', 'owner_id'],
                'material_groups_owner_index'
            );
        });
    }

    public function down(): void
    {
        Schema::table('material_groups', function (Blueprint $table) {
            $table->dropIndex('material_groups_owner_index');

            $table->dropColumn([
                'is_custom',
                'is_active',
                'owner_type',
                'owner_id',
            ]);
        });
    }
};
