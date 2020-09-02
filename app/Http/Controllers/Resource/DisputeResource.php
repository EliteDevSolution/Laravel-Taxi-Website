<?php

namespace App\Http\Controllers\Resource;

use App\Dispute;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Http\Controllers\Controller;
use App\UserRequestDispute;
use App\UserRequests;
use Carbon\Carbon;
use App\Notifications\WebPush;
use App\Http\Controllers\ProviderResources\TripController;

class DisputeResource extends Controller
{
    
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('demo', ['only' => ['store' ,'update', 'destroy']]);
        $this->middleware('permission:dispute-list', ['only' => ['index']]);
        $this->middleware('permission:dispute-create', ['only' => ['create','store']]);
        $this->middleware('permission:dispute-edit', ['only' => ['edit','update']]);
        $this->middleware('permission:dispute-delete', ['only' => ['destroy']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $dispute = Dispute::orderBy('created_at' , 'desc')->get();
        return view('admin.dispute.index', compact('dispute'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.dispute.create');
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
            'dispute_type' => 'required',
            'dispute_name' => 'required',           
        ]);

        try{
            //PeakHour::create($request->all());
            $Dispute = new Dispute;
            $Dispute->dispute_type = $request->dispute_type;
            $Dispute->dispute_name = $request->dispute_name;
            $Dispute->status = $request->dispute_status;                    
            $Dispute->save();

            return back()->with('flash_success', trans('admin.dispute_msgs.saved'));

        } 

        catch (ModelNotFoundException $e) {
            return back()->with('flash_error', trans('admin.dispute_msgs.not_found'));
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
            return Dispute::findOrFail($id);
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
            $dispute = Dispute::findOrFail($id);
            return view('admin.dispute.edit',compact('dispute'));
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
            'dispute_type' => 'required',
            'dispute_name' => 'required',  
        ]);

        try {

            $Dispute = Dispute::findOrFail($id);

            $Dispute->dispute_type = $request->dispute_type;
            $Dispute->dispute_name = $request->dispute_name;                    
            $Dispute->status = $request->dispute_status;                    
            $Dispute->save();

            return redirect()->route('admin.dispute.index')->with('flash_success', trans('admin.dispute_msgs.update'));    
        } 

        catch (ModelNotFoundException $e) {
            return back()->with('flash_error', trans('admin.dispute_msgs.not_found'));
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
            Dispute::find($id)->delete();
            return back()->with('flash_success', trans('admin.dispute_msgs.delete'));
        } 
        catch (ModelNotFoundException $e) {
            return back()->with('flash_error', trans('admin.dispute_msgs.not_found'));
        }
    }

    public function dispute_list(Request $request)
    {
        $this->validate($request, [
            'dispute_type' => 'required'         
        ]);

        $dispute = Dispute::select('dispute_name')->where('dispute_type' , $request->dispute_type)->where('status' , 'active')->get();

        return $dispute;
    }

    public function userdisputes()
    {

        $disputes = UserRequestDispute::with('request')->with('user')->with('provider')->orderBy('created_at' , 'desc')->get();
       
        return view('admin.userdispute.index', compact('disputes'));
    }

    public function userdisputecreate()
    {
        return view('admin.userdispute.create');
    }

    public function userdisputeedit($id)
    {

        try {
            $dispute = UserRequestDispute::with('request')->with('user')->with('provider')->findOrFail($id);
            return view('admin.userdispute.edit',compact('dispute'));
        } catch (ModelNotFoundException $e) {
            return $e;
        }
    }

    public function create_dispute(Request $request)
    {

        $this->validate($request, [
            'request_id' => 'required',
            'dispute_type' => 'required', 
            'dispute_name' => 'required',        
        ]);

        try{
            $Dispute = new UserRequestDispute();
            $Dispute->request_id = $request->request_id;
            $Dispute->dispute_type = $request->dispute_type;
            $Dispute->user_id = $request->user_id;
            $Dispute->provider_id = $request->provider_id;
            $Dispute->dispute_name = $request->dispute_name;
            if(!empty($request->dispute_other))
                $Dispute->dispute_name = $request->dispute_other;
            $Dispute->comments = $request->comments;                    
            $Dispute->save();

            UserRequests::where('id', $request->request_id)->update(['is_dispute' => 1]);

            $admin = \App\Admin::find(\Auth::user()->id);

            if($admin == null) {
                $admin = \App\Admin::whereNotNull('name')->first();
            }

            if($admin != null) {
                $admin->notify(new WebPush("Notifications", trans('admin.dispute.new_dispute'), url('/')));
            }
            

            if($request->ajax()){
                return response()->json(['message' => trans('admin.dispute_msgs.saved')]);
            }else{
                return back()->with('flash_success', trans('admin.dispute_msgs.saved'));
            }
        } 

        catch (ModelNotFoundException $e) {
            return back()->with('flash_error', trans('admin.dispute_msgs.not_found'));
        }
    }

    public function update_dispute(Request $request, $id)
    {

        $this->validate($request, [            
            'comments' => 'required', 
            'status' => 'required',        
        ]);

        try{

            $Dispute = UserRequestDispute::findOrFail($id);
            $Dispute->comments = $request->comments;                    
            $Dispute->refund_amount = $request->refund_amount;

            if(!empty($request->refund_amount)){
                //create the dispute transactions
                if($Dispute->dispute_type=='user'){
                    $type=1;
                    $request_by_id=$Dispute->user_id;
                }
                else{
                    $type=0;
                    $request_by_id=$Dispute->provider_id;
                }

                (new TripController)->disputeCreditDebit($request->refund_amount,$request_by_id,$type);
            }
            
            $Dispute->status = $request->status;                    
            $Dispute->save();

            if($request->ajax()){
                return response()->json(['message' => trans('admin.dispute_msgs.saved')]);
            }else{
                return back()->with('flash_success', trans('admin.dispute_msgs.saved'));
            }
        } 

        catch (ModelNotFoundException $e) {
            return back()->with('flash_error', trans('admin.dispute_msgs.not_found'));
        }
    }

}
