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
        Schema::create('plans', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->text('description')->nullable();
            $table->decimal('min_amount', 12, 2)->nullable();
            $table->decimal('max_amount', 12, 2)->nullable();
            $table->decimal('daily_roi', 5, 2)->nullable();
            $table->integer('duration_days')->nullable();
            $table->decimal('total_return', 12, 2)->nullable();
            $table->enum('status', ['1', '0'])->default('1')->comment('1=>active,0=>inactive')->nullable();
            $table->enum('type', ['1', '2', '3', '4', '5', '6'])->comment('1=>basic,2=>advanced,3=>premium,4=>expert,5=>master,6=>professional')->default('1')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plans');
    }
};
