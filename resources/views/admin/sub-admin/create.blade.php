@extends('admin.layout.base')

@section('title', 'Add Admin ')

@section('content')

<div class="content-area py-1">
    <div class="container-fluid">
        <div class="box box-block bg-white">
            <a href="{{ route('admin.sub-admins.index') }}" class="btn btn-default pull-right"><i class="fa fa-angle-left"></i> @lang('admin.back')</a>

            <h5 style="margin-bottom: 2em;">@lang('admin.admins.Add_User')</h5>

            <form class="form-horizontal" action="{{route('admin.sub-admins.store')}}" method="POST" role="form">
                {{csrf_field()}}
                <div class="form-group row">
                    <label for="name" class="col-xs-12 col-form-label">@lang('admin.name')</label>
                    <div class="col-xs-10">
                        <input class="form-control" type="text" value="{{ old('name') }}" name="name" required id="name" placeholder="@lang('admin.name')">
                    </div>
                </div>

                <div class="form-group row">
                    <label for="name" class="col-xs-12 col-form-label">@lang('admin.email')</label>
                    <div class="col-xs-10">
                        <input class="form-control" type="text" value="{{ old('email') }}" name="email" required id="name" placeholder="@lang('admin.email')">
                    </div>
                </div>

                <div class="form-group row">
                    <label for="name" class="col-xs-12 col-form-label">@lang('admin.password')</label>
                    <div class="col-xs-10">
                        <input class="form-control" type="password" value="" name="password" required id="password" placeholder="@lang('admin.password')">
                    </div>
                </div>

                <div class="form-group row">
                    <label for="name" class="col-xs-12 col-form-label">@lang('admin.password_confirmation')</label>
                    <div class="col-xs-10">
                        <input class="form-control" type="password" value="" name="password_confirmation" required id="password_confirmation" placeholder="@lang('admin.password_confirmation')">
                    </div>
                </div>

                <div class="form-group row">
                    <label for="permission" class="col-xs-12 col-form-label">@lang('admin.role')</label>
                    <div class="col-xs-10">
                        @foreach($roles as $value)
                            @if($value->id>5)
                                <label><input type="checkbox" value="{{ $value->id }}" name="roles[]" id="role" />{{ $value->name }}</label>
                            @endif    
                        @endforeach
                    </div>
                </div>

                <div class="form-group row">
                    <label for="zipcode" class="col-xs-12 col-form-label"></label>
                    <div class="col-xs-10">
                        <button type="submit" class="btn btn-primary">@lang('admin.admins.Add_User')</button>
                        <a href="{{route('admin.sub-admins.index')}}" class="btn btn-default">@lang('admin.cancel')</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
