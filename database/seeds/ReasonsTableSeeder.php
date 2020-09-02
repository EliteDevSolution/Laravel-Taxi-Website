<?php

use Illuminate\Database\Seeder;

use Carbon\Carbon;

class ReasonsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
    	DB::table('reasons')->truncate();
        DB::table('reasons')->insert([
            [
                'type' => 'PROVIDER',
                'reason' => 'Take long time to reach pickup point',
                'status' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'type' => 'PROVIDER',
                'dispute_name' => 'Heavy Traffic',
                'status' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'type' => 'PROVIDER',
                'dispute_name' => 'User choose wrong location',
                'status' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'type' => 'PROVIDER',
                'dispute_name' => 'My reason not listed',
                'status' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'type' => 'PROVIDER',
                'dispute_name' => 'User Unavailable',
                'status' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'type' => 'USER',
                'dispute_name' => 'My reason not listed',
                'status' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'type' => 'USER',
                'dispute_name' => 'Unable to contact driver',
                'status' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'type' => 'USER',
                'dispute_name' => 'Expected a shoter wait time',
                'status' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'type' => 'USER',
                'dispute_name' => 'Driver denied to come and pikcup',
                'status' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'type' => 'USER',
                'dispute_name' => 'Driver denied to go to destination',
                'status' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'type' => 'USER',
                'dispute_name' => 'Driver Charged Extra',
                'status' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ]);
    }
}
