@extends('provider.layout.app')

@section('content')
<div class="pro-dashboard-head">
    <div class="container">
        <a href="{{ route('provider.profile.index') }}" class="pro-head-link">@lang('provider.profile.profile')</a>
        <a href="#" class="pro-head-link active">@lang('provider.profile.manage_documents')</a>
        <a href="{{ route('provider.location.index') }}" class="pro-head-link">@lang('provider.profile.update_location')</a>
        <a href="{{route('provider.wallet.transation')}}" class="pro-head-link">@lang('provider.profile.wallet_transaction')</a>
        @if(config('constants.card')==1)
            <a href="{{ route('provider.cards') }}" class="pro-head-link">@lang('provider.card.list')</a>
        @endif    
        <a href="{{ route('provider.transfer') }}" class="pro-head-link">@lang('provider.profile.transfer')</a>
        @if(config('constants.referral')==1)
            <a href="{{ route('provider.referral') }}" class="pro-head-link">@lang('provider.profile.refer_friend')</a>
        @endif
    </div>
</div>

<div class="pro-dashboard-content gray-bg">
    <div class="container">
        <div class="manage-docs pad30">
            <div class="manage-doc-content">
                <div class="manage-doc-section pad50">
                    <div class="manage-doc-section-head row no-margin">
                        <h3 class="manage-doc-tit">
                            @lang('provider.profile.driver_document')
                        </h3>
                    </div>
                    @include('common.notify')
                    <div class="manage-doc-section-content">
                        @foreach($DriverDocuments as $Document)
                        <div class="manage-doc-box row no-margin border-top">
                            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                <div class="manage-doc-box-left">
                                    <p class="manage-txt">{{ $Document->name }}</p>
                                    <p class="license">@lang('provider.expires'): {{ $Provider->document($Document->id) ? ($Provider->document($Document->id)->expires_at ? $Provider->document($Document->id)->expires_at: 'N/A') : 'N/A' }}</p>
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                <div class="manage-doc-box-center text-center">
                                    <p class="manage-badge {{ $Provider->document($Document->id) ? ($Provider->document($Document->id)->status == 'ASSESSING' ? 'yellow-badge' : 'green-badge') : 'red-badge'}}">
                                        {{ $Provider->document($Document->id) ? $Provider->document($Document->id)->status : 'MISSING' }}
                                    </p>
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                <div class="manage-doc-box-right text-right">
                                    <div class="fileinput fileinput-new input-group" data-provides="fileinput">
                                        <form action="{{ route('provider.documents.update', $Document->id) }}" method="POST" enctype="multipart/form-data">
                                            {{ csrf_field() }}
                                            {{ method_field('PATCH') }}
                                            <div class="form-control" data-trigger="fileinput">
                                                <span class="fileinput-filename"></span>
                                            </div>
                                            <span class="input-group-addon btn btn-default btn-file fileinput-exists btn-submit">
                                                <button>
                                                    <i class="fa fa-check"></i>
                                                </button>
                                            </span>
                                            <span class="input-group-addon btn btn-default btn-file">
                                                <span class="fileinput-new upload-link">
                                                    <i class="fa fa-upload upload-icon"></i> @lang('provider.profile.upload')
                                                </span>
                                                <span class="fileinput-exists">
                                                    <i class="fa fa-edit"></i>
                                                </span>
                                                <input type="file" name="document" accept="application/pdf, image/*">
                                            </span>
                                            <a href="#" class="input-group-addon btn btn-default fileinput-exists" data-dismiss="fileinput">
                                                <i class="fa fa-times"></i>
                                            </a>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

                <div class="manage-doc-section">
                    <div class="manage-doc-section-head row no-margin">
                        <h3 class="manage-doc-tit">
                           @lang('provider.profile.vehicle_document')
                        </h3>
                    </div>

                    <div class="manage-doc-section-content">
                        @foreach($VehicleDocuments as $Document)
                        <div class="manage-doc-box row no-margin border-top">
                            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                <div class="manage-doc-box-left">
                                    <p class="manage-txt">{{ $Document->name }}</p>
                                    <p class="license">@lang('provider.expires'): {{ $Provider->document($Document->id) ? ($Provider->document($Document->id)->expires_at ? $Provider->document($Document->id)->expires_at: 'N/A'): 'N/A' }}</p>
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                <div class="manage-doc-box-center text-center">
                                    <p class="manage-badge {{ $Provider->document($Document->id) ? ($Provider->document($Document->id)->status == 'ASSESSING' ? 'yellow-badge' : 'green-badge') : 'red-badge'}}">
                                        {{ $Provider->document($Document->id) ? $Provider->document($Document->id)->status : 'MISSING' }}
                                    </p>
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                <div class="manage-doc-box-right text-right">
                                    <div class="fileinput fileinput-new input-group" data-provides="fileinput">
                                        <form action="{{ route('provider.documents.update', $Document->id) }}" method="POST" enctype="multipart/form-data">
                                            {{ csrf_field() }}
                                            {{ method_field('PATCH') }}
                                            <div class="form-control" data-trigger="fileinput">
                                                <span class="fileinput-filename"></span>
                                            </div>
                                            <span class="input-group-addon btn btn-default btn-file fileinput-exists btn-submit">
                                                <button>
                                                    <i class="fa fa-check"></i>
                                                </button>
                                            </span>
                                            <span class="input-group-addon btn btn-default btn-file">
                                                <span class="fileinput-new upload-link">
                                                    <i class="fa fa-upload upload-icon"></i> @lang('provider.profile.upload')
                                                </span>
                                                <span class="fileinput-exists">
                                                    <i class="fa fa-edit"></i>
                                                </span>
                                                <input type="file" name="document" accept="application/pdf, image/*">
                                            </span>
                                            <a href="#" class="input-group-addon btn btn-default fileinput-exists" data-dismiss="fileinput">
                                                <i class="fa fa-times"></i>
                                            </a>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@section('styles')
<link href="{{ asset('asset/css/jasny-bootstrap.min.css') }}" rel="stylesheet" type="text/css">
<style type="text/css">
    .fileinput .btn-file {
        padding:0;
        background-color: #fff;
        border: 0;
        border-radius:0!important;
    }
    .fileinput .form-control {
        border: 0;
        box-shadow : none;
        border-left:0;
        border-right:5px;
    }
    .fileinput .upload-link {
        border:0;
        border-radius: 0;
        padding:0;
    }
    .input-group-addon.btn {
        background: #fff;
        border: 1px solid #37b38b;
        border-radius: 0; 
        padding: 10px;
        height: 40px;
        line-height: 20px;
    }
    .fileinput .fileinput-filename {
        font-size: 10px;
    }
    .fileinput .btn-submit {
        padding: 0;
    }
    .fileinput button {
        background-color: white;
        border: 0;
        padding: 10px;
    }
</style>
@endsection

@section('scripts')
<script type="text/javascript" src="{{ asset('asset/js/jasny-bootstrap.min.js') }}"></script>
@endsection