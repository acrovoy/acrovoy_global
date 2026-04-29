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
        Schema::table('rfq_offers', function (Blueprint $table) {

            /*
            |----------------------------------------------------------
            | POLYMORPHIC PARTICIPANT
            |----------------------------------------------------------
            */
            $table->string('participant_type')->nullable()->after('rfq_id');
            $table->unsignedBigInteger('participant_id')->nullable()->after('participant_type');

            /*
            |----------------------------------------------------------
            | INDEX
            |----------------------------------------------------------
            */
            $table->index(
                ['participant_type', 'participant_id'],
                'rfq_offers_participant_index'
            );
        });
    }

    public function down(): void
    {
        Schema::table('rfq_offers', function (Blueprint $table) {

            /*
            |----------------------------------------------------------
            | DROP INDEX
            |----------------------------------------------------------
            */
            $table->dropIndex('rfq_offers_participant_index');

            /*
            |----------------------------------------------------------
            | DROP COLUMNS
            |----------------------------------------------------------
            */
            $table->dropColumn([
                'participant_type',
                'participant_id',
            ]);
        });
    }
};
