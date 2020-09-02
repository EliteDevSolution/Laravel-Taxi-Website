<?php

use Illuminate\Database\Seeder;

use Carbon\Carbon;

class DisputesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
    	DB::table('disputes')->truncate();
        DB::table('disputes')->insert([
            [
                'dispute_type' => 'provider',
                'dispute_name' => 'User not familiar with route and changed route',
                'status' => 'active',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'dispute_type' => 'provider',
                'dispute_name' => 'User arrogant and rude',
                'status' => 'active',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'dispute_type' => 'provider',
                'dispute_name' => 'User not paid amount',
                'status' => 'active',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],            
            [
                'dispute_type' => 'user',
                'dispute_name' => "I didn't feel safe during the ride",
                'status' => 'active',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'dispute_type' => 'user',
                'dispute_name' => 'Driver Unprofessional',
                'status' => 'active',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'dispute_type' => 'user',
                'dispute_name' => 'Driver took long and incorrect route',
                'status' => 'active',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'dispute_type' => 'user',
                'dispute_name' => 'Driver Delayed Pickup',
                'status' => 'active',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'dispute_type' => 'user',
                'dispute_name' => 'Driver changed route and charged extra amont',
                'status' => 'active',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ]);
    }
}
