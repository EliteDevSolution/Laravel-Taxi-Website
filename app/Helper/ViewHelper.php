<?php

use App\PromocodeUsage;
use App\ServiceType;

function currency($value = '')
{
	if($value == ""){
		return config('constants.currency').number_format(0, 2, '.', '');
	} else {
		return config('constants.currency').number_format($value, 2, '.', '');
	}
}

function distance($value = '')
{
    if($value == ""){
        return "0 ".config('constants.distance', 'Kms');
    }else{
        return $value." ".config('constants.distance', 'Kms');
    }
}

function img($img){
	if($img == ""){
		return asset('main/avatar.jpg');
	}else if (strpos($img, 'http') !== false) {
        return $img;
    }else{
		return asset('storage/'.$img);
	}
}

function image($img){
	if($img == ""){
		return asset('main/avatar.jpg');
	}else{
		return asset($img);
	}
}

function promo_used_count($promo_id)
{
	return PromocodeUsage::where('status','USED')->where('promocode_id',$promo_id)->count();
}

function curl($url)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $return = curl_exec($ch);
    curl_close ($ch);
    return $return;
}

function get_all_service_types()
{
	return ServiceType::all();
}

function demo_mode(){
	if(\Setting::get('demo_mode', 0) == 1) {
        return back()->with('flash_error', 'Disabled for demo purposes! Please contact us at info@appdupe.com');
    }
}

function get_all_language()
{
	return array('en'=>'English','ar'=>'Arabic');
}