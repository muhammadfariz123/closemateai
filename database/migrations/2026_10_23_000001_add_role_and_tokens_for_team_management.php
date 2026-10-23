<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'role')) {
                $table->string('role')->default('owner');
            }
            if (!Schema::hasColumn('users', 'owner_id')) {
                $table->unsignedBigInteger('owner_id')->nullable();
            }
        });

        Schema::table('team_members', function (Blueprint $table) {
            if (!Schema::hasColumn('team_members', 'token')) {
                $table->string('token')->nullable();
            }
            if (!Schema::hasColumn('team_members', 'reset_token')) {
                $table->string('reset_token')->nullable();
            }
            if (!Schema::hasColumn('team_members', 'reset_sent_at')) {
                $table->timestamp('reset_sent_at')->nullable();
            }
            if (!Schema::hasColumn('team_members', 'joined_at')) {
                $table->timestamp('joined_at')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'role')) {
                $table->dropColumn('role');
            }
            if (Schema::hasColumn('users', 'owner_id')) {
                $table->dropColumn('owner_id');
            }
        });

        Schema::table('team_members', function (Blueprint $table) {
            if (Schema::hasColumn('team_members', 'token')) {
                $table->dropColumn('token');
            }
            if (Schema::hasColumn('team_members', 'reset_token')) {
                $table->dropColumn('reset_token');
            }
            if (Schema::hasColumn('team_members', 'reset_sent_at')) {
                $table->dropColumn('reset_sent_at');
            }
            if (Schema::hasColumn('team_members', 'joined_at')) {
                $table->dropColumn('joined_at');
            }
        });
    }
};
