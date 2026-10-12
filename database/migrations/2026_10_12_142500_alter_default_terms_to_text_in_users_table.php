<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('users') && Schema::hasColumn('users', 'default_terms')) {
            try {
                $driver = DB::getDriverName();
                if ($driver === 'pgsql') {
                    DB::statement('ALTER TABLE users ALTER COLUMN default_terms TYPE text USING default_terms::text');
                } elseif ($driver === 'mysql') {
                    DB::statement('ALTER TABLE users MODIFY COLUMN default_terms LONGTEXT NULL');
                } else {
                    Schema::table('users', function (Blueprint $table) {
                        $table->text('default_terms')->nullable()->change();
                    });
                }
            } catch (\Throwable $e) {
                Log::warning('alter default_terms error: ' . $e->getMessage());
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Keep as text, no need to revert to json
    }
};
