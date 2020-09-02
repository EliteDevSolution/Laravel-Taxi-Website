<?php

use App\Dispatcher;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class DispatcherSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('dispatchers')->truncate();

        $users = Dispatcher::create([
            'name' => 'Demo',
            'email' => 'dispatcher@demo.com',
            'password' => bcrypt('123456'),
        ]);

        $role = Role::where('name', 'DISPATCHER')->first();

        if($role != null) $users->assignRole($role->id);
    }
}
