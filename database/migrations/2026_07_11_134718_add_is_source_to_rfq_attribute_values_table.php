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
        Schema::table('rfq_attribute_values', function (Blueprint $table) {
            $table->boolean('is_source')
                ->default(false)
                ->after('attribute_option_id');

            $table->index(['rfq_id', 'is_source']);
        });
    }

    public function down(): void
    {
        Schema::table('rfq_attribute_values', function (Blueprint $table) {
            $table->dropIndex(['rfq_id', 'is_source']);
            $table->dropColumn('is_source');
        });
    }
};
