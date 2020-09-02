<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateReferralEarningsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('referral_earnings', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('referrer_id');
            $table->tinyInteger('type')->default(1)->comment('1-user,2-provider');
            $table->double('amount', 10, 2)->default(0);
            $table->mediumInteger('count')->default(0);
            $table->string('referral_histroy_id',50)->nullable();            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('referral_earnings');
    }
}
