<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateReferralHistroyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('referral_histroy', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('referrer_id');
            $table->tinyInteger('type')->default(1)->comment('1-user,2-provider');
            $table->integer('referral_id');
            $table->longText('referral_data')->nullable();
            $table->enum('status', ['P', 'C'])->default('C');
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
        Schema::dropIfExists('referral_histroy');
    }
}
