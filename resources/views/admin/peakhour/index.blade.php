@extends('admin.layout.base')

@section('title', 'PeakHours ')

@section('content')

    <div class="content-area py-1">
        <div class="container-fluid">
            
            <div class="box box-block bg-white">
                @if(Setting::get('demo_mode', 0) == 1)
                    <div class="col-md-12" style="height:50px;color:red;">
                        ** Demo Mode : @lang('admin.demomode')
                    </div>
                @endif
                <h5 class="mb-1">@lang('admin.peakhour.title')</h5>
                @can('peak-hour-create')
                <a href="{{ route('admin.peakhour.create') }}" style="margin-left: 1em;" class="btn btn-primary pull-right"><i class="fa fa-plus"></i> @lang('admin.peakhour.add_time')</a>
                @endcan

                <table class="table table-striped table-bordered dataTable" id="table-2">
                    <thead>
                        <tr>
                            <th>@lang('admin.id')</th>
                            <th>@lang('admin.peakhour.start_time') </th>
                            <th>@lang('admin.peakhour.end_time') </th>                           
                            <th>@lang('admin.action')</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($peakhour as $index => $peak)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ date('h:i A', strtotime($peak->start_time)) }}</td>
                            <td>{{ date('h:i A', strtotime($peak->end_time)) }} </td>
                           
                            <td>
                                <form action="{{ route('admin.peakhour.destroy', $peak->id) }}" method="POST">
                                    {{ csrf_field() }}
                                    <input type="hidden" name="_method" value="DELETE">
                                    @if( Setting::get('demo_mode', 0) == 0)
                                    @can('peak-hour-edit')
                                    <a href="{{ route('admin.peakhour.edit', $peak->id) }}" class="btn btn-info"><i class="fa fa-pencil"></i> Edit</a>
                                    @endcan
                                    @can('peak-hour-delete')
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
                            <th>@lang('admin.peakhour.start_time') </th>
                            <th>@lang('admin.peakhour.end_time') </th>                           
                            <th>@lang('admin.action')</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
            
        </div>
    </div>
@endsection