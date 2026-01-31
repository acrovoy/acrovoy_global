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
    Schema::table('products', function (Blueprint $table) {
        $table->unsignedBigInteger('country_id')->nullable()->after('customization');
        $table->foreign('country_id')
              ->references('id')
              ->on('countries')
              ->onDelete('set null');
    });
}

public function down(): void
{
    Schema::table('products', function (Blueprint $table) {
        $table->dropForeign(['country_id']);
        $table->dropColumn('country_id');
    });
}
};
