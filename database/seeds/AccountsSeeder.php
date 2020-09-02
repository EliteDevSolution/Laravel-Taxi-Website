<?php

use App\Account;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class AccountsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('accounts')->truncate();

        $users = Account::create([
            'name' => 'Demo',
            'email' => 'account@demo.com',
            'password' => bcrypt('123456'),
        ]);

        $role = Role::where('name', 'ACCOUNT')->first();

        if($role != null) $users->assignRole($role->id);
    }
}
