@extends('dispatcher.layout.base')

@section('title', 'Update Profile ')

@section('content')

<div class="content-area py-1">
    <div class="container-fluid">
    	<div class="box box-block bg-white">

			<h5 style="margin-bottom: 2em;">@lang('admin.account.update_profile')</h5>

            <form class="form-horizontal" action="{{route('dispatcher.profile.update')}}" method="POST" enctype="multipart/form-data" role="form">
            	{{csrf_field()}}

				<div class="form-group row">
					<label for="name" class="col-xs-2 col-form-label">@lang('admin.name')</label>
					<div class="col-xs-10">
						<input class="form-control" type="text" value="{{ Auth::guard('dispatcher')->user()->name }}" name="name" required id="name" placeholder=" @lang('admin.name')">
					</div>
				</div>

				<div class="form-group row">
					<label for="email" class="col-xs-2 col-form-label">@lang('admin.email')</label>
					<div class="col-xs-10">
						<input class="form-control" type="email" required name="email" value="{{ isset(Auth::guard('dispatcher')->user()->email) ? Auth::guard('dispatcher')->user()->email : '' }}" id="email" placeholder="@lang('admin.email')">
					</div>
				</div>

				<div class="form-group row">
					<label for="mobile" class="col-xs-2 col-form-label">@lang('admin.mobile')</label>
					<div class="col-xs-10">
						<input class="form-control" type="text" value="{{ Auth::guard('dispatcher')->user()->mobile }}" name="mobile" required id="mobile" placeholder=" @lang('admin.mobile')">
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
