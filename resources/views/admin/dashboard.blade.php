@extends('admin.layout.base')

@section('title', 'Dashboard ')

@section('styles')
	<link rel="stylesheet" href="{{asset('main/vendor/jvectormap/jquery-jvectormap-2.0.3.css')}}">
@endsection

@section('content')

<div class="content-area py-1">
<div class="container-fluid">
    <div class="row row-md">
    	@can('dashboard-menus')
		<div class="col-lg-3 col-md-6 col-xs-12">
			<div class="box box-block bg-white tile tile-1 mb-2">
				<div class="t-icon right"><span class="bg-danger"></span><i class="ti-rocket"></i></div>
				<div class="t-content">
					<h6 class="text-uppercase mb-1">@lang('admin.dashboard.Rides')</h6>
					<h1 class="mb-1">{{$rides->count()}}</h1>
					<span class="tag tag-danger mr-0-5">@if($cancel_rides == 0) 0.00 @else {{round($cancel_rides/$rides->count(),2)}}% @endif</span>
					<span class="text-muted font-90">% down from cancelled Request</span>
				</div>
			</div>
		</div>
		<div class="col-lg-3 col-md-6 col-xs-12">
			<div class="box box-block bg-white tile tile-1 mb-2">
				<div class="t-icon right"><span class="bg-success"></span><i class="ti-bar-chart"></i></div>
				<div class="t-content">
					<h6 class="text-uppercase mb-1">@lang('admin.dashboard.Revenue')</h6>
					<h1 class="mb-1">{{currency($revenue)}}</h1>
					<i class="fa fa-caret-up text-success mr-0-5"></i><span>from {{$rides->count()}} Rides</span>
				</div>
			</div>
		</div>
		<div class="col-lg-3 col-md-6 col-xs-12">
			<div class="box box-block bg-white tile tile-1 mb-2">
				<div class="t-icon right"><span class="bg-primary"></span><i class="ti-view-grid"></i></div>
				<div class="t-content">
					<h6 class="text-uppercase mb-1">@lang('admin.dashboard.service')</h6>
					<h1 class="mb-1">{{$service}}</h1>
				</div>
			</div>
		</div>
		<!-- <div class="col-lg-3 col-md-6 col-xs-12">
			<div class="box box-block bg-white tile tile-1 mb-2">
				<div class="t-icon right"><span class="bg-warning"></span><i class="ti-archive"></i></div>
				<div class="t-content">
					<h6 class="text-uppercase mb-1">@lang('admin.dashboard.total_rides')</h6>
					<h1 class="mb-1">{{$cancel_rides}}</h1>
					<i class="fa fa-caret-down text-danger mr-0-5"></i><span>for @if($cancel_rides == 0) 0.00 @else {{round($cancel_rides/$rides->count(),2)}}% @endif Rides</span>
				</div>
			</div>
		</div> -->
		<div class="col-lg-3 col-md-6 col-xs-12">
			<div class="box box-block bg-white tile tile-1 mb-2">
				<div class="t-icon right"><span class="bg-success"></span><i class="ti-bar-chart"></i></div>
				<div class="t-content">
					<h6 class="text-uppercase mb-1">@lang('admin.dashboard.scheduled')</h6>
					<h1 class="mb-1">{{$scheduled_rides}}</h1>
				</div>
			</div>
		</div>
		@endcan
	</div>
	<div class="row row-md">
		@can('dashboard-menus')
		<div class="col-lg-3 col-md-6 col-xs-12">
			<div class="box box-block bg-white tile tile-1 mb-2">
				<div class="t-icon right"><span class="bg-primary"></span><i class="ti-view-grid"></i></div>
				<div class="t-content">
					<h6 class="text-uppercase mb-1">@lang('admin.dashboard.cancel_count')</h6>
					<h1 class="mb-1">{{$user_cancelled}}</h1>
				</div>
			</div>
		</div>
		<div class="col-lg-3 col-md-6 col-xs-12">
			<div class="box box-block bg-white tile tile-1 mb-2">
				<div class="t-icon right"><span class="bg-danger"></span><i class="ti-bar-chart"></i></div>
				<div class="t-content">
					<h6 class="text-uppercase mb-1">@lang('admin.dashboard.provider_cancel_count')</h6>
					<h1 class="mb-1">{{$provider_cancelled}}</h1>
				</div>
			</div>
		</div>
		<div class="col-lg-3 col-md-6 col-xs-12">
			<div class="box box-block bg-white tile tile-1 mb-2">
				<div class="t-icon right"><span class="bg-success"></span><i class="ti-user"></i></div>
				<div class="t-content">
					<h6 class="text-uppercase mb-1">@lang('admin.dashboard.providers')</h6>
					<h1 class="mb-1">{{$provider}}</h1>
				</div>
			</div>
		</div>
		<div class="col-lg-3 col-md-6 col-xs-12">
			<div class="box box-block bg-white tile tile-1 mb-2">
				<div class="t-icon right"><span class="bg-warning"></span><i class="ti-rocket"></i></div>
				<div class="t-content">
					<h6 class="text-uppercase mb-1">@lang('admin.dashboard.fleets')</h6>
					<h1 class="mb-1">{{$fleet}}</h1>
				</div>
			</div>
		</div>
		@endcan
	</div>

	<div class="row row-md mb-2">
		@can('wallet-summary')
		<div class="col-md-4">
			<div class="box bg-white">
					<div class="box-block clearfix">
						<h5 class="float-xs-left">Wallet Summary</h5>
						<div class="float-xs-right">
							<!-- <button class="btn btn-link btn-sm text-muted" type="button"><i class="ti-close"></i></button> -->
						</div>
					</div>
					<table class="table mb-md-0">
						<tbody>
								@php($total=$wallet['admin'])
							<tr>
								<th scope="row">Admin Credit</th>
								<td class="text-success">{{currency($wallet['admin'])}}</td>
							</tr>
							<tr>
								<th scope="row">Provider Credit</th>
								@if($wallet['provider_credit'])
								@php($total=$total-$wallet['provider_credit'][0]['total_credit'])
									<td class="text-success">{{currency($wallet['provider_credit'][0]['total_credit'])}}</td>
								@else
									<td class="text-success">{{currency()}}</td>	
								@endif	
							</tr>

							<tr>
								<th scope="row">Provider Debit</th>
								@if($wallet['provider_debit'])
									
									<td class="text-danger">{{currency($wallet['provider_debit'][0]['total_debit'])}}</td>
								@else
									<td class="text-danger">{{currency()}}</td>	
								@endif
							</tr>

							<tr>
								<th scope="row">Fleet Credit</th>
								@if($wallet['fleet_credit'])
									@php($total=$total-($wallet['fleet_credit'][0]['total_credit']))
									<td class="text-success">{{currency($wallet['fleet_credit'][0]['total_credit'])}}</td>
								@else
									<td class="text-success">{{currency()}}</td>		
								@endif	
							</tr>
							<tr>
								<th scope="row">Fleet Debit</th>
								@if($wallet['fleet_debit'])								
									<td class="text-danger">{{currency($wallet['fleet_debit'][0]['total_debit'])}}</td>
								@else
									<td class="text-danger">{{currency()}}</td>		
								@endif	
							</tr>
							<tr>
								<th scope="row">Commission</th>
								<td class="text-success">{{currency($wallet['admin_commission'])}}</td>
							</tr>
							<tr>
								<th scope="row">Peak Commission</th>
								<td class="text-success">{{currency($wallet['peak_commission'])}}</td>
							</tr>
							<tr>
								<th scope="row">Waiting Commission</th>
								<td class="text-success">{{currency($wallet['waiting_commission'])}}</td>
							</tr>
							<tr>
								<th scope="row">Discount</th>
								<td class="text-danger">{{currency($wallet['admin_discount'])}}</td>
							</tr>
							<tr>
								@php($total=$total-($wallet['admin_tax']))
								<th scope="row">Tax Amount</th>
								<td class="text-success">{{currency($wallet['admin_tax'])}}</td>
							</tr>

							<tr>
								<th scope="row">Tips</th>
								<td class="text-danger">{{currency($wallet['tips'])}}</td>
							</tr>
							
							<tr>
								<th scope="row">Referrals</th>
								<td class="text-danger">{{currency($wallet['admin_referral'])}}</td>
							</tr>
							
							<tr>
								<th scope="row">Disputes</th>
								<td class="text-danger">{{currency($wallet['admin_dispute'])}}</td>
							</tr>
							
							<!-- <tr>
								<th scope="row text-right">Total</th>
								<td>{{currency($total)}}</td>
							</tr> -->
						</tbody>
					</table>
				</div>
			</div>
			@endcan
		@can('recent-rides')
		<div class="col-md-8">
				<div class="box bg-white">
					<div class="box-block clearfix">
						<h5 class="float-xs-left">@lang('admin.dashboard.Recent_Rides')</h5>
						<div class="float-xs-right">
							<button class="btn btn-link btn-sm text-muted" type="button"><i class="ti-close"></i></button>
						</div>
					</div>
					<table class="table mb-md-0">
						<tbody>
						<?php $diff = ['-success','-info','-warning','-danger']; ?>
						@foreach($rides as $index => $ride)
							<tr>
								<th scope="row">{{$index + 1}}</th>
								<td>{{$ride->user->first_name}} {{$ride->user->last_name}}</td>
								<td>
									@if($ride->status != "CANCELLED")
										<a class="text-primary" href="{{route('admin.requests.show',$ride->id)}}"><span class="underline">@lang('admin.dashboard.View_Ride_Details')</span></a>
									@else
										<span>@lang('admin.dashboard.No_Details_Found') </span>
									@endif									
								</td>
								<td>
									<span class="text-muted">{{$ride->created_at->diffForHumans()}}</span>
								</td>
								<td>
									@if($ride->status == "COMPLETED")
										<span class="tag tag-success">{{$ride->status}}</span>
									@elseif($ride->status == "CANCELLED")
										<span class="tag tag-danger">{{$ride->status}}</span>
									@else
										<span class="tag tag-info">{{$ride->status}}</span>
									@endif
								</td>
							</tr>
							<?php if($index==10) break; ?>
						@endforeach
							
						</tbody>
					</table>
				</div>
			</div>
			@endcan
		</div>

	</div>
