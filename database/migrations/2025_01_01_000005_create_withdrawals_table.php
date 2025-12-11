<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWithdrawalsTable extends Migration
{
    public function up(){
        Schema::create('withdrawals', function(Blueprint $table){
            $table->id();
            $table->unsignedBigInteger('user_id')->index();
            $table->decimal('amount',16,2);
            $table->string('status')->default('pending');
            $table->string('method');
            $table->timestamps();
        });
    }
    public function down(){ Schema::dropIfExists('withdrawals'); }
}
