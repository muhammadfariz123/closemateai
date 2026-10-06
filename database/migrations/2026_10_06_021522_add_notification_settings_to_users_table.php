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
        Schema::table('users', function (Blueprint $table) {
            $table->string('notification_number')->nullable();
            $table->string('owner_whatsapp')->nullable();
            $table->boolean('notify_hot_lead')->default(true);
            $table->boolean('notify_human_takeover')->default(true);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['notification_number', 'owner_whatsapp', 'notify_hot_lead', 'notify_human_takeover']);
        });
    }
};
