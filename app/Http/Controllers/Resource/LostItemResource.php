<?php

namespace App\Http\Controllers\Resource;

use App\UserRequestLostItem;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Http\Controllers\Controller;
use Carbon\Carbon;

class LostItemResource extends Controller
{
    
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('demo', ['only' => ['store' ,'update', 'destroy']]);
        $this->middleware('permission:lost-item-list', ['only' => ['index']]);
        $this->middleware('permission:lost-item-create', ['only' => ['create','store']]);
        $this->middleware('permission:lost-item-edit', ['only' => ['edit','update']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $lostitem = UserRequestLostItem::orderBy('created_at' , 'desc')->get();
        return view('admin.lostitem.index', compact('lostitem'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.lostitem.create');
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
            'request_id' => 'required',           
            'user_id' => 'required',           
            'lost_item_name' => 'required',           
        ]);

        try{

            $LostItem = new UserRequestLostItem;
            $LostItem->request_id = $request->request_id;
            $LostItem->user_id = $request->user_id;                    
            $LostItem->lost_item_name = $request->lost_item_name;

            if($request->has('comments')) {
                $LostItem->comments = $request->comments;
            }    

            if($request->has('status')) {    
                $LostItem->status = $request->status;
            }    

            if($request->has('is_admin')) {                   
                $LostItem->is_admin = $request->is_admin;
                $LostItem->comments_by = 'admin';
            }
            
            if($request->has('comments_by')) {
                $LostItem->comments_by = $request->comments_by;
            }

            $LostItem->save();

            if($request->ajax()){
                return response()->json(['message' => trans('admin.lostitem_msgs.saved')]);
            }else{
                return back()->with('flash_success', trans('admin.lostitem_msgs.saved'));
            }

        } 

        catch (ModelNotFoundException $e) {
            return back()->with('flash_error', trans('admin.lostitem_msgs.not_found'));
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
            $lostitem = UserRequestLostItem::findOrFail($id);
            return view('admin.lostitem.edit',compact('lostitem'));
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
            'comments' => 'required',
        ]);

        try {

            $LostItem = UserRequestLostItem::findOrFail($id);

            if($request->has('comments')) {
                $LostItem->comments = $request->comments;
            }
            
            if($request->has('comments_by')) {
                if($request->ajax()){
                    $LostItem->comments_by = 'user';
                } else {
                    $LostItem->comments_by = 'admin';
                }
                
            }    

            if($request->has('status')) {    
                $LostItem->status = $request->status;
            }    

            $LostItem->save();

            if($request->ajax()){
                return response()->json(['message' => trans('admin.lostitem_msgs.update')]);
            } else {
                return redirect()->route('admin.lostitem.index')->with('flash_success', trans('admin.lostitem_msgs.update')); 
            }

               
        } 

        catch (ModelNotFoundException $e) {
            return back()->with('flash_error', trans('admin.lostitem_msgs.not_found'));
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
            UserRequestLostItem::find($id)->delete();
            return back()->with('flash_success', trans('admin.lostitem_msgs.delete'));
        } 
        catch (ModelNotFoundException $e) {
            return back()->with('flash_error', trans('admin.lostitem_msgs.not_found'));
        }
    }
       /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function saveLostItem(Request $request)
    {

        $this->validate($request, [
            'request_id' => 'required',           
            'user_id' => 'required',           
            'lost_item_name' => 'required',           
        ]);

        try{

            $LostItem = new UserRequestLostItem;
            $LostItem->request_id = $request->request_id;
            $LostItem->user_id = $request->user_id;                    
            $LostItem->lost_item_name = $request->lost_item_name;

            if($request->has('comments')) {
                $LostItem->comments = $request->comments;
            }    

            if($request->has('status')) {    
                $LostItem->status = $request->status;
            }    

            if($request->has('is_admin')) {                   
                $LostItem->is_admin = $request->is_admin;
                $LostItem->comments_by = 'admin';
            }
            
            if($request->has('comments_by')) {
                $LostItem->comments_by = $request->comments_by;
            }

            $LostItem->save();

            if($request->ajax()){
                return response()->json(['message' => trans('admin.lostitem_msgs.saved')]);
            }else{
                return back()->with('flash_success', trans('admin.lostitem_msgs.saved'));
            }

        } 

        catch (ModelNotFoundException $e) {
            return back()->with('flash_error', trans('admin.lostitem_msgs.not_found'));
        }
    }
}
