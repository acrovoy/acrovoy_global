<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rfq_offers', function (Blueprint $table) {
            $table
                ->timestamp('buyer_viewed_at')
                ->nullable()
                ->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('rfq_offers', function (Blueprint $table) {
            $table->dropColumn('buyer_viewed_at');
        });
    }
};
