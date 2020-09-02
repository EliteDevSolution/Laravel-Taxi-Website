@extends('admin.layout.base')

@section('title', 'Update User ')

@section('content')

<div class="content-area py-1">
    <div class="container-fluid">
        <div class="box box-block bg-white">
            <a href="{{ route('admin.sub-admins.index') }}" class="btn btn-default pull-right"><i class="fa fa-angle-left"></i> @lang('admin.back')</a>

            <h5 style="margin-bottom: 2em;">@lang('admin.admins.update_User')</h5>

            <form class="form-horizontal" action="{{route('admin.sub-admins.update', $user->id )}}" method="POST" role="form">
                {{csrf_field()}}
                <input type="hidden" name="_method" value="PATCH">
                <div class="form-group row">
                    <label for="name" class="col-xs-12 col-form-label">@lang('admin.name')</label>
                    <div class="col-xs-10">
                        <input class="form-control" type="text" value="{{ $user->name }}" name="name" required id="name" placeholder="First Name">
                    </div>
                </div>

                <div class="form-group row">
                    <label for="name" class="col-xs-12 col-form-label">@lang('admin.email')</label>
                    <div class="col-xs-10">
                        <input class="form-control" type="text" value="{{ $user->email }}" name="email" required id="email" placeholder="First Name">
                    </div>
                </div>


                <!-- <div class="form-group row">
                    <label for="name" class="col-xs-12 col-form-label">@lang('admin.name')</label>
                    <div class="col-xs-10">
                        <input class="form-control" type="password" value="" name="password" id="name" placeholder="First Name">
                    </div>
                </div>
                
                <div class="form-group row">
                    <label for="name" class="col-xs-12 col-form-label">@lang('admin.name')</label>
                    <div class="col-xs-10">
                        <input class="form-control" type="password" value="" name="password_confirmation" id="password_confirmation" placeholder="First Name">
                    </div>
                </div> -->

                <div class="form-group row">
                    <label for="permission" class="col-xs-12 col-form-label">@lang('admin.role')</label>
                    <div class="col-xs-10">
                        @foreach($roles as $role)
                        <label><input type="checkbox" @php if(in_array($role->id, $userRole)) { echo 'checked'; } @endphp value="{{ $role->id }}" name="roles[]" id="roles" />
                        {{ $role->name }}</label>
                        @endforeach
                    </div>
                </div>

                <div class="form-group row">
                    <label for="zipcode" class="col-xs-12 col-form-label"></label>
                    <div class="col-xs-10">
                        <button type="submit" class="btn btn-primary">@lang('admin.admins.update_User')</button>
                        <a href="{{route('admin.sub-admins.index')}}" class="btn btn-default">@lang('admin.cancel')</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
