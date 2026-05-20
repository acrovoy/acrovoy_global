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
        Schema::table('rfq_offer_version_items', function (Blueprint $table) {

            $table->unsignedBigInteger('updated_by')->nullable()
                ->after('notes');

            $table->foreign('updated_by')
                ->references('id')
                ->on('users')
                ->nullOnDelete();

        });
    }

    public function down(): void
    {
        Schema::table('rfq_offer_version_items', function (Blueprint $table) {

            $table->dropForeign(['updated_by']);

            $table->dropColumn('updated_by');

        });
    }
};
