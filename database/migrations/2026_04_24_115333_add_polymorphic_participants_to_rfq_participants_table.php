<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Models\Supplier;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('rfq_participants', function (Blueprint $table) {

            $table->string('participant_type')
                ->nullable()
                ->after('rfq_id');

            $table->unsignedBigInteger('participant_id')
                ->nullable()
                ->after('participant_type');

            $table->index(['participant_type', 'participant_id']);
        });

        /*
        |--------------------------------------------------------------------------
        | MIGRATE EXISTING SUPPLIER DATA
        |--------------------------------------------------------------------------
        */

        DB::table('rfq_participants')
            ->whereNotNull('supplier_id')
            ->update([
                'participant_type' => Supplier::class,
                'participant_id' => DB::raw('supplier_id'),
            ]);
    }

    public function down(): void
    {
        Schema::table('rfq_participants', function (Blueprint $table) {

            $table->dropIndex([
                'participant_type',
                'participant_id'
            ]);

            $table->dropColumn([
                'participant_type',
                'participant_id'
            ]);
        });
    }
};
