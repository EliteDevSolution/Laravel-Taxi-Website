<?php 

namespace App\Helpers;

use File;
use Setting;
use Illuminate\Support\Facades\Mail;
use App\WalletRequests;

class Helper
{

	public static function upload_picture($picture)
	{
		$file_name = time();
		$file_name .= rand();
		$file_name = sha1($file_name);
		if ($picture) {
			$ext = $picture->getClientOriginalExtension();
			$picture->move(public_path() . "/uploads", $file_name . "." . $ext);
			$local_url = $file_name . "." . $ext;

			$s3_url = url('/').'/uploads/'.$local_url;
			
			return $s3_url;
		}
		return "";
	}


	public static function delete_picture($picture) {
		File::delete( public_path() . "/uploads/" . basename($picture));
		return true;
	}
	public static function routeCheck($routeName) {
		
		return true;
	}

	public static function generate_booking_id() {
		return config('constants.booking_prefix').mt_rand(100000, 999999);
	}

	public static function site_sendmail($user){

		$site_details=Setting::all();

		Mail::send('emails.invoice', ['Email' => $user], function ($mail) use ($user,$site_details) {
		   
			//$mail->to('tamilvanan@blockchainappfactory.com')->subject('Invoice');

			$mail->to($user->user->email, $user->user->first_name.' '.$user->user->last_name)->subject('Invoice');
		});

		/*if( count(Mail::failures()) > 0 ) {

		   echo "There was one or more failures. They were: <br />";

		   foreach(Mail::failures() as $email_address) {
			   echo " - $email_address <br />";
			}

		} else {
			echo "No errors, all sent successfully!";
		}*/

		return true;
	}

	public static function site_registermail($user){
		
		$site_details=Setting::all();
		
		Mail::send('emails.welcome', ['user' => $user], function ($mail) use ($user) {
		   // $mail->from('harapriya@appoets.com', 'Your Application');

			//$mail->to('tamilvanan@blockchainappfactory.com')->subject('Invoice');

			$mail->to($user->email, $user->first_name.' '.$user->last_name)->subject('Welcome');
		});

		if( count(Mail::failures()) > 0 ) {

		   echo "There was one or more failures. They were: <br />";

		   foreach(Mail::failures() as $email_address) {
			   echo " - $email_address <br />";
			}

		} else {
			echo "No errors, all sent successfully!";
		}
		
		return true;
	}

	public function formatPagination($pageobj){

		$results = new \stdClass();

		$results->links=$pageobj->links();
		$results->count=$pageobj->count();
		$results->currentPage=$pageobj->currentPage();
		$results->firstItem=$pageobj->firstItem();
		$results->hasMorePages=$pageobj->hasMorePages();
		$results->lastItem=$pageobj->lastItem();
		$results->lastPage=$pageobj->lastPage();
		$results->nextPageUrl=$pageobj->nextPageUrl();
		$results->perPage=$pageobj->perPage();
		$results->previousPageUrl=$pageobj->previousPageUrl();
		$results->total=$pageobj->total();
		//$results->url=$pageobj->url();  

		return $results;
	}

	public static function generate_request_id($type) {

		if($type=='provider'){
			$tr_str='PSET';
		}
		else{
			$tr_str='FSET';
		}

		$typecount=WalletRequests::where('request_from',$type)->count();

		if(!empty($typecount))
			$next_id=$typecount+1;
		else
			$next_id=1;

		$alias_id=$tr_str.str_pad($next_id, 6, 0, STR_PAD_LEFT); 
			
		return $alias_id;

	}

	public static function curl($url)
	{
	    $ch = curl_init();
	    curl_setopt($ch, CURLOPT_URL, $url);
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	    $return = curl_exec($ch);
	    curl_close ($ch);
	    return $return;
	}


	public static function getAddress($latitude,$longitude){

		if(!empty($latitude) && !empty($longitude)){
			//Send request and receive json data by address
			$geocodeFromLatLong = file_get_contents('https://maps.googleapis.com/maps/api/geocode/json?latlng='.trim($latitude).','.trim($longitude).'&sensor=false&key='.config('constants.map_key')); 
			$output = json_decode($geocodeFromLatLong);
			$status = $output->status;
			//Get address from json data
			$address = ($status=="OK")?$output->results[0]->formatted_address:'';
			//Return address of the given latitude and longitude
			if(!empty($address)){
				return $address;
			}else{
				return false;
			}
		}else{
			return false;   
		}
	}
	public static function upload_qrCode($phone,$file)
	{
		$file_name = time();
		$file_name .= rand();
		if ($file) {
			$fileName       = $file_name.'_'.$phone.".png";
			file_put_contents(public_path().'/uploads/'.$fileName,$file);
			$qrcode_url = 'uploads/'.$fileName;
			return $qrcode_url;
		}
		return "";
	}

}
