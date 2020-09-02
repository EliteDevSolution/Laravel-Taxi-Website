@extends('provider.layout.app')

@section('content')
<div class="pro-dashboard-head">
    <div class="container">
        <a href="{{ route('provider.profile.index') }}" class="pro-head-link">@lang('provider.profile.profile')</a>
        <a href="{{ route('provider.documents.index') }}" class="pro-head-link">@lang('provider.profile.manage_documents')</a>
        <a href="{{ route('provider.location.index') }}" class="pro-head-link">@lang('provider.profile.update_location')</a>
        <a href="{{route('provider.wallet.transation')}}" class="pro-head-link">@lang('provider.profile.wallet_transaction')</a>
        @if(config('constants.card')==1)
            <a href="{{ route('provider.cards') }}" class="pro-head-link">@lang('provider.card.list')</a>
        @endif    
        <a href="#" class="pro-head-link active">@lang('provider.profile.transfer')</a>
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
                            @lang('provider.transfer')
                            (@lang('provider.current_balance') : {{currency($wallet_balance)}})
                        </h3>
                    </div>
                    @include('common.notify')
                         <div class="row no-margin">
                                <div class="col-lg-12 col-md-12 col-sm-8 col-xs-12 no-padding">
                                    <div class="prof-form-sub-sec">
                                        <div class="row no-margin">
                                            <form class="profile-form" action="{{route('provider.requestamount')}}" method="POST"  role="form" id="requestform">
                                            {{ csrf_field() }}
                                            <div class="prof-sub-col col-sm-4 col-xs-12 no-left-padding">
                                                <div class="form-group">
                                                    <label>@lang('provider.amount')</label>
                                                    <input type="hidden" name='type' value='provider'/> 
                                                    <input type="text" class="form-control" placeholder="@lang('provider.amount')" name="amount" value="" required="">
                                                </div>
                                            </div>
                                            <div class="prof-sub-col col-sm-3 col-xs-12 no-right-padding">
                                                <div class="form-group">
                                                   <button type="submit" class="btn btn-block btn-primary update-link">@lang('provider.transfer')</button>
                                                </div>
                                            </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                   
                     <div class="manage-doc-section-content border-top">
                     <div class="tab-content list-content">
                        <div class="list-view pad30 ">
                            <table class="earning-table table table-responsive">
                                <thead>
                                    <tr>
                                        <th>@lang('provider.sno')</th>
                                        <th>@lang('provider.transaction_ref')</th>
                                        <th>@lang('provider.datetime')</th>
                                        <th>@lang('provider.amount')</th>
                                        <th>@lang('provider.status')</th>
                                    </tr>
                                </thead>
                                <tbody>
                                   @php($total=0)
                                   @foreach($pendinglist as $index=>$pending)
                                        @php($total+=$pending->amount)
                                        <tr>
                                            <td>{{$index+1}}</td>
                                            <td>{{$pending->alias_id}}</td>
                                            <td>{{$pending->created_at->diffForHumans()}}</td>
                                            <td>{{currency($pending->amount)}}</td>
                                            <td>
                                                {{$pending->status == '0' ? 'Pending' : 'Approved'}}
                                                <a href="{{ route('provider.requestcancel') }}?id={{$pending->id}}" class="alert alert-danger" style="padding: 5px;"><i class="fa fa-close"></i></a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>

                            </table>
                        </div>
                     </div>
                     </div>
               
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@section('scripts')
<script type="text/javascript">
    @if($wallet_balance<=0) 
        $("#requestform :input").prop("disabled", true);
    @elseif($total==$wallet_balance)
        $("#requestform :input").prop("disabled", true);    
    @endif    
</script>    
@endsection

