<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Chat;
use App\Models\Message;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Message::truncate();
        Chat::query()->delete();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
