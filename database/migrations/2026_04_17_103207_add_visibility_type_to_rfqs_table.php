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
        Schema::table('rfqs', function (Blueprint $table) {

            $table->string('visibility_type')
                ->default('private')
                ->after('status');

            $table->index('visibility_type');
        });
    }

    public function down(): void
    {
        Schema::table('rfqs', function (Blueprint $table) {
            $table->dropIndex(['visibility_type']);
            $table->dropColumn('visibility_type');
        });
    }
};
