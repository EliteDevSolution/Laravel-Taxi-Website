<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserRequestLostItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_request_lost_items', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('request_id');
            $table->integer('parent_id')->nullable();
            $table->integer('user_id');            
            $table->string('lost_item_name');
            $table->string('comments')->nullable();
            $table->enum('comments_by', ['user', 'admin']);
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
        Schema::dropIfExists('user_request_lost_items');
    }
}