</div>
@endsection

@section('scripts')    

<script type="text/javascript">

var _registration = null;
function registerServiceWorker() {
  return navigator.serviceWorker.register("{{ asset('js/service-worker.js') }}")
  .then(function(registration) {
    console.log('Service worker successfully registered.');
    _registration = registration;
    return registration;
  })
  .catch(function(err) {
    console.error('Unable to register service worker.', err);
  });
}

function askPermission() {
  return new Promise(function(resolve, reject) {
    const permissionResult = Notification.requestPermission(function(result) {
      resolve(result);
    });

    if (permissionResult) {
      permissionResult.then(resolve, reject);
    }
  })
  .then(function(permissionResult) {
    if (permissionResult !== 'granted') {
      throw new Error('We weren\'t granted permission.');
    }
    else{
      subscribeUserToPush();
    }
  });
}

function urlBase64ToUint8Array(base64String) {
  const padding = '='.repeat((4 - base64String.length % 4) % 4);
  const base64 = (base64String + padding)
    .replace(/\-/g, '+')
    .replace(/_/g, '/');

  const rawData = window.atob(base64);
  const outputArray = new Uint8Array(rawData.length);

  for (let i = 0; i < rawData.length; ++i) {
    outputArray[i] = rawData.charCodeAt(i);
  }
  return outputArray;
}

function getSWRegistration(){
  var promise = new Promise(function(resolve, reject) {
  // do a thing, possibly async, then…

  if (_registration != null) {
    resolve(_registration);
  }
  else {
    reject(Error("It broke"));
  }
  });
  return promise;
}

function subscribeUserToPush() {
  getSWRegistration()
  .then(function(registration) {
    console.log(registration);
    const subscribeOptions = {
      userVisibleOnly: true,
      applicationServerKey: urlBase64ToUint8Array(
        "{{env('VAPID_PUBLIC_KEY')}}"
      )
    };
    return registration.pushManager.subscribe(subscribeOptions);
  })
  .then(function(pushSubscription) {
    console.log('Received PushSubscription: ', JSON.stringify(pushSubscription));
    sendSubscriptionToBackEnd(pushSubscription);
    return pushSubscription;
  });
}

function sendSubscriptionToBackEnd(subscription) {
    $.ajax({
            url: "/save-subscription/{{Auth::user()->id}}/admin",
            headers: {'Content-Type': 'application/json'},
            type: 'post',
            data: JSON.stringify(subscription),
            success:function(data, textStatus, jqXHR) {
                console.log(data);
            }
        });
}


  registerServiceWorker();

  askPermission();

</script>

@endsection