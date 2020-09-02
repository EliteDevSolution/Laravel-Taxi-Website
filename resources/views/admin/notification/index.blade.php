@extends('admin.layout.base')

@section('title', 'Notifications ')

@section('content')

    <div class="content-area py-1">
        <div class="container-fluid">
            
            <div class="box box-block bg-white">
                @if(Setting::get('demo_mode', 0) == 1)
                    <div class="col-md-12" style="height:50px;color:red;">
                        ** Demo Mode : @lang('admin.demomode')
                    </div>
                @endif
                <h5 class="mb-1">@lang('admin.notification.title')</h5>
                @can('notification-create')
                <a href="{{ route('admin.notification.create') }}" style="margin-left: 1em;" class="btn btn-primary pull-right"><i class="fa fa-plus"></i> @lang('admin.notification.add')</a>
                @endcan

                <table class="table table-striped table-bordered dataTable" id="table-2">
                    <thead>
                        <tr>
                            <th>@lang('admin.id')</th>
                            <th>@lang('admin.notification.notify_type') </th>
                            <th>@lang('admin.notification.notify_image') </th>                           
                            <th>@lang('admin.notification.notify_desc') </th>
                            <th>@lang('admin.notification.notify_status') </th>                           
                            <th>@lang('admin.action')</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($notification as $index => $notify)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $notify->notify_type }}</td>
                            <td>
                                @if($notify->image) 
                                    <img src="{{$notify->image}}" style="height: 50px" >
                                @else
                                    N/A
                                @endif
                            </td>    
                            <td>{{ $notify->description }} </td>

                            <td>
                                @if($notify->status=='active')
                                    <span class="tag tag-success">Active</span>
                                @else
                                    <span class="tag tag-danger">Inactive</span>
                                @endif
                            </td>
                           
                            <td>
                                <form action="{{ route('admin.notification.destroy', $notify->id) }}" method="POST">
                                    {{ csrf_field() }}
                                    <input type="hidden" name="_method" value="DELETE">
                                    @if( Setting::get('demo_mode', 0) == 0)
                                    @can('notification-edit')
                                    <a href="{{ route('admin.notification.edit', $notify->id) }}" class="btn btn-info"><i class="fa fa-pencil"></i> Edit</a>
                                    @endcan
                                    @can('notification-delete')
                                    <button class="btn btn-danger" onclick="return confirm('Are you sure?')"><i class="fa fa-trash"></i> Delete</button>
                                    @endcan
                                    @endif
                                </form>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <th>@lang('admin.id')</th>
                            <th>@lang('admin.notification.notify_type') </th>
                            <th>@lang('admin.notification.notify_image') </th>                           
                            <th>@lang('admin.notification.notify_desc') </th>
                            <th>@lang('admin.notification.notify_status') </th>                           
                            <th>@lang('admin.action')</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
            
        </div>
    </div>
@endsection