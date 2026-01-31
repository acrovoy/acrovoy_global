<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::table('rfq_offers', function (Blueprint $table) {
        $table->timestamp('supplier_viewed_at')->nullable()->after('buyer_viewed_at');
    });
}

public function down()
{
    Schema::table('rfq_offers', function (Blueprint $table) {
        $table->dropColumn('supplier_viewed_at');
    });
}
};
