<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProvidersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('providers', function (Blueprint $table) {
            $table->increments('id');
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->unique();
            $table->enum('gender', [
                    'MALE',
                    'FEMALE'
                ])->default('MALE');
            $table->string('country_code')->nullable();
            $table->string('mobile')->unique();
            $table->string('password');
            $table->string('avatar')->nullable();
            $table->decimal('rating', 4, 2)->default(5);
            $table->enum('status', ['document','card','onboarding', 'approved', 'banned', 'balance'])->default('document');
            $table->integer('fleet')->default(0);
            $table->double('latitude', 15, 8)->nullable();
            $table->double('longitude', 15, 8)->nullable();
            $table->string('stripe_acc_id')->nullable();
            $table->string('stripe_cust_id')->nullable();
            $table->string('paypal_email')->nullable();
            $table->enum('login_by',array('manual','facebook','google'));
            $table->string('social_unique_id')->nullable();
            $table->mediumInteger('otp')->default(0);
            $table->double('wallet_balance', 10,2)->default(0);
            $table->string('referral_unique_id',10)->nullable();
            $table->string('qrcode_url')->nullable();
            $table->rememberToken();
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
        Schema::drop('providers');
    }
}
