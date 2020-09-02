<?php

use App\Admin;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class AdminsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('admins')->truncate();
        DB::table('admin_wallet')->truncate();
        $users = Admin::create([
            'name' => 'Tranxit',
            'email' => 'admin@demo.com',
            'password' => bcrypt('123456'),
        ]);

        $role = Role::where('name', 'ADMIN')->first();

        if($role != null) $users->assignRole($role->id);

        $dispatcher = Admin::create([
            'name' => 'Demo Dispatcher',
            'email' => 'dispatcher@demo.com',
            'password' => bcrypt('123456'),
        ]);

        $role = Role::where('name', 'DISPATCHER')->first();

        if($role != null) $dispatcher->assignRole($role->id);

        $account = Admin::create([
            'name' => 'Demo Account',
            'email' => 'account@demo.com',
            'password' => bcrypt('123456'),
        ]);

        $role = Role::where('name', 'ACCOUNT')->first();

        if($role != null) $account->assignRole($role->id);

        $dispute = Admin::create([
            'name' => 'Demo Dispute',
            'email' => 'dispute@demo.com',
            'password' => bcrypt('123456'),
        ]);

        $role = Role::where('name', 'DISPUTE')->first();

        if($role != null) $dispute->assignRole($role->id);
    }
}
