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
        Schema::table('buyers', function (Blueprint $table) {

            $table->string('buyerable_type')
                ->nullable()
                ->after('user_id');

            $table->unsignedBigInteger('buyerable_id')
                ->nullable()
                ->after('buyerable_type');

            $table->index(
                ['buyerable_type', 'buyerable_id'],
                'buyers_buyerable_index'
            );

        });
    }

    public function down(): void
    {
        Schema::table('buyers', function (Blueprint $table) {

            $table->dropIndex('buyers_buyerable_index');

            $table->dropColumn([
                'buyerable_type',
                'buyerable_id',
            ]);

        });
    }
};
