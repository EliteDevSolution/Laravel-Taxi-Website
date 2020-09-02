<?php

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
    	Schema::disableForeignKeyConstraints();

    	DB::table('model_has_roles')->truncate();

        DB::table('permissions')->truncate();

        $admin = Role::where('name', 'ADMIN')->first();

    	DB::table('permissions')->insert([
            ['name' => 'dashboard-menus', 'display_name' => 'Box Menus', 'guard_name' => 'admin', 'group_name' => 'Dashboard'],
            ['name' => 'wallet-summary', 'display_name' => 'Wallet Summary', 'guard_name' => 'admin', 'group_name' => 'Dashboard'],
            ['name' => 'recent-rides', 'display_name' => 'Recent Rides', 'guard_name' => 'admin', 'group_name' => 'Dashboard'],


            ['name' => 'dispatcher-panel', 'display_name' => 'Dispatcher Menu', 'guard_name' => 'admin', 'group_name' => 'Dispatcher Panel'],
            ['name' => 'dispatcher-panel-add', 'display_name' => 'Add Rides', 'guard_name' => 'admin', 'group_name' => 'Dispatcher Panel'],

            ['name' => 'dispute-list', 'display_name' => 'Dispute list', 'guard_name' => 'admin', 'group_name' => 'Dispute'],
            ['name' => 'dispute-create', 'display_name' => 'Create Dispute', 'guard_name' => 'admin', 'group_name' => 'Dispute'],
            ['name' => 'dispute-edit', 'display_name' => 'Edit Dispute', 'guard_name' => 'admin', 'group_name' => 'Dispute'],
            ['name' => 'dispute-delete', 'display_name' => 'Delete Dispute', 'guard_name' => 'admin', 'group_name' => 'Dispute'],


            ['name' => 'heat-map', 'display_name' => 'Heat Map', 'guard_name' => 'admin', 'group_name' => 'Map'],
            ['name' => 'god-eye', 'display_name' => 'God\'s Eye', 'guard_name' => 'admin', 'group_name' => 'Map'],

            ['name' => 'user-list', 'display_name' => 'User list', 'guard_name' => 'admin', 'group_name' => 'Users'],
            ['name' => 'user-history', 'display_name' => 'User History', 'guard_name' => 'admin', 'group_name' => 'Users'],
            ['name' => 'user-create', 'display_name' => 'Create User', 'guard_name' => 'admin', 'group_name' => 'Users'],
            ['name' => 'user-edit', 'display_name' => 'Edit User', 'guard_name' => 'admin', 'group_name' => 'Users'],
            ['name' => 'user-delete', 'display_name' => 'Delete User', 'guard_name' => 'admin', 'group_name' => 'Users'],

            ['name' => 'provider-list', 'display_name' => 'Provider list', 'guard_name' => 'admin', 'group_name' => 'Providers'],
            ['name' => 'provider-create', 'display_name' => 'Create Provider', 'guard_name' => 'admin', 'group_name' => 'Providers'],
            ['name' => 'provider-edit', 'display_name' => 'Edit Provider', 'guard_name' => 'admin', 'group_name' => 'Providers'],
            ['name' => 'provider-delete', 'display_name' => 'Delete Provider', 'guard_name' => 'admin', 'group_name' => 'Providers'],
            ['name' => 'provider-status', 'display_name' => 'Provider Status', 'guard_name' => 'admin', 'group_name' => 'Providers'],
            ['name' => 'provider-history', 'display_name' => 'Ride History', 'guard_name' => 'admin', 'group_name' => 'Providers'],
            ['name' => 'provider-statements', 'display_name' => 'Statements', 'guard_name' => 'admin', 'group_name' => 'Providers'],
            ['name' => 'provider-services', 'display_name' => 'Provider Services', 'guard_name' => 'admin', 'group_name' => 'Providers'],
            ['name' => 'provider-service-update', 'display_name' => 'Provider Service Update', 'guard_name' => 'admin', 'group_name' => 'Providers'],
            ['name' => 'provider-service-delete', 'display_name' => 'Provider Service Delete', 'guard_name' => 'admin', 'group_name' => 'Providers'],
            ['name' => 'provider-documents', 'display_name' => 'Provider Documents', 'guard_name' => 'admin', 'group_name' => 'Providers'],
            ['name' => 'provider-document-edit', 'display_name' => 'Provider Document Edit', 'guard_name' => 'admin', 'group_name' => 'Providers'],
            ['name' => 'provider-document-delete', 'display_name' => 'Provider Document Delete', 'guard_name' => 'admin', 'group_name' => 'Providers'],

            ['name' => 'dispatcher-list', 'display_name' => 'Dispatcher list', 'guard_name' => 'admin', 'group_name' => 'Dispatcher'],
            ['name' => 'dispatcher-create', 'display_name' => 'Create Dispatcher', 'guard_name' => 'admin', 'group_name' => 'Dispatcher'],
            ['name' => 'dispatcher-edit', 'display_name' => 'Edit Dispatcher', 'guard_name' => 'admin', 'group_name' => 'Dispatcher'],
            ['name' => 'dispatcher-delete', 'display_name' => 'Delete Dispatcher', 'guard_name' => 'admin', 'group_name' => 'Dispatcher'],

            ['name' => 'fleet-list', 'display_name' => 'Fleet Owner list', 'guard_name' => 'admin', 'group_name' => 'Fleet Owner'],
            ['name' => 'fleet-create', 'display_name' => 'Create Fleet Owner', 'guard_name' => 'admin', 'group_name' => 'Fleet Owner'],
            ['name' => 'fleet-edit', 'display_name' => 'Edit Fleet Owner', 'guard_name' => 'admin', 'group_name' => 'Fleet Owner'],
            ['name' => 'fleet-delete', 'display_name' => 'Delete Fleet Owner', 'guard_name' => 'admin', 'group_name' => 'Fleet Owner'],
            ['name' => 'fleet-providers', 'display_name' => 'Fleet Owner\'s Providers list', 'guard_name' => 'admin', 'group_name' => 'Fleet Owner'],

            ['name' => 'account-manager-list', 'display_name' => 'Account Manager list', 'guard_name' => 'admin', 'group_name' => 'Account Manager'],
            ['name' => 'account-manager-create', 'display_name' => 'Create Account Manager', 'guard_name' => 'admin', 'group_name' => 'Account Manager'],
            ['name' => 'account-manager-edit', 'display_name' => 'Edit Account Manager', 'guard_name' => 'admin', 'group_name' => 'Account Manager'],
            ['name' => 'account-manager-delete', 'display_name' => 'Delete Account Manager', 'guard_name' => 'admin', 'group_name' => 'Account Manager'],


            ['name' => 'dispute-manager-list', 'display_name' => 'Dispute Manager list', 'guard_name' => 'admin', 'group_name' => 'Dispute Manager'],
            ['name' => 'dispute-manager-create', 'display_name' => 'Create Dispute Manager', 'guard_name' => 'admin', 'group_name' => 'Dispute Manager'],
            ['name' => 'dispute-manager-edit', 'display_name' => 'Edit Dispute Manager', 'guard_name' => 'admin', 'group_name' => 'Dispute Manager'],
            ['name' => 'dispute-manager-delete', 'display_name' => 'Delete Dispute Manager', 'guard_name' => 'admin', 'group_name' => 'Dispute Manager'],

            ['name' => 'statements', 'display_name' => 'Statements', 'guard_name' => 'admin', 'group_name' => 'Statements'],

            ['name' => 'settlements', 'display_name' => 'Settlements', 'guard_name' => 'admin', 'group_name' => 'Settlements'],

            ['name' => 'ratings', 'display_name' => 'Ratings', 'guard_name' => 'admin', 'group_name' => 'Ratings'],

            ['name' => 'ride-history', 'display_name' => 'Ride History', 'guard_name' => 'admin', 'group_name' => 'Rides'],
            ['name' => 'ride-delete', 'display_name' => 'Delete Ride', 'guard_name' => 'admin', 'group_name' => 'Rides'],

            ['name' => 'schedule-rides', 'display_name' => 'Schedule Rides', 'guard_name' => 'admin', 'group_name' => 'Rides'],

            ['name' => 'promocodes-list', 'display_name' => 'Promocodes List', 'guard_name' => 'admin', 'group_name' => 'Promocodes'],
            ['name' => 'promocodes-create', 'display_name' => 'Create Promocode', 'guard_name' => 'admin', 'group_name' => 'Promocodes'],
            ['name' => 'promocodes-edit', 'display_name' => 'Edit Promocode', 'guard_name' => 'admin', 'group_name' => 'Promocodes'],
            ['name' => 'promocodes-delete', 'display_name' => 'Delete Promocode', 'guard_name' => 'admin', 'group_name' => 'Promocodes'],

            ['name' => 'service-types-list', 'display_name' => 'Service Types List', 'guard_name' => 'admin', 'group_name' => 'Service Types'],
            ['name' => 'service-types-create', 'display_name' => 'Create Service Type', 'guard_name' => 'admin', 'group_name' => 'Service Types'],
            ['name' => 'service-types-edit', 'display_name' => 'Edit Service Type', 'guard_name' => 'admin', 'group_name' => 'Service Types'],
            ['name' => 'service-types-delete', 'display_name' => 'Delete Service Type', 'guard_name' => 'admin', 'group_name' => 'Service Types'],

            ['name' => 'peak-hour-list', 'display_name' => 'Peak Hour List', 'guard_name' => 'admin', 'group_name' => 'Service Types'],
            ['name' => 'peak-hour-create', 'display_name' => 'Create Peak Hour', 'guard_name' => 'admin', 'group_name' => 'Service Types'],
            ['name' => 'peak-hour-edit', 'display_name' => 'Edit Peak Hour', 'guard_name' => 'admin', 'group_name' => 'Service Types'],
            ['name' => 'peak-hour-delete', 'display_name' => 'Delete Peak Hour', 'guard_name' => 'admin', 'group_name' => 'Service Types'],

            ['name' => 'documents-list', 'display_name' => 'Documents List', 'guard_name' => 'admin', 'group_name' => 'Documents'],
            ['name' => 'documents-create', 'display_name' => 'Create Document', 'guard_name' => 'admin', 'group_name' => 'Documents'],
            ['name' => 'documents-edit', 'display_name' => 'Edit Document', 'guard_name' => 'admin', 'group_name' => 'Documents'],
            ['name' => 'documents-delete', 'display_name' => 'Delete Document', 'guard_name' => 'admin', 'group_name' => 'Documents'],

            ['name' => 'cancel-reasons-list', 'display_name' => 'Cancel Reason List', 'guard_name' => 'admin', 'group_name' => 'Cancel Reasons'],
            ['name' => 'cancel-reasons-create', 'display_name' => 'Create Reason', 'guard_name' => 'admin', 'group_name' => 'Cancel Reasons'],
            ['name' => 'cancel-reasons-edit', 'display_name' => 'Edit Reason', 'guard_name' => 'admin', 'group_name' => 'Cancel Reasons'],
            ['name' => 'cancel-reasons-delete', 'display_name' => 'Delete Reason', 'guard_name' => 'admin', 'group_name' => 'Cancel Reasons'],

            ['name' => 'notification-list', 'display_name' => 'Notifications List', 'guard_name' => 'admin', 'group_name' => 'Notifications'],
            ['name' => 'notification-create', 'display_name' => 'Create Notification', 'guard_name' => 'admin', 'group_name' => 'Notifications'],
            ['name' => 'notification-edit', 'display_name' => 'Edit Notification', 'guard_name' => 'admin', 'group_name' => 'Notifications'],
            ['name' => 'notification-delete', 'display_name' => 'Delete Notification', 'guard_name' => 'admin', 'group_name' => 'Notifications'],

            ['name' => 'lost-item-list', 'display_name' => 'Lost Item List', 'guard_name' => 'admin', 'group_name' => 'Lost Items'],
            ['name' => 'lost-item-create', 'display_name' => 'Create Lost Item', 'guard_name' => 'admin', 'group_name' => 'Lost Items'],
            ['name' => 'lost-item-edit', 'display_name' => 'Edit Lost Item', 'guard_name' => 'admin', 'group_name' => 'Lost Items'],


            ['name' => 'role-list', 'display_name' => 'Role list', 'guard_name' => 'admin', 'group_name' => 'Role'],
            ['name' => 'role-create', 'display_name' => 'Create Role', 'guard_name' => 'admin', 'group_name' => 'Role'],
            ['name' => 'role-edit', 'display_name' => 'Edit Role', 'guard_name' => 'admin', 'group_name' => 'Role'],
            ['name' => 'role-delete', 'display_name' => 'Delete Role', 'guard_name' => 'admin', 'group_name' => 'Role'],

            ['name' => 'sub-admin-list', 'display_name' => 'Sub Admins List', 'guard_name' => 'admin', 'group_name' => 'Sub Admins'],
            ['name' => 'sub-admin-create', 'display_name' => 'Create Sub Admin', 'guard_name' => 'admin', 'group_name' => 'Sub Admins'],
            ['name' => 'sub-admin-edit', 'display_name' => 'Edit Sub Admin', 'guard_name' => 'admin', 'group_name' => 'Sub Admins'],
            ['name' => 'sub-admin-delete', 'display_name' => 'Delete Sub Admin', 'guard_name' => 'admin', 'group_name' => 'Sub Admins'],


            ['name' => 'payment-history', 'display_name' => 'Payment History List', 'guard_name' => 'admin', 'group_name' => 'Payment'],

            ['name' => 'payment-settings', 'display_name' => 'Payment Settings List', 'guard_name' => 'admin', 'group_name' => 'Payment'],

            ['name' => 'site-settings', 'display_name' => 'Site Settings', 'guard_name' => 'admin', 'group_name' => 'Settings'],

            ['name' => 'account-settings', 'display_name' => 'Account Settings', 'guard_name' => 'admin', 'group_name' => 'Settings'],

            ['name' => 'transalations', 'display_name' => 'Translations', 'guard_name' => 'admin', 'group_name' => 'Settings'],

            ['name' => 'change-password', 'display_name' => 'Change Password', 'guard_name' => 'admin', 'group_name' => 'Settings'],

            ['name' => 'cms-pages', 'display_name' => 'CMS Pages', 'guard_name' => 'admin', 'group_name' => 'Pages'],

            ['name' => 'help', 'display_name' => 'Help', 'guard_name' => 'admin', 'group_name' => 'Pages'],

            ['name' => 'custom-push', 'display_name' => 'Custom Push', 'guard_name' => 'admin', 'group_name' => 'Others'],

            ['name' => 'db-backup', 'display_name' => 'DB Backup', 'guard_name' => 'admin', 'group_name' => 'Others']
        ]);

        $admin_permissions = Permission::select('id')->get();

        $admin->syncPermissions($admin_permissions->toArray());

        $permission = [];

        foreach ($admin_permissions as $admin_permission) {
            $permission[] = $admin_permission->id;
        }


        $fleet = Role::where('name', 'FLEET')->first();

        $fleet->permissions()->detach();

        $fleet->permissions()->attach( $permission );


        $dispatcher = Role::where('name', 'DISPATCHER')->first();

        $dispatcher_permissions = Permission::select('id')->whereIn('id', [4,5,57,91,93])->get();

        $dispatcher->syncPermissions($dispatcher_permissions->toArray());

        $dispute = Role::where('name', 'DISPUTE')->first();

        $dispute_permissions = Permission::select('id')->whereIn('id', [6,7,8,9,91,93])->get();

        $dispute->syncPermissions($dispute_permissions->toArray());

        $account = Role::where('name', 'ACCOUNT')->first();

        $account_permissions = Permission::select('id')->whereIn('id', [1,2,3,47,91,93])->get();

        $account->syncPermissions($account_permissions->toArray());

    	Schema::enableForeignKeyConstraints();
    }
}
