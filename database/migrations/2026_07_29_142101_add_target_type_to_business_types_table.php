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
        Schema::table('business_types', function (Blueprint $table) {
            $table->string('target_type')->after('slug');
        });
    }

    public function down(): void
    {
        Schema::table('business_types', function (Blueprint $table) {
            $table->dropColumn('target_type');
        });
    }
};
