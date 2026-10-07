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
        Schema::create('chats', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id'); // Vendor / Admin
            $table->string('client_wa_number'); // Client's WhatsApp number
            $table->string('client_name')->nullable();
            $table->integer('ai_reply_count')->default(0);
            $table->boolean('is_human_takeover')->default(false);
            $table->string('status')->default('Belum Dihandle'); // e.g. Belum Dihandle, Hot Lead, Closed
            $table->text('quick_notes')->nullable();
            $table->string('event_date')->nullable();
            $table->string('location')->nullable();
            $table->string('budget')->nullable();
            $table->timestamps();
            
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chats');
    }
};
