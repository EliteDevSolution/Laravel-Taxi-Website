<?php

namespace App\Http\Controllers\Resource;

use App\Notifications;
use App\Helpers\Helper;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Http\Controllers\Controller;
use Carbon\Carbon;

class NotificationResource extends Controller
{
    
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('demo', ['only' => ['store' ,'update', 'destroy']]);
        $this->middleware('permission:notification-list', ['only' => ['index']]);
        $this->middleware('permission:notification-create', ['only' => ['create','store']]);
        $this->middleware('permission:notification-edit', ['only' => ['edit','update']]);
        $this->middleware('permission:notification-delete', ['only' => ['destroy']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $notification = Notifications::orderBy('created_at' , 'desc')->get();
        return view('admin.notification.index', compact('notification'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.notification.create');
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
            'notify_type' => 'required',           
            'image' => 'required|mimes:jpeg,jpg,png|max:5242880',           
        ]);

        try{

            $Notifications = new Notifications;
            $Notifications->notify_type = $request->notify_type;

            if($request->hasFile('image')) {
                $Notifications->image = Helper::upload_picture($request->image);
            }

            $Notifications->description = $request->description;                    
            $Notifications->expiry_date = date('Y-m-d H:i:s', strtotime($request->expiry_date));
            $Notifications->status = $request->status;                    
            $Notifications->save();

            return back()->with('flash_success', trans('admin.notification_msgs.saved'));

        } 

        catch (ModelNotFoundException $e) {
            return back()->with('flash_error', trans('admin.notification_msgs.not_found'));
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Reason  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
            return Notifications::findOrFail($id);
        } catch (ModelNotFoundException $e) {
            return $e;
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Reason  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        try {
            $notification = Notifications::findOrFail($id);
            return view('admin.notification.edit',compact('notification'));
        } catch (ModelNotFoundException $e) {
            return $e;
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Reason  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'notify_type' => 'required',
            'image' => 'mimes:jpeg,jpg,png|max:5242880', 
        ]);

        try {

            $Notifications = Notifications::findOrFail($id);

            $Notifications->notify_type = $request->notify_type;            

            if($request->hasFile('image')) {
                if($Notifications->image) {
                    Helper::delete_picture($Notifications->image);
                }
                $Notifications->image = Helper::upload_picture($request->image);
            }

            $Notifications->description = $request->description;                    
            $Notifications->expiry_date = date('Y-m-d H:i:s', strtotime($request->expiry_date));
            $Notifications->status = $request->status;                    
            $Notifications->save();

            return redirect()->route('admin.notification.index')->with('flash_success', trans('admin.notification_msgs.update'));    
        } 

        catch (ModelNotFoundException $e) {
            return back()->with('flash_error', trans('admin.notification_msgs.not_found'));
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Reason  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            Notifications::find($id)->delete();
            return back()->with('flash_success', trans('admin.notification_msgs.delete'));
        } 
        catch (ModelNotFoundException $e) {
            return back()->with('flash_error', trans('admin.notification_msgs.not_found'));
        }
    }

    /**
       get notifications for respcted types     
    */
    public function getnotify($type)
    {
        if($type=='user'){
            $search_type='provider';
        }
        else{
            $search_type='user';
        }

        try {

            $notification = Notifications::where('notify_type', '!=', $search_type)->where('status', 'active')->orderBy('created_at' , 'desc')->get();
            return response()->json($notification);
        } 
        catch (Exception $e) {
            return response()->json(['error' => trans('api.something_went_wrong')], 500);
        }
    }
}
