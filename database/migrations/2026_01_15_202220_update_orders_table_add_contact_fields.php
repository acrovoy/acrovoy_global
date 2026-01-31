<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Удаляем старый shipping_address
            if (Schema::hasColumn('orders', 'shipping_address')) {
                $table->dropColumn('shipping_address');
            }

            // Добавляем поля контактов и адреса
            if (!Schema::hasColumn('orders', 'first_name')) {
                $table->string('first_name')->after('delivery_method');
            }
            if (!Schema::hasColumn('orders', 'last_name')) {
                $table->string('last_name')->after('first_name');
            }
            if (!Schema::hasColumn('orders', 'country')) {
                $table->string('country', 2)->after('last_name');
            }
            if (!Schema::hasColumn('orders', 'city')) {
                $table->string('city')->after('country');
            }
            if (!Schema::hasColumn('orders', 'region')) {
                $table->string('region')->nullable()->after('city');
            }
            if (!Schema::hasColumn('orders', 'street')) {
                $table->string('street')->after('region');
            }
            if (!Schema::hasColumn('orders', 'postal_code')) {
                $table->string('postal_code')->after('street');
            }
            if (!Schema::hasColumn('orders', 'phone')) {
                $table->string('phone')->nullable()->after('postal_code');
            }
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Удаляем добавленные поля
            foreach (['first_name','last_name','country','city','region','street','postal_code','phone'] as $col) {
                if (Schema::hasColumn('orders', $col)) {
                    $table->dropColumn($col);
                }
            }

            // Восстанавливаем shipping_address
            if (!Schema::hasColumn('orders', 'shipping_address')) {
                $table->text('shipping_address')->nullable()->after('delivery_method');
            }
        });
    }
};
