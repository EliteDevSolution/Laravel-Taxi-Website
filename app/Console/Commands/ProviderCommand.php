<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;
use App\Http\Controllers\SendPushNotification;
use App\Http\Controllers\AdminController;
use Carbon\Carbon;
use App\Provider;

class ProviderCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cronjob:providers';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update the provider status';

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
        //Temporarily disable inactive providers        
        $Providers = Provider::with('service')
            ->whereHas('service', function($query){
                        $query->where('status','active');                       
                    })           
            ->where('updated_at','<=',\Carbon\Carbon::now()->subMinutes(10))->get();            
                
        if(!empty($Providers)){
            foreach($Providers as $Provider){                
                DB::table('provider_services')->where('provider_id',$Provider->id)->update(['status' =>'hold']);
                //send push to provider
                (new SendPushNotification)->provider_hold($Provider->id);
             }
        }                    

    }
}
