<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


return new class extends Migration
{
    public function up(): void
    {
        Schema::table('suppliers', function (Blueprint $table) {
            // slug (уникальный, nullable на время добавления)
            $table->string('slug')->nullable()->after('name');

            // verified и premium_status
            $table->boolean('verified')->default(false)->after('slug');
            $table->boolean('premium_status')->default(false)->after('verified');

            // status
            $table->string('status')->default('active')->after('premium_status');
        });

        // После добавления slug можно обновить пустые значения и сделать уникальный индекс
        \DB::table('suppliers')->whereNull('slug')->update(['slug' => \DB::raw('CONCAT("supplier-", id)')]);

        Schema::table('suppliers', function (Blueprint $table) {
            $table->unique('slug');
        });
    }

    public function down(): void
    {
        Schema::table('suppliers', function (Blueprint $table) {
            $table->dropUnique(['slug']);
            $table->dropColumn(['slug', 'verified', 'premium_status', 'status']);
        });
    }
};

