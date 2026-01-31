<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('suppliers', function (Blueprint $table) {
            // Основные поля профиля компании
            if (!Schema::hasColumn('suppliers', 'email')) {
                $table->string('email')->nullable();
            }

            if (!Schema::hasColumn('suppliers', 'phone')) {
                $table->string('phone', 50)->nullable();
            }

            if (!Schema::hasColumn('suppliers', 'address')) {
                $table->text('address')->nullable();
            }

            if (!Schema::hasColumn('suppliers', 'logo')) {
                $table->string('logo')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('suppliers', function (Blueprint $table) {
            if (Schema::hasColumn('suppliers', 'email')) {
                $table->dropColumn('email');
            }

            if (Schema::hasColumn('suppliers', 'phone')) {
                $table->dropColumn('phone');
            }

            if (Schema::hasColumn('suppliers', 'address')) {
                $table->dropColumn('address');
            }

            if (Schema::hasColumn('suppliers', 'logo')) {
                $table->dropColumn('logo');
            }
        });
    }
};