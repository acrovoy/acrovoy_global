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
    Schema::table('supplier_leads', function (Blueprint $table) {
        $table->string('linkedin_url')->nullable()->after('website');
    });
}

public function down(): void
{
    Schema::table('supplier_leads', function (Blueprint $table) {
        $table->dropColumn('linkedin_url');
    });
}
};
