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
    Schema::table('supplier_lead_contacts', function (Blueprint $table) {
        $table->string('company_linkedin_url')->nullable()->after('linkedin_url');
    });
}

public function down(): void
{
    Schema::table('supplier_lead_contacts', function (Blueprint $table) {
        $table->dropColumn('company_linkedin_url');
    });
}
};
