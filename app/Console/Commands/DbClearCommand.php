<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;

class DbClearCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cronjob:demodata';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clearing the demo data weekily basics';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        if(Setting::get('demo_mode', 0) == 1) {
            \Log::info('demo data deleting');
            $uservalues=array('stripe_cust_id'=>NULL,'wallet_balance'=>0,'rating'=>5);
            DB::table('users')->where('id', '>', 100)->delete();
            DB::table('users')->update($uservalues);
            DB::table('password_resets')->delete();
            DB::table('cards')->delete();
            DB::table('user_wallet')->delete();
            DB::table('providers')->where('id', '>', 100)->delete();
            DB::table('providers')->update($uservalues);
            DB::table('provider_cards')->delete();
            DB::table('provider_devices')->where('provider_id', '>', 100)->delete();
            DB::table('provider_profiles')->where('provider_id', '>', 100)->delete();
            DB::table('provider_documents')->where('provider_id', '>', 100)->delete();
            DB::table('provider_services')->where('provider_id', '>', 100)->delete();
            DB::table('provider_wallet')->delete();
            DB::table('admins')->where('id', '>', 1)->delete();
            DB::table('admin_wallet')->delete();
            DB::table('fleets')->where('id', '>', 1)->delete();
            $othervalues=array('stripe_cust_id'=>NULL,'wallet_balance'=>0);
            DB::table('fleets')->update($othervalues);
            DB::table('fleet_password_resets')->delete();
            DB::table('fleet_cards')->delete();
            DB::table('fleet_wallet')->delete();
            DB::table('accounts')->where('id', '>', 1)->delete();
            DB::table('account_password_resets')->delete();
            DB::table('dispatchers')->where('id', '>', 1)->delete();
            DB::table('dispatcher_password_resets')->delete();

            //other tables
            DB::table('custom_pushes')->delete();
            DB::table('favourite_locations')->delete();
            DB::table('promocodes')->delete();
            DB::table('promocode_usages')->delete();
            DB::table('request_filters')->delete();
            DB::table('user_requests')->delete();
            DB::table('user_request_payments')->delete();
            DB::table('user_request_ratings')->delete();
            DB::table('wallet_requests')->delete();
        }    
        
    }
}
