<?php

namespace App\Http\Controllers\Resource;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Exception;
use Auth;
use DB;
use App\FavouriteLocation;
use App\UserRequests;

class FavouriteLocationResource extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $HomeLocation = FavouriteLocation::where(['type'=>'home','user_id'=>Auth::user()->id])->get();
        $WorkLocation = FavouriteLocation::where(['type'=>'work','user_id'=>Auth::user()->id])->get();
        $OthersLocation = FavouriteLocation::where(['type'=>'others','user_id'=>Auth::user()->id])->get();

        $SourceAddressRecent = UserRequests::select(['s_address as address','s_latitude as latitude','s_longitude as longitude'])->where('user_id',Auth::user()->id)->distinct('s_address');
        $DistinationAddressRecent = UserRequests::select(['d_address as address','d_latitude as latitude','d_longitude as longitude'])->where('user_id',Auth::user()->id)->distinct('d_address');

        $RecentLocation = $SourceAddressRecent->union($DistinationAddressRecent)->skip(0)->take(10)->get();        
       
        $SearchLocation = ["home" => $HomeLocation,"work"=>$WorkLocation,"others"=>$OthersLocation,
            "recent"=>$RecentLocation];

        return $SearchLocation;

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'address' => 'required|max:255',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'type' => 'required|in:home,work,recent,others'
        ]);

        try{

            $Location['user_id'] = Auth::user()->id;
            $Location['address'] = $request->address;
            $Location['latitude'] = $request->latitude;
            $Location['longitude'] = $request->longitude;
            $Location['type'] = $request->type;

            $IsExists = FavouriteLocation::where($Location)->count();

            if($IsExists==0){
                FavouriteLocation::create($Location);
                if($request->ajax()){
                    return response()->json(['message' => trans('admin.favourite_location_msgs.favourite_saved')],200); 
                }else{
                    return back()->with('flash_success',trans('admin.favourite_location_msgs.favourite_saved')); 
                }
            }else{
                if($request->ajax()){
                    return response()->json(['error' => trans('admin.favourite_location_msgs.favourite_exists')],400); 
                }else{
                    return back()->with('flash_error', trans('admin.favourite_location_msgs.favourite_exists')); 
                }
            }
        } catch (ModelNotFoundException $e) {
            if($request->ajax()){
                return response()->json(['error' => trans('admin.favourite_location_msgs.favourite_not_found')],500); 
            }else{
                return back()->with('flash_error', trans('admin.favourite_location_msgs.favourite_not_found'));
            }
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
            $Favourite = FavouriteLocation::findOrFail($id);
            if($request->ajax()){
               return $Favourite;  
            }else{
               return back()->with('flash_error', trans('admin.favourite_location_msgs.favourite_not_found')); 
            }
        } catch (ModelNotFoundException $e) {
            if($request->ajax()){
               return response()->json(['error' => trans('admin.favourite_location_msgs.favourite_not_found')],500);  
            }else{
               return back()->with('flash_error', trans('admin.favourite_location_msgs.favourite_not_found')); 
            }
            
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        try {
            $Favourite = FavouriteLocation::findOrFail($id);
            
            if($request->ajax()){
               return $Favourite;  
            }else{
               return back()->with('flash_error', trans('admin.favourite_location_msgs.favourite_not_found')); 
            }
        } catch (ModelNotFoundException $e) {
            if($request->ajax()){
               return response()->json(['error' => trans('admin.favourite_location_msgs.favourite_not_found')],500);  
            }else{
               return back()->with('flash_error', trans('admin.favourite_location_msgs.favourite_not_found')); 
            }
            
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'address' => 'required|max:255',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'type' => 'required|in:home,work,recent,others'
        ]);

        try{
            $UpdateLocation = FavouriteLocation::findOrFail($id);

            $Location['user_id']   = Auth::user()->id;
            $Location['address']   = $request->address;
            $Location['latitude']  = $request->latitude;
            $Location['longitude'] = $request->longitude;
            $Location['type']      = $request->type;
            $IsExists = FavouriteLocation::where($Location)->count();

            if($IsExists==0){

                $UpdateLocation->user_id = Auth::user()->id;
                $UpdateLocation->address = $request->address;
                $UpdateLocation->latitude = $request->latitude;
                $UpdateLocation->longitude = $request->longitude;
                $UpdateLocation->type = $request->type;
                $UpdateLocation->save();

                if($request->ajax()){
                    return response()->json(['message' => trans('admin.favourite_location_msgs.favourite_update')],200); 
                }else{
                    return back()->with('flash_success',trans('admin.favourite_location_msgs.favourite_update')); 
                }
                
            }else{
                if($request->ajax()){
                    return response()->json(['error' => trans('admin.favourite_location_msgs.favourite_exists')],400); 
                }else{
                    return back()->with('flash_error', trans('admin.favourite_location_msgs.favourite_exists')); 
                }
                
            }

        } catch (ModelNotFoundException $e) {
            if($request->ajax()){
                return response()->json(['error' => trans('admin.favourite_location_msgs.favourite_not_found')],500); 
            }else{
                return back()->with('flash_error', trans('admin.favourite_location_msgs.favourite_not_found'));
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request,$id)
    {
        try {
            $fav_count = FavouriteLocation::whereid($id)->exists();
            if ($fav_count == true) {
               FavouriteLocation::find($id)->delete();
                if($request->ajax()){
                    return response()->json(['message' => trans('admin.favourite_location_msgs.favourite_delete')]); 
                }else{
                    return back()->with('message', trans('admin.favourite_location_msgs.favourite_delete'));
                } 
            }else{
                return response()->json(['message' => trans('admin.favourite_location_msgs.favourite_exists')]);
            }
            
        }  catch (Exception $e) {
            if($request->ajax()){
                return response()->json(['message' => trans('admin.favourite_location_msgs.favourite_not_found')]); 
            }else{
                return back()->with('flash_error', trans('admin.favourite_location_msgs.favourite_not_found'));
            }
            
        }
    }
}
