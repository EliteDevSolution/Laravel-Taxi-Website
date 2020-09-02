@extends('admin.layout.base')

@section('title', 'Update Notification ')

@section('content')
<link rel="stylesheet" type="text/css" href="{{asset('asset/css/bootstrap-datetimepicker.min.css')}}">	
<div class="content-area py-1">
    <div class="container-fluid">
    	<div class="box box-block bg-white">
    	    <a href="{{ route('admin.notification.index') }}" class="btn btn-default pull-right"><i class="fa fa-angle-left"></i> @lang('admin.back')</a>

			<h5 style="margin-bottom: 2em;">@lang('admin.notification.update')</h5>

            <form class="form-horizontal" action="{{route('admin.notification.update', $notification->id )}}" method="POST" enctype="multipart/form-data" role="form">
            	{{csrf_field()}}
            	<input type="hidden" name="_method" value="PATCH">				
				
				<div class="form-group row">
					<label for="notify_type" class="col-xs-2 col-form-label">@lang('admin.notification.notify_type')</label>
					<div class="col-xs-10">
						<select name="notify_type" class="form-control">
							<option value="all">All</option>
							<option value="user">User</option>
							<option value="provider">Provider</option>
						</select>
					</div>
				</div>

				<div class="form-group row">
					<label for="picture" class="col-xs-2 col-form-label">@lang('admin.notification.notify_image')</label>
					<div class="col-xs-10">
						@if(isset($notification->image))
                        	<img style="height: 90px; margin-bottom: 15px; border-radius:2em;" src="{{ $notification->image }}">
                        @endif
						<input type="file" accept="image/*" name="image" class="dropify form-control-file" id="picture" aria-describedby="fileHelp">
					</div>
				</div>

				<div class="form-group row">
					<label for="notify_desc" class="col-xs-2 col-form-label">@lang('admin.notification.notify_desc')</label>
					<div class="col-xs-10">
						<input class="form-control" autocomplete="off"  type="text" value="{{ $notification->description }}" name="description" required id="description" placeholder="@lang('admin.notification.notify_desc')">
					</div>
				</div>

				<div class="form-group row">
					<label for="expiry_date" class="col-xs-2 col-form-label">@lang('admin.notification.notify_expiry')</label>
					<div class="col-xs-10">
						<input class="form-control datetimepicker" autocomplete="off"  type="text" value="{{$notification->expiry_date}}" name="expiry_date" required id="expiry_date" placeholder="@lang('admin.notification.notify_expiry')">
					</div>
				</div>

				<div class="form-group row">
					<label for="notify_status" class="col-xs-2 col-form-label">@lang('admin.notification.notify_status')</label>
					<div class="col-xs-10">
						<select name="status" class="form-control">
							<option value="active">Active</option>
							<option value="inactive">Inactive</option>
						</select>
					</div>
				</div>

				<div class="form-group row">
					<label for="" class="col-xs-2 col-form-label"></label>
					<div class="col-xs-10">
						<button type="submit" class="btn btn-primary">@lang('admin.notification.update')</button>
						<a href="{{route('admin.notification.index')}}" class="btn btn-default">@lang('admin.cancel')</a>
					</div>
				</div>
			</form>
		</div>
    </div>
</div>

@endsection

@section('scripts')
	<script type="text/javascript" src="{{asset('asset/js/bootstrap-datetimepicker.min.js')}}"></script>
	<script type="text/javascript" src="{{asset('asset/js/moment-with-locales.min.js')}}"></script>
	<script type="text/javascript">
		$(document).ready(function () {
		  //your code here
		 $(function() {
		        $('.datetimepicker').datetimepicker({defaultDate: moment(),minDate: moment(),format: 'YYYY-MM-DD HH:mm'});
		    });
		});
       
    </script>
@endsection
