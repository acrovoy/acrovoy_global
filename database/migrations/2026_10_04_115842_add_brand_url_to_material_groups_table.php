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
            $table->string('brand_url')->nullable()->after('brand');
        });
    }

    public function down(): void
    {
        Schema::table('material_groups', function (Blueprint $table) {
            $table->dropColumn('brand_url');
        });
    }
};
