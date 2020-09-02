<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePaymentLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payment_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('is_wallet')->default(0);
            $table->string('user_type')->nullable()->comment('user or provider');
            $table->string('payment_mode')->nullable();
            $table->integer('user_id')->comment('user id or provider id');
            $table->float('amount')->default(0);
            $table->string('transaction_code')->nullable()->comment('Random code generated during payment');
            $table->string('transaction_id')->nullable()->comment('Foreign key of the user request or wallet table');
            $table->text('response')->nullable();
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
        Schema::dropIfExists('payment_logs');
    }
}
