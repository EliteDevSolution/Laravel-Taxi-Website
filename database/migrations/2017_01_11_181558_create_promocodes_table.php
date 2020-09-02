<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePromocodesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('promocodes', function (Blueprint $table) {
            $table->increments('id');
            $table->string('promo_code');
            $table->float('percentage',5, 2)->default(0);
            $table->float('max_amount',10, 2)->default(0);
            $table->string('promo_description');
            $table->dateTime('expiration');
            $table->enum('status', ['ADDED','EXPIRED']);
            $table->softDeletes();
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
        Schema::dropIfExists('promocodes');
    }
}
