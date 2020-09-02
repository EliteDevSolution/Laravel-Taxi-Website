<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFleetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fleets', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->string('company')->nullable();
            $table->string('mobile')->nullable();
            $table->string('logo')->nullable();
            $table->rememberToken();
            $table->double('commission', 5,2)->default(0);
            $table->double('wallet_balance', 10,2)->default(0);
            $table->string('stripe_cust_id')->nullable();
            $table->string('language',10)->nullable();
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
        Schema::drop('fleets');
    }
}
