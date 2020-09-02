<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class SettingsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('settings')->delete();
        DB::table('settings')->insert([
            [
                'key' => 'demo_mode',
                'value' => 0
            ],
            [
                'key' => 'help',
                'value' => '<p>Help page</p>'
            ],
            [
                'key' => 'page_privacy',
                'value' => '<p>Privacy Policy</p>'
            ],
            [
                'key' => 'terms',
                'value' => '<p>Terms and Conditions</p>'
            ],
            [
                'key' => 'cancel',
                'value' => '<p>Cancelation Policy</p>'
            ]
        ]);
    }
}
