<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserRequestDisputesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_request_disputes', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('request_id');
            $table->enum('dispute_type', ['user', 'provider']);
            $table->integer('user_id');
            $table->integer('provider_id');
            $table->string('dispute_name');
            $table->string('dispute_title')->nullable();
            $table->string('comments')->nullable();
            $table->float('refund_amount',10,2)->default(0);
            $table->enum('status', ['open', 'closed'])->default('open');
            $table->tinyInteger('is_admin')->default(0);
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
        Schema::dropIfExists('user_request_disputes');
    }
}
