<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDepositsTable extends Migration
{
    public function up(){
        Schema::create('deposits', function(Blueprint $table){
            $table->id();
            $table->unsignedBigInteger('user_id')->index();
            $table->decimal('amount',16,2);
            $table->string('status')->default('pending');
            $table->string('tx_ref')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();
        });
    }
    public function down(){ Schema::dropIfExists('deposits'); }
}
