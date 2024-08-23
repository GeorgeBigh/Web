<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('quizzes', function (Blueprint $table) {
            // Add the column only if it does not already exist
            if (!Schema::hasColumn('quizzes', 'live_stream')) {
                $table->boolean('live_stream')->default(0);
            }
        });
    }

    public function down(): void
    {
        Schema::table('quizzes', function (Blueprint $table) {
            // Drop the column only if it exists
            if (Schema::hasColumn('quizzes', 'live_stream')) {
                $table->dropColumn('live_stream');
            }
        });
    }
};
