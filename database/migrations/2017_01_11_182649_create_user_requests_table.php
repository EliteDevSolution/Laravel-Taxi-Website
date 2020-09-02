<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_requests', function (Blueprint $table) {
            $table->increments('id');
            $table->string('booking_id');
            $table->integer('user_id');
            $table->string('braintree_nonce')->nullable();
            $table->integer('provider_id')->default(0);
            $table->integer('current_provider_id');
            $table->integer('service_type_id');
            $table->integer('promocode_id');
            $table->integer('rental_hours')->nullable();
            $table->enum('status', [
                    'SEARCHING',
                    'CANCELLED',
                    'ACCEPTED', 
                    'STARTED',
                    'ARRIVED',
                    'PICKEDUP',
                    'DROPPED',
                    'COMPLETED',
                    'SCHEDULED',
                ]);

            $table->enum('cancelled_by', [
                    'NONE',
                    'USER',
                    'PROVIDER'
                ]);

            $table->string('cancel_reason')->nullable();

            $table->enum('payment_mode', [
                    'BRAINTREE',
                    'CASH',
                    'CARD',
                    'PAYPAL',
                    'PAYPAL-ADAPTIVE',
                    'PAYUMONEY',
                    'PAYTM'
                ]);
            
            $table->boolean('paid')->default(0);
            $table->enum('is_track', ['YES','NO'])->default('NO');

            $table->double('distance', 15, 8);
            $table->string('travel_time')->nullable();

            $table->enum('unit', [
                    'Kms',
                    'Miles'                    
                ])->default('Kms');

            $table->string('otp')->nullable();
            
            $table->string('s_address')->nullable();
            $table->double('s_latitude', 15, 8);
            $table->double('s_longitude', 15, 8);
            
            $table->string('d_address')->nullable();
            $table->double('d_latitude', 15, 8)->nullable();
            $table->double('d_longitude', 15, 8)->nullable();

            $table->double('track_distance', 15, 8)->default(0);
            $table->double('track_latitude', 15, 8)->default(0);
            $table->double('track_longitude', 15, 8)->default(0);

            $table->longText('destination_log')->nullable();
            $table->boolean('is_drop_location')->default(1);
            $table->boolean('is_instant_ride')->default(0);
            $table->boolean('is_dispute')->default(0);
            
            $table->timestamp('assigned_at')->nullable();
            $table->timestamp('schedule_at')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('finished_at')->nullable();
            

            $table->enum('is_scheduled', [
                    'YES',
                    'NO',
                ])->default('NO');

            $table->boolean('user_rated')->default(0);
            $table->boolean('provider_rated')->default(0);
            $table->boolean('use_wallet')->default(0);
            $table->boolean('surge')->default(0);
            $table->longText('route_key');
            $table->string('nonce')->nullable();

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
        Schema::dropIfExists('user_requests');
    }
}
