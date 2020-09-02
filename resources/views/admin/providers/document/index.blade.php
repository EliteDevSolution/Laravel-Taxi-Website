@extends('admin.layout.base')

@section('title', 'Provider Documents ')

@section('content')
<div class="content-area py-1">
    <div class="container-fluid">
        @can('provider-services')
        <div class="box box-block bg-white">
          @if(Setting::get('demo_mode', 0) == 1)
                <div class="col-md-12" style="height:50px;color:red;">
                    <h1>** Demo Mode : No Permission to Edit and Delete.</h1>
                </div>
             @endif
            <h5 class="mb-1">@lang('admin.provides.type_allocation')</h5>
            <a href="{{$backurl}}" style="margin-left: 1em;margin-top: -30px" class="btn btn-primary pull-right"><i class="fa fa-arrow-left"></i> Back</a>
            <div class="row">
                <div class="col-xs-12">                    
                    @if($ProviderService->count() > 0)
                    <hr><h6>Allocated Services :  </h6>
                    <table class="table table-striped table-bordered dataTable">
                        <thead>
                            <tr>
                                <th>@lang('admin.provides.service_name')</th>
                                <th>@lang('admin.provides.service_number')</th>
                                <th>@lang('admin.provides.service_model')</th>
                                <th>@lang('admin.action')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($ProviderService as $service)
                            <tr>
                                <td>{{ $service->service_type->name }}</td>
                                <td>{{ $service->service_number }}</td>
                                <td>{{ $service->service_model }}</td>
                                <td>
                                @if( Setting::get('demo_mode', 0) == 0)
                                    <form action="{{ route('admin.provider.document.service', [$Provider->id, $service->id]) }}" method="POST">
                                        {{ csrf_field() }}
                                        {{ method_field('DELETE') }}
                                        @can('provider-service-delete')<button class="btn btn-danger btn-large btn-block">Delete</a>@endcan
                                    </form>
                                     @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <th>@lang('admin.provides.service_name')</th>
                                <th>@lang('admin.provides.service_number')</th>
                                <th>@lang('admin.provides.service_model')</th>
                                <th>@lang('admin.action')</th>
                            </tr>
                        </tfoot>
                    </table>
                    @endif
                    <hr>
                </div>
                <form action="{{ route('admin.provider.document.store', $Provider->id) }}" method="POST">
                    {{ csrf_field() }}
                    <div class="col-xs-3">
                        <select class="form-control input" name="service_type" required>
                            @forelse($ServiceTypes as $Type)
                            <option value="{{ $Type->id }}">{{ $Type->name }}</option>
                            @empty
                            <option>- @lang('admin.service_select') -</option>
                            @endforelse
                        </select>
                    </div>
                    <div class="col-xs-3">
                        <input type="text" required name="service_number" class="form-control" placeholder="Number (CY 98769)">
                    </div>
                    <div class="col-xs-3">
                        <input type="text" required name="service_model" class="form-control" placeholder="Model (Audi R8 - Black)">
                    </div>
                    @if( Setting::get('demo_mode', 0) == 0)
                    <div class="col-xs-3">
                        @can('provider-service-update')<button class="btn btn-primary btn-block" type="submit">@lang('admin.update')</button>@endcan
                    </div>
                    @endif
                </form>
            </div>
        </div>
        @endcan
        @can('provider-documents')
        <div class="box box-block bg-white">
            <h5 class="mb-1">@lang('admin.provides.provider_documents')</h5>
            @if( Setting::get('demo_mode', 0) == 0)
                @if(count($Provider->documents)>0)
                    <a href="{{route('admin.download', $Provider->id)}}" style="margin-left: 1em;" class="btn btn-primary pull-right"><i class="fa fa-download"></i> @lang('admin.provides.download')</a>
                @endif    
            @endif    
            <table class="table table-striped table-bordered dataTable" id="table-2">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>@lang('admin.provides.document_type')</th>
                        <th>@lang('admin.status')</th>
                        <th>@lang('admin.action')</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($Provider->documents as $Index => $Document)
                    <tr>
                        <td>{{ $Index + 1 }}</td>
                        <td>@if($Document->document){{ $Document->document->name }}@endif</td>
                        <td>{{ $Document->status }}</td>
                        <td>
                            <div class="input-group-btn">
                            @if( Setting::get('demo_mode', 0) == 0)
                                @can('provider-document-edit')
                                <a href="{{ route('admin.provider.document.edit', [$Provider->id, $Document->id]) }}"><span class="btn btn-success btn-large">@lang('admin.view')</span></a>
                                @endcan
                                @can('provider-document-delete')
                                <button class="btn btn-danger btn-large doc-delete" id="{{$Document->id}}">@lang('admin.delete')</button>
                                <form action="{{ route('admin.provider.document.destroy', [$Provider->id, $Document->id]) }}" method="POST" id="form_delete_{{$Document->id}}">
                                    {{ csrf_field() }}
                                    {{ method_field('DELETE') }}
                                </form>
                                @endcan
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <th>#</th>
                        <th>@lang('admin.provides.document_type')</th>
                        <th>@lang('admin.status')</th>
                        <th>@lang('admin.action')</th>
                    </tr>
                </tfoot>
            </table>
        </div>
        @endcan
    </div>
</div>
@endsection

@section('scripts')
<script type="text/javascript">
    $(".doc-delete").on('click', function(){       
        var doc_id=$(this).attr('id');
        $("#form_delete_"+doc_id).submit();
    });
</script>
@endsection