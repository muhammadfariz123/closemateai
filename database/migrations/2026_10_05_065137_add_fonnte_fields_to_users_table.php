<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('fonnte_token')->nullable();
            $table->string('wa_status')->default('disconnected');
            $table->string('wa_number')->nullable();
            $table->string('webhook_secret')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['fonnte_token', 'wa_status', 'wa_number', 'webhook_secret']);
        });
    }
};
