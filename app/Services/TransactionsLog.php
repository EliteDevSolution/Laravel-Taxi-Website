<?php 

namespace App\Services;

use Illuminate\Http\Request;
use Validator;
use Exception;
use DateTime;
use Auth;
use Lang;
use Setting;
use App\Transactions;
use App\TransactionType;
use App\AdminTransactions;
use App\AdminWallet;
use App\UserWallet;
use App\ProviderWallet;
use App\FleetWallet;
use App\Helpers\Helper;


class TransactionsLog{

    public function __construct(){}

    /**
        * create the transaction and also insert data to appropriate wallets based on the transaction type.
        *
        * @param  array $input data
        * @return array $response with errors or data
    */

    public function CreateTransaction($request){

        $transaction_response=array();

        try{

            echo "<pre>";
            print_r($request);exit;

            $type_id=$request['transaction_type'];

            $Transaction = new Transaction;
            $Transaction->transaction_type = $request['transaction_type'];
            if($request['invoice_id'])
                $Transaction->invoice_id = $request['invoice_id'];
            if($request['user_id'])
                $Transaction->user_id = $request['user_id'];            
            if($request['provider_id'])
                $Transaction->provider_id = $request['provider_id'];            
            $Transaction->admin_id = 1;
            if($request['fleet_id'])
                $Transaction->fleet_id = $request['fleet_id'];            
            $Transaction->created_at = date('Y-m-d h:m:s');            
            $Transaction->save();
            
           /* $total=$tax_price='';
            $location=$this->getLocationDistance($request);
           
            if(!empty($location['errors'])){
                throw new Exception($location['errors']);
            }
            else{                             

                $service_response["data"]=$return_data;                    
            }*/
            $TransactionType = TransactionType::findOrFail($type_id);
           echo "hi";exit;
           // $request['type_ref']=$TransactionType->type_ref.$TransactionType->type_start;

            //$transaction_response=$this->createTransactionWallet($type_id,$request);






        } catch(Exception $e) {
            $transaction_response["errors"]=$e->getMessage();
        }
    
        return $transaction_response;    
    } 

    protected function userRecharge($request){
        //insert the data to admin and user wallets

    }

    protected function rideComplete($request){

    }

    protected function providerSettlement($request){

    }

    protected function fleetSettlement($request){

    }
   

    protected function createAdminWallet($request){
        return ;
    }

    protected function createUserWallet($total){
        return ;
    }

    protected function createProviderWallet($total){
        return ;
    }

    protected function createFleetWallet($total){
        return ;
    }

    protected function createTransactionWallet($type_id, $request){

        echo "hi";exit;

        $result = array();

        switch ($type_id) {
            case 1:
                $result = $this->userRecharge($request);
                break;
            
            case 2:
                $result = $this->rideComplete($request);
                break;

            case 3:
                $result = $this->providerSettlement($request);
                break;

            case 4:
                $result = $this->fleetSettlement($request);
                break;             

            default:                
                break;
        }

        return $result;
    }
    
    
}