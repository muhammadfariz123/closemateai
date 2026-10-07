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
            $table->boolean('ai_limit_enabled')->default(false);
            $table->boolean('ai_multi_bubble_enabled')->default(true);
            $table->integer('ai_max_bubbles')->default(3);
            $table->boolean('ai_require_data_before_price')->default(true);
            $table->json('ai_required_data')->nullable(); // will store JSON array of required fields
            $table->text('ai_custom_questions')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'ai_limit_enabled',
                'ai_multi_bubble_enabled',
                'ai_max_bubbles',
                'ai_require_data_before_price',
                'ai_required_data',
                'ai_custom_questions'
            ]);
        });
    }
};
