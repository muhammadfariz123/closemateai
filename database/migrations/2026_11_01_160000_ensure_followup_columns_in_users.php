<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('users')) {
            Schema::table('users', function (Blueprint $table) {
                if (!Schema::hasColumn('users', 'fu_is_active')) {
                    $table->boolean('fu_is_active')->default(false);
                }
                if (!Schema::hasColumn('users', 'fu_1_days')) {
                    $table->integer('fu_1_days')->default(1);
                }
                if (!Schema::hasColumn('users', 'fu_1_template')) {
                    $table->text('fu_1_template')->nullable();
                }
                if (!Schema::hasColumn('users', 'fu_2_days')) {
                    $table->integer('fu_2_days')->default(3);
                }
                if (!Schema::hasColumn('users', 'fu_2_template')) {
                    $table->text('fu_2_template')->nullable();
                }
                if (!Schema::hasColumn('users', 'fu_3_days')) {
                    $table->integer('fu_3_days')->default(7);
                }
                if (!Schema::hasColumn('users', 'fu_3_template')) {
                    $table->text('fu_3_template')->nullable();
                }
            });
        }
    }

    public function down(): void
    {
    }
};
