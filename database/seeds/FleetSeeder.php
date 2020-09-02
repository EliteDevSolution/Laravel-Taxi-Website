<?php

use App\Fleet;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class FleetSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('fleets')->truncate();
        DB::table('fleet_cards')->truncate();
        DB::table('fleet_wallet')->truncate();

        $users = Fleet::create([
            'name' => 'Demo',
            'email' => 'fleet@demo.com',
            'password' => bcrypt('123456'),
        ]);

        $role = Role::where('name', 'FLEET')->first();

        if($role != null) $users->assignRole($role->id);
    }
}
