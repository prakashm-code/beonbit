<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReferralsTable extends Migration
{
    public function up(){
        Schema::create('referrals', function(Blueprint $table){
            $table->id();
            $table->unsignedBigInteger('referrer_id')->index();
            $table->unsignedBigInteger('referred_id')->index();
            $table->decimal('income',16,2)->default(0);
            $table->integer('level')->default(1);
            $table->timestamps();
        });
    }
    public function down(){ Schema::dropIfExists('referrals'); }
}
