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
        Schema::table('rfq_participants', function (Blueprint $table) {

    $table->dropUnique('rfq_participants_rfq_id_supplier_id_unique');

    $table->unique([
        'rfq_id',
        'participant_type',
        'participant_id'
    ], 'rfq_participants_unique_participant');
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
