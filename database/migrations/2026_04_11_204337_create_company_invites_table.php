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
        Schema::create('company_invites', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('company_id');
            $table->string('company_type');

            $table->string('email');

            $table->string('role')->default('member');

            $table->string('token')->unique();

            $table->timestamp('accepted_at')->nullable();
            $table->timestamp('expires_at')->nullable();

            $table->unsignedBigInteger('invited_by');

            $table->timestamps();

            $table->index(['company_id', 'company_type']);
            $table->index('email');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('company_invites');
    }
};
