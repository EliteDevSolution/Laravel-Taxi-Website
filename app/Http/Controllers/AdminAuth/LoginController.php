<?php

namespace App\Http\Controllers\AdminAuth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;
use Hesto\MultiAuth\Traits\LogsoutGuard;
use Illuminate\Support\Facades\Lang;
use Session;
use Redirect;
class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers, LogsoutGuard {
        LogsoutGuard::logout insteadof AuthenticatesUsers;

    }

    /**
     * Where to redirect users after login / registration.
     *
     * @var string
     */
    //public $redirectTo = '/admin/dashboard';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //Session::flush();
        $this->middleware('admin.guest', ['except' => 'logout']);
        //parent::__construct();
    }

    /**
     * Show the application's login form.
     *
     * @return \Illuminate\Http\Response
     */
    public function showLoginForm()
    {
        return view('admin.auth.login');
    }

    /**
     * Get the failed login response instance.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */

    protected function sendFailedLoginResponse(Request $request)
    {
        return redirect()->back()
            ->withInput($request->only($this->username(), 'remember'))
            ->withErrors([
                $this->username() => Lang::get('auth.failed'),'login_type'=> $request->login_type
            ]);
    }

    /**
     * Get the guard to be used during authentication.
     *
     * @return \Illuminate\Contracts\Auth\StatefulGuard
     */
    protected function guard()
    {
        return Auth::guard('admin');
    }


    public function redirectTo(){
        
        // User role
        $role = Auth::guard('admin')->user()->getRoleNames()->toArray(); 
        
        // Check user role
        switch ($role[0]) {
            case 'ADMIN':
                    return 'admin/dashboard';
                break;
            case 'ACCOUNT':
                    return 'admin/dashboard';
                break;
            case 'DISPATCHER':
                    return 'admin/dispatcher';
                break;
            case 'DISPUTE':
                    return 'admin/dispute';
                break;                         
            default:
                    return 'admin/dashboard'; 
                break;
        }
    }

    public function logout(){
        Auth::logout();
        Session::flush();
        return Redirect::to('/admin');
    }
}
