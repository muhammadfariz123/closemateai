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
        // 1. Tambah field di tabel users jika belum ada
        if (Schema::hasTable('users')) {
            Schema::table('users', function (Blueprint $table) {
                if (!Schema::hasColumn('users', 'google_calendar_url')) {
                    $table->text('google_calendar_url')->nullable();
                }
                if (!Schema::hasColumn('users', 'ai_prompt')) {
                    $table->longText('ai_prompt')->nullable();
                }
                if (!Schema::hasColumn('users', 'ai_personality_type')) {
                    $table->string('ai_personality_type')->default('casual');
                }
            });
        }

        // 2. Buat tabel faqs jika belum ada
        if (!Schema::hasTable('faqs')) {
            Schema::create('faqs', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id')->index();
                $table->string('question');
                $table->text('answer');
                $table->string('category')->default('General');
                $table->integer('sort_order')->default(0);
                $table->timestamps();

                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('faqs');

        if (Schema::hasTable('users')) {
            Schema::table('users', function (Blueprint $table) {
                if (Schema::hasColumn('users', 'google_calendar_url')) {
                    $table->dropColumn('google_calendar_url');
                }
                if (Schema::hasColumn('users', 'ai_prompt')) {
                    $table->dropColumn('ai_prompt');
                }
                if (Schema::hasColumn('users', 'ai_personality_type')) {
                    $table->dropColumn('ai_personality_type');
                }
            });
        }
    }
};
