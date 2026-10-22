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
        if (!Schema::hasTable('team_members')) {
            Schema::create('team_members', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id');
                $table->string('name')->nullable();
                $table->string('email');
                $table->string('phone')->nullable();
                $table->string('role')->default('Admin CS');
                $table->string('status')->default('pending'); // 'pending', 'active', 'inactive'
                $table->timestamp('invited_at')->nullable();
                $table->timestamp('joined_at')->nullable();
                $table->string('token')->nullable();
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
        Schema::dropIfExists('team_members');
    }
};
