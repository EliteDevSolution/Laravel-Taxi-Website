<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWalletRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('wallet_requests', function (Blueprint $table) {
            $table->increments('id');
            $table->string('alias_id');            
            $table->string('request_from')->comment('user,provider,fleet');
            $table->integer('from_id');
            $table->string('from_desc')->nullable();
            $table->enum('type', [
                    'C',
                    'D',
                ]);
            $table->double('amount', 15, 8)->default(0);
            $table->string('send_by')->comment('online,offline')->nullable();
            $table->string('send_desc')->nullable();
            $table->tinyInteger('status')->comment('0-Pendig,1-Approved,2-cancel')->default(0);
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
        Schema::dropIfExists('wallet_requests');
    }
}
