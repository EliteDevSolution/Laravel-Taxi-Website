<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('first_name');
            $table->string('last_name');
            $table->enum('payment_mode', ['BRAINTREE', 'CASH', 'CARD', 'PAYPAL', 'PAYPAL-ADAPTIVE', 'PAYUMONEY', 'PAYTM']);
            $table->enum('user_type', ['INSTANT', 'NORMAL'])->default('NORMAL');
            $table->string('email')->unique();
            $table->enum('gender', [
                    'MALE',
                    'FEMALE'
                ])->default('MALE');
            $table->string('country_code')->nullable();
            $table->string('mobile')->unique();
            $table->string('password');
            $table->string('picture')->nullable();
            $table->string('device_token')->nullable();
            $table->string('device_id')->nullable();
            $table->enum('device_type',array('android','ios'));
            $table->enum('login_by',array('manual','facebook','google'));
            $table->string('social_unique_id')->nullable();
            $table->double('latitude', 15, 8)->nullable();
            $table->double('longitude',15,8)->nullable();
            $table->string('stripe_cust_id')->nullable();
            $table->float('wallet_balance')->default(0);
            $table->decimal('rating', 4, 2)->default(5);
            $table->mediumInteger('otp')->default(0);
            $table->string('language')->nullable();
            $table->string('qrcode_url')->nullable();
            $table->string('referral_unique_id',10)->nullable();
            $table->mediumInteger('referal_count')->default(0);
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
        Schema::dropIfExists('users');
    }
}
