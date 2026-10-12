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
        if (Schema::hasTable('users')) {
            Schema::table('users', function (Blueprint $table) {
                if (!Schema::hasColumn('users', 'vendor_address')) {
                    $table->text('vendor_address')->nullable();
                }
                if (!Schema::hasColumn('users', 'invoice_format')) {
                    $table->string('invoice_format')->nullable();
                }
            });
        }

        if (!Schema::hasTable('invoice_packages')) {
            Schema::create('invoice_packages', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id')->index();
                $table->string('name');
                $table->decimal('price', 15, 2)->default(0);
                $table->decimal('discount', 15, 2)->default(0);
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
        Schema::dropIfExists('invoice_packages');

        if (Schema::hasTable('users')) {
            Schema::table('users', function (Blueprint $table) {
                if (Schema::hasColumn('users', 'vendor_address')) {
                    $table->dropColumn('vendor_address');
                }
                if (Schema::hasColumn('users', 'invoice_format')) {
                    $table->dropColumn('invoice_format');
                }
            });
        }
    }
};
