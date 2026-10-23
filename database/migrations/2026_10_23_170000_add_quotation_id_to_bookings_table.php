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
        if (!Schema::hasColumn('bookings', 'quotation_id')) {
            Schema::table('bookings', function (Blueprint $table) {
                $table->string('quotation_id')->nullable()->index()->after('user_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('bookings', 'quotation_id')) {
            Schema::table('bookings', function (Blueprint $table) {
                $table->dropColumn('quotation_id');
            });
        }
    }
};
