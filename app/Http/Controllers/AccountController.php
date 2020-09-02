<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Helpers\Helper;

use Auth;
use Setting;
use Exception;
use \Carbon\Carbon;

use App\User;
use App\Fleet;
use App\Account;
use App\Provider;
use App\UserPayment;
use App\ServiceType;
use App\UserRequests;
use App\ProviderService;
use App\UserRequestRating;
use App\UserRequestPayment;

class AccountController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('account');
        $this->middleware('demo', ['only' => ['profile_update', 'password_update']]);
        $this->perpage = config('constants.per_page', '10');
    }


    /**
     * Dashboard.
     *
     * @param  \App\Provider  $provider
     * @return \Illuminate\Http\Response
     */
    public function dashboard()
    {
        try{

            $rides = UserRequests::has('user')->orderBy('id','desc')->get();
            $cancel_rides = UserRequests::where('status','CANCELLED');
            $scheduled_rides = UserRequests::where('status','SCHEDULED')->count();
            $user_cancelled = $cancel_rides->where('cancelled_by','USER')->count();
            $provider_cancelled = $cancel_rides->where('cancelled_by','PROVIDER')->count();
            $cancel_rides = $cancel_rides->count();
            $service = ServiceType::count();
            $fleet = Fleet::count();
            $revenue = UserRequestPayment::sum('total');
            $providers = Provider::take(10)->orderBy('rating','desc')->get();

            return view('account.dashboard',compact('providers','fleet','scheduled_rides','service','rides','user_cancelled','provider_cancelled','cancel_rides','revenue'));
        }
        catch(Exception $e){
            return redirect()->route('account.user.index')->with('flash_error', trans('admin.something_wrong_dashboard'));
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Provider  $provider
     * @return \Illuminate\Http\Response
     */
    public function profile()
    {
        return view('account.account.profile');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Provider  $provider
     * @return \Illuminate\Http\Response
     */
    public function profile_update(Request $request)
    {

        $this->validate($request,[
            'name' => 'required|max:255',
            'mobile' => 'required|digits_between:6,13',
        ]);

        try{
            $account = Auth::guard('account')->user();
            $account->name = $request->name;
            $account->mobile = $request->mobile;
            $account->language = $request->language;
            $account->save();

            return redirect()->back()->with('flash_success', trans('admin.profile_update'));
        }

        catch (Exception $e) {
             return back()->with('flash_error', trans('admin.something_wrong'));
        }
        
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Provider  $provider
     * @return \Illuminate\Http\Response
     */
    public function password()
    {
        return view('account.account.change-password');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Provider  $provider
     * @return \Illuminate\Http\Response
     */
    public function password_update(Request $request)
    {

        $this->validate($request,[
            'old_password' => 'required',
            'password' => 'required|min:6|confirmed',
        ]);

        try {

           $Account = Account::find(Auth::guard('account')->user()->id);

            if(password_verify($request->old_password, $Account->password))
            {
                $Account->password = bcrypt($request->password);
                $Account->save();

                return redirect()->back()->with('flash_success', trans('admin.password_update'));
            }
        } catch (Exception $e) {
             return back()->with('flash_error', trans('admin.something_wrong'));
        }
    }

    /**
     * account statements.
     *
     * @param  \App\Provider  $provider
     * @return \Illuminate\Http\Response
     */
    public function statement($type = '', $request = null){

        try{
            
            $page = trans('admin.include.overall_ride_statments');
            $listname = trans('admin.include.overall_ride_earnings');

            if($type == 'individual'){
                $page = trans('admin.include.provider_statement');
                $listname = trans('admin.include.provider_earnings');
            }elseif($type == 'today'){
                $page = trans('admin.include.today_statement').' - '. date('d M Y');
                $listname = trans('admin.include.today_earnings');
            }elseif($type == 'monthly'){
                $page = trans('admin.include.monthly_statement').' - '. date('F');
                $listname = trans('admin.include.monthly_earnings');
            }elseif($type == 'yearly'){
                $page = trans('admin.include.yearly_statement').' - '. date('Y');
                $listname = trans('admin.include.yearly_earnings');
            }elseif($type == 'range'){
                $page = trans('admin.include.statement_from').' '.Carbon::createFromFormat('Y-m-d', $request->from_date)->format('d M Y').'  '.trans('admin.include.statement_to').' '.Carbon::createFromFormat('Y-m-d', $request->to_date)->format('d M Y');
            }


            $rides = UserRequests::with('payment')->orderBy('id','desc');
            $cancel_rides = UserRequests::where('status','CANCELLED');
            $revenue = UserRequestPayment::select(\DB::raw(
                           'SUM(ROUND(fixed) + ROUND(distance)) as overall, SUM(ROUND(commision)) as commission' 
                       ));

            if($type == 'today'){

                $rides->where('created_at', '>=', Carbon::today());
                $cancel_rides->where('created_at', '>=', Carbon::today());
                $revenue->where('created_at', '>=', Carbon::today());

            }elseif($type == 'monthly'){

                $rides->where('created_at', '>=', Carbon::now()->month);
                $cancel_rides->where('created_at', '>=', Carbon::now()->month);
                $revenue->where('created_at', '>=', Carbon::now()->month);

            }elseif($type == 'yearly'){

                $rides->where('created_at', '>=', Carbon::now()->year);
                $cancel_rides->where('created_at', '>=', Carbon::now()->year);
                $revenue->where('created_at', '>=', Carbon::now()->year);

            }elseif ($type == 'range') {

                if($request->from_date == $request->to_date) {
                    $rides->whereDate('created_at', date('Y-m-d', strtotime($request->from_date)));
                    $cancel_rides->whereDate('created_at', date('Y-m-d', strtotime($request->from_date)));
                    $revenue->whereDate('created_at', date('Y-m-d', strtotime($request->from_date)));
                } else {
                    $rides->whereBetween('created_at',[Carbon::createFromFormat('Y-m-d', $request->from_date),Carbon::createFromFormat('Y-m-d', $request->to_date)]);
                    $cancel_rides->whereBetween('created_at',[Carbon::createFromFormat('Y-m-d', $request->from_date),Carbon::createFromFormat('Y-m-d', $request->to_date)]);
                    $revenue->whereBetween('created_at',[Carbon::createFromFormat('Y-m-d', $request->from_date),Carbon::createFromFormat('Y-m-d', $request->to_date)]);
                }
            }

            
            $rides = $rides->paginate($this->perpage);
            
            if ($type == 'range'){
                $path='range?from_date='.$request->from_date.'&to_date='.$request->to_date;
                $rides->setPath($path);
            } 
            $cancel_rides = $cancel_rides->count();
            $revenue = $revenue->get();

            $pagination=(new Helper)->formatPagination($rides);

            $dates['yesterday'] = Carbon::yesterday()->format('Y-m-d');
            $dates['today'] = Carbon::today()->format('Y-m-d');
            $dates['pre_week_start'] = Carbon::today()->subWeek()->format('Y-m-d');
            $dates['pre_week_end'] = Carbon::parse('last sunday of this month')->format('Y-m-d');
            $dates['cur_week_start'] = Carbon::today()->startOfWeek()->format('Y-m-d');
            $dates['cur_week_end'] = Carbon::today()->endOfWeek()->format('Y-m-d');
            $dates['pre_month_start'] = Carbon::parse('first day of last month')->format('Y-m-d');
            $dates['pre_month_end'] = Carbon::parse('last day of last month')->format('Y-m-d');
            $dates['cur_month_start'] = Carbon::parse('first day of this month')->format('Y-m-d');
            $dates['cur_month_end'] = Carbon::parse('last day of this month')->format('Y-m-d');
            $dates['pre_year_start'] = Carbon::parse('first day of last year')->format('Y-m-d');
            $dates['pre_year_end'] = Carbon::parse('last day of last year')->format('Y-m-d');
            $dates['cur_year_start'] = Carbon::parse('first day of this year')->format('Y-m-d');
            $dates['cur_year_end'] = Carbon::parse('last day of this year')->format('Y-m-d');
            $dates['nextWeek'] = Carbon::today()->addWeek()->format('Y-m-d');
                
            return view('account.providers.statement', compact('rides','cancel_rides','revenue','pagination','dates'))
                    ->with('page',$page)->with('listname',$listname);

        } catch (Exception $e) {
            echo "<pre>";
            print_r($e->getMessage());exit;
            return back()->with('flash_error', trans('admin.something_wrong'));
        }
    }


    /**
     * account statements today.
     *
     * @param  \App\Provider  $provider
     * @return \Illuminate\Http\Response
     */
    public function statement_today(){
        return $this->statement('today');
    }

    /**
     * account statements today.
     *
     * @param  \App\Provider  $provider
     * @return \Illuminate\Http\Response
     */
    public function statement_range(Request $request){
        return $this->statement('range', $request);
    }

    /**
     * account statements monthly.
     *
     * @param  \App\Provider  $provider
     * @return \Illuminate\Http\Response
     */
    public function statement_monthly(){
        return $this->statement('monthly');
    }

     /**
     * account statements monthly.
     *
     * @param  \App\Provider  $provider
     * @return \Illuminate\Http\Response
     */
    public function statement_yearly(){
        return $this->statement('yearly');
    }


    /**
     * account statements.
     *
     * @param  \App\Provider  $provider
     * @return \Illuminate\Http\Response
     */
    public function statement_provider(){

        try{

            $Providers = Provider::paginate($this->perpage);

            foreach($Providers as $index => $Provider){

                $Rides = UserRequests::where('provider_id',$Provider->id)
                            ->where('status','<>','CANCELLED')
                            ->get()->pluck('id');

                $Providers[$index]->rides_count = $Rides->count();

                $Providers[$index]->payment = UserRequestPayment::whereIn('request_id', $Rides)
                                ->select(\DB::raw(
                                   'SUM(ROUND(fixed) + ROUND(distance)) as overall, SUM(ROUND(commision)) as commission' 
                                ))->get();
            }

            $pagination=(new Helper)->formatPagination($Providers);

            return view('account.providers.provider-statement', compact('Providers','pagination'))->with('page','Providers Statement');

        } catch (Exception $e) {
            return back()->with('flash_error', trans('admin.something_wrong'));
        }
    }

    public function statement_user(){

        try{

            $Users = User::paginate($this->perpage);

            $pagination=(new Helper)->formatPagination($Users);

            foreach($Users as $index => $User){

                $Rides = UserRequests::where('user_id',$User->id)
                            ->where('status','<>','CANCELLED')
                            ->get()->pluck('id');

                $Users[$index]->rides_count = $Rides->count();

                $Users[$index]->payment = UserRequestPayment::whereIn('request_id', $Rides)
                                ->select(\DB::raw(
                                   'SUM(ROUND(total)) as overall' 
                                ))->get();
            }           

            return view('account.providers.user-statement', compact('Users','pagination'))->with('page','Users Statement');

        } catch (Exception $e) {
            return back()->with('flash_error','Something Went Wrong!');
        }
    }

    public function statement_fleet(){

        try{

            $Fleets = Fleet::paginate($this->perpage);

            $pagination=(new Helper)->formatPagination($Fleets);

            foreach($Fleets as $index => $Fleet){

                $Rides = UserRequestPayment::where('fleet_id',$Fleet->id)->get()->pluck('id');

                $Fleets[$index]->rides_count = $Rides->count();

                $Fleets[$index]->payment = UserRequestPayment::where('fleet_id', $Fleet->id)
                                ->select(\DB::raw(
                                   'SUM(ROUND(fleet)) as overall' 
                                ))->get();                                  
            }

            return view('account.providers.fleet-statement', compact('Fleets','pagination'))->with('page','Fleets Statement');

        } catch (Exception $e) {
            return back()->with('flash_error','Something Went Wrong!');
        }
    }
}
