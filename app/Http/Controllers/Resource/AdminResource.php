<?php

namespace App\Http\Controllers\Resource;

use App\Admin;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Spatie\Permission\Models\Role;

class AdminResource extends Controller
{
    public function __construct()
    {
        $this->middleware('demo', ['only' => ['store', 'update','destroy']]);
        $this->middleware('permission:sub-admin-list', ['only' => ['index']]);
        $this->middleware('permission:sub-admin-create', ['only' => ['create','store']]);
        $this->middleware('permission:sub-admin-edit', ['only' => ['edit','update']]);
        $this->middleware('permission:sub-admin-delete', ['only' => ['destroy']]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = Admin::where('id', '!=', \Auth::id())->orderBy('id' , 'asc')->whereHas("roles", function($q){ $q->where("id",'>',"5"); })->get();
        return view('admin.sub-admin.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $roles = Role::get();
        if(count($roles)<6) {
            return back()->with('flash_error', trans('admin.admins.role_not_found'));
        }else{
        return view('admin.sub-admin.create', compact('roles'));
        }
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
            'name' => 'required|max:255',
            'email' => 'required|unique:admins,email|email|max:255',
            'mobile' => 'digits_between:6,13',
            'picture' => 'mimes:jpeg,jpg,bmp,png|max:5242880',
            'password' => 'required|min:6|confirmed',
            'roles' => 'required',
        ]);
        try{

            $users = $request->all();

            $users['password'] = bcrypt($request->password);
            if($request->hasFile('picture')) {
                $users['picture'] = $request->picture->store('admin/profile');
            }

            $users = Admin::create($users);
            $users->assignRole($request->input('roles'));

            return redirect()->route('admin.sub-admins.index')->with('flash_success', trans('admin.admins.user_saved'));

        } 

        catch (Exception $e) {
            return back()->with('flash_error', trans('admin.admins.user_not_found'));
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
            $user = Admin::findOrFail($id);
            return view('admin.sub-admin.user-details', compact('user'));
        } catch (ModelNotFoundException $e) {
            return $e;
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
            $user = Admin::findOrFail($id);
            $roles = Role::get();
            $userRole = $user->roles->pluck('id','id')->all();

            return view('admin.sub-admin.edit',compact('user', 'roles', 'userRole'));
        } catch (ModelNotFoundException $e) {
            return $e;
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
            'first_name' => 'required|max:255',
            'last_name' => 'required|max:255',
            'mobile' => 'digits_between:6,13',
            'picture' => 'mimes:jpeg,jpg,bmp,png|max:5242880',
        ]);

        try {

            $user = Admin::findOrFail($id);

            if($request->hasFile('picture')) {
                Storage::delete($user->picture);
                $user->picture = $request->picture->store('admin/profile');
            }

            $user->name = $request->name;
            $user->email = $request->email;
            $user->mobile = $request->mobile;
            $user->save();

            return redirect()->route('admin.sub-admins.index')->with('flash_success', trans('admin.admins.user_update'));    
        } 

        catch (ModelNotFoundException $e) {
            return back()->with('flash_error', trans('admin.admins.user_not_found'));
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            Admin::find($id)->delete();
            return back()->with('message', trans('admin.admins.user_delete'));
        } 
        catch (Exception $e) {
            return back()->with('flash_error', trans('admin.admins.user_not_found'));
        }
    }
}
