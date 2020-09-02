@extends('fleet.layout.base')

@section('title', 'Update Profile ')

@section('content')

<div class="content-area py-1">
    <div class="container-fluid">
    	<div class="box box-block bg-white">

			<h5 style="margin-bottom: 2em;">@lang('admin.account.update_profile')</h5>

            <form class="form-horizontal" action="{{route('fleet.profile.update')}}" method="POST" enctype="multipart/form-data" role="form">
            	{{csrf_field()}}

				<div class="form-group row">
					<label for="name" class="col-xs-2 col-form-label">@lang('admin.name')</label>
					<div class="col-xs-10">
						<input class="form-control" type="text" value="{{ Auth::guard('fleet')->user()->name }}" name="name" required id="name" placeholder="@lang('admin.name')">
					</div>
				</div>

				<div class="form-group row">
					<label for="email" class="col-xs-2 col-form-label">@lang('admin.email')</label>
					<div class="col-xs-10">
						<input class="form-control" type="email" required name="email" value="{{ isset(Auth::guard('fleet')->user()->email) ? Auth::guard('fleet')->user()->email : '' }}" id="email" placeholder="@lang('admin.email')" readonly>
					</div>
				</div>

				<div class="form-group row">
					<label for="company" class="col-xs-2 col-form-label">@lang('admin.company')</label>
					<div class="col-xs-10">
						<input class="form-control" type="text" required name="company" value="{{ isset(Auth::guard('fleet')->user()->company) ? Auth::guard('fleet')->user()->company : '' }}" id="company" placeholder="@lang('admin.company')">
					</div>
				</div>

				<div class="form-group row">
					<label for="mobile" class="col-xs-2 col-form-label">@lang('admin.mobile')</label>
					<div class="col-xs-10">
						<input class="form-control" type="text" required name="mobile" value="{{ isset(Auth::guard('fleet')->user()->mobile) ? Auth::guard('fleet')->user()->mobile : '' }}" id="mobile" placeholder="@lang('admin.mobile')">
					</div>
				</div>

				<div class="form-group row">
                    <label class="col-xs-2 col-form-label">@lang('user.profile.language')</label>
                    <div class="col-xs-10">
                    	@php($language=get_all_language())
	                    <select class="form-control" name="language" id="language">
	                        @foreach($language as $lkey=>$lang)
	                        	<option value="{{$lkey}}" @if(Auth::user()->language==$lkey) selected @endif>{{$lang}}</option>
	                        @endforeach
	                    </select>
	                </div>    
                </div>

				<div class="form-group row">
					<label for="logo" class="col-xs-2 col-form-label">@lang('admin.logo')</label>
					<div class="col-xs-10">
						@if(isset(Auth::guard('fleet')->user()->logo))
	                    	<img style="height: 90px; margin-bottom: 15px; border-radius:2em;" src="{{img(Auth::guard('fleet')->user()->logo)}}">
	                    @endif
						<input type="file" accept="image/*" name="logo" class=" dropify form-control-file" aria-describedby="fileHelp">
					</div>
				</div>

				<div class="form-group row">
					<label for="zipcode" class="col-xs-2 col-form-label"></label>
					<div class="col-xs-10">
						<button type="submit" class="btn btn-primary">@lang('admin.account.update_profile')</button>
					</div>
				</div>
			</form>
		</div>
    </div>
</div>

@endsection
