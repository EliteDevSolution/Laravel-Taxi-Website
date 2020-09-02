@extends('admin.layout.base')

@section('title', 'Provider Documents ')

@section('content')
<div class="content-area py-1">
    <div class="container-fluid">
        
        <div class="box box-block bg-white">
            <h5 class="mb-1">@lang('admin.provides.provider_name'): {{ $Document->provider->first_name }} {{ $Document->provider->last_name }}</h5>
            <h5 class="mb-1">@lang('admin.document.document_name'): {{ $Document->document->name }}</h5>
            <embed src="{{ asset('storage/'.$Document->url) }}" width="100%" height="100%" />

            <div class="row">
                <div class="col-xs-6">
                    <form action="{{ route('admin.provider.document.update', [$Document->provider->id, $Document->id]) }}" method="POST">
                        {{ csrf_field() }}
                        {{ method_field('PATCH') }}
                        <button class="btn btn-block btn-primary" type="submit">@lang('admin.provides.approve')</button>
                    </form>
                </div>

                <div class="col-xs-6">
                    <form action="{{ route('admin.provider.document.destroy', [$Document->provider->id, $Document->id]) }}" method="POST">
                        {{ csrf_field() }}
                        {{ method_field('DELETE') }}
                        <button class="btn btn-block btn-danger" type="submit">@lang('admin.provides.delete')</button>
                    </form>
                </div>
            </div>
        </div>
        
    </div>
</div>
@endsection