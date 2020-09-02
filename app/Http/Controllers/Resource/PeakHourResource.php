<?php

namespace App\Http\Controllers\Resource;

use App\PeakHour;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Http\Controllers\Controller;
use Carbon\Carbon;

class PeakHourResource extends Controller
{
    
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('demo', ['only' => ['store' ,'update', 'destroy']]);
        $this->middleware('permission:peak-hour-list', ['only' => ['index']]);
        $this->middleware('permission:peak-hour-create', ['only' => ['create','store']]);
        $this->middleware('permission:peak-hour-edit', ['only' => ['edit','update']]);
        $this->middleware('permission:peak-hour-delete', ['only' => ['destroy']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $peakhour = PeakHour::orderBy('created_at' , 'desc')->get();
        return view('admin.peakhour.index', compact('peakhour'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.peakhour.create');
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
            'start_time' => 'required',
            'end_time' => 'required',           
        ]);

        try{
            //PeakHour::create($request->all());
            $PeakHour = new PeakHour;
            $PeakHour->start_time = date('H:i:s', strtotime($request->start_time));
            $PeakHour->end_time = date('H:i:s', strtotime($request->end_time));                    
            $PeakHour->save();

            return back()->with('flash_success', trans('admin.peakhour_msgs.peakhour_saved'));

        } 

        catch (ModelNotFoundException $e) {
            return back()->with('flash_error', trans('admin.peakhour_msgs.peakhour_not_found'));
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
            return PeakHour::findOrFail($id);
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
            $peakhour = PeakHour::findOrFail($id);
            return view('admin.peakhour.edit',compact('peakhour'));
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
            'start_time' => 'required',
            'end_time' => 'required',  
        ]);

        try {

            $PeakHour = PeakHour::findOrFail($id);

            $PeakHour->start_time = date('H:i:s', strtotime($request->start_time));
            $PeakHour->end_time = date('H:i:s', strtotime($request->end_time));                    
            $PeakHour->save();

            return redirect()->route('admin.peakhour.index')->with('flash_success', trans('admin.peakhour_msgs.peakhour_update'));    
        } 

        catch (ModelNotFoundException $e) {
            return back()->with('flash_error', trans('admin.peakhour_msgs.peakhour_not_found'));
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
            PeakHour::find($id)->delete();
            return back()->with('flash_success', trans('admin.peakhour_msgs.peakhour_delete'));
        } 
        catch (ModelNotFoundException $e) {
            return back()->with('flash_error', trans('admin.peakhour_msgs.peakhour_not_found'));
        }
    }
}
