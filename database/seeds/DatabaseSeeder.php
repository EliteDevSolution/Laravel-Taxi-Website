<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call(RoleTableSeeder::class);
        $this->call(PermissionTableSeeder::class);
        $this->call(AdminsTableSeeder::class);
        $this->call(FleetSeeder::class);
        $this->call(ServiceTypesTableSeeder::class);
        $this->call(DocumentsTableSeeder::class);
        $this->call(SettingsTableSeeder::class);
        $this->call(DemoSeeder::class);
        $this->call(DisputesTableSeeder::class);
        $this->call(ReasonsTableSeeder::class);
        $this->call(PromocodesTableSeeder::class);
    }
}
