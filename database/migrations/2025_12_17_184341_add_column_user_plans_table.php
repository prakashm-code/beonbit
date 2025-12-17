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
        Schema::table('user_plans', function (Blueprint $table) {
            $table->decimal('daily_interest', 15, 2)
                ->default(0)
                ->after('daily_return_percent');

            $table->decimal('total_interest', 15, 2)
                ->default(0)
                ->after('daily_interest');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
