@extends('user.layout.base')

@section('title', 'Change Password ')

@section('content')

<div class="col-md-9">
    <div class="dash-content">
        <div class="row no-margin">
            <div class="col-md-12">
                <h4 class="page-title">@lang('user.profile.change_password')</h4>
            </div>
        </div>
        @if(Setting::get('demo_mode', 0) == 1)
            <div class="alert alert-danger">
                 @lang('admin.demomode')
            </div>
        @else
            @include('common.notify')
        @endif
        <div class="row no-margin edit-pro">
            <form action="{{url('change/password')}}" method="post">
            {{ csrf_field() }}
                <div class="col-md-6">
                    <div class="form-group">
                        <label>@lang('user.profile.old_password')</label>
                        <input type="password" name="old_password" class="form-control" placeholder="@lang('user.profile.old_password')">
                    </div>
                    <div class="form-group">
                        <label>@lang('user.profile.password')</label>
                        <input type="password" name="password" class="form-control" placeholder="@lang('user.profile.password')">
                    </div>

                    <div class="form-group">
                        <label>@lang('user.profile.confirm_password')</label>
                        <input type="password" name="password_confirmation" class="form-control" placeholder="@lang('user.profile.confirm_password')">
                    </div>
                  
                    <div>
                        <button type="submit" class="form-sub-btn big">@lang('user.profile.change_password')</button>
                    </div>
                </div>
            </form>
        </div>

    </div>
</div>

@endsection