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
        Schema::table('rfq_offer_versions', function (Blueprint $table) {

            $table->string('owner_type')
                ->nullable()
                ->after('created_by');

            $table->unsignedBigInteger('owner_id')
                ->nullable()
                ->after('owner_type');

            $table->index([
                'owner_type',
                'owner_id'
            ], 'rfq_offer_versions_owner_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rfq_offer_versions', function (Blueprint $table) {

            $table->dropIndex('rfq_offer_versions_owner_index');

            $table->dropColumn([
                'owner_type',
                'owner_id',
            ]);
        });
    }
};
