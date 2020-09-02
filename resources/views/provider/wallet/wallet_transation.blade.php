@extends('provider.layout.app')

@section('content')
<div class="pro-dashboard-head">
    <div class="container">
        <a href="{{ route('provider.profile.index') }}" class="pro-head-link">@lang('provider.profile.profile')</a>
        <a href="{{ route('provider.documents.index') }}" class="pro-head-link">@lang('provider.profile.manage_documents')</a>
        <a href="{{ route('provider.location.index') }}" class="pro-head-link">@lang('provider.profile.update_location')</a>
        <a href="#" class="pro-head-link active">@lang('provider.profile.wallet_transaction')</a>
        @if(config('constants.card') == 1 || config('constants.paystack') == 1)
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
                    <!-- <div class="manage-doc-section-head row no-margin">
                        <h3 class="manage-doc-tit">
                            @lang('provider.profile.wallet_transaction')
                            (@lang('provider.current_balance') : {{currency($wallet_balance)}})
                        </h3>
                    </div> -->
                    @include('common.notify')
                    <div class="row no-margin">
                    <form action="{{url('/provider/add/money')}}" id="add_money" method="POST">
                    {{ csrf_field() }}
                        <div class="col-md-6">
                             
                            <div class="wallet">
                                <h4 class="amount">
                                    <span class="price">{{currency(Auth::user()->wallet_balance)}}</span>
                                    <span class="txt">@lang('user.in_your_wallet')</span>
                                </h4>
                            </div>                                                               

                        </div>
                        <div class="col-md-6">
                            
                            <h6><strong>@lang('user.add_money')</strong></h6>

                            <select class="form-control" autocomplete="off" name="payment_mode" onchange="card(this.value);">
                              @if(config('constants.card') == 1 || config('constants.paystack') == 1)
                              @if($cards->count() > 0)
                                <option value="CARD">CARD</option>
                              @endif
                              @if(Config::get('constants.braintree') == 1)
                              <option value="BRAINTREE">BRAINTREE</option>
                              @endif
                              @endif
                              @if(Config::get('constants.payumoney') == 1)
                              <option value="PAYUMONEY">PAYUMONEY</option>
                              @endif
                              @if(Config::get('constants.paypal') == 1)
                              <option value="PAYPAL">PAYPAL</option>
                              @endif
                              @if(Config::get('constants.paytm') == 1)
                              <option value="PAYTM">PAYTM</option>
                              @endif
                            </select>
                            <br>
                            
                            @if(config('constants.card') == 1 || config('constants.paystack') == 1)
                            <select style="display: none;" class="form-control" name="card_id" id="card_id">
                              @foreach($cards as $card)
                                <option @if($card->is_default == 1) selected @endif value="{{$card->card_id}}">{{strtoupper($card->brand)}} **** **** **** {{$card->last_four}}</option>
                              @endforeach
                            </select>
                            @endif

                            @if(Config::get('constants.braintree') == 1)
                                <div style="display: none;" id="braintree">
                                    <div id="dropin-container"></div>
                                </div>
                            @endif

                            <br>
                            @if(Config::get('constants.braintree') == 1)
                            <input type="hidden" name="braintree_nonce" value="" />
                            @endif
                            <input type="hidden" name="user_type" value="provider" />
                            <div class="input-group full-input">
                                <input type="number" required min=10 class="form-control" name="amount" placeholder="@lang('user.enter_amount')" >
                            </div>

                            
                            <button type="submit" id="submit-button" class="full-primary-btn fare-btn">@lang('user.add_money')</button> 

                        </div>
                    </form>

                </div>

                   
                     <div class="manage-doc-section-content">
                     <div class="tab-content list-content">
                      <div class="list-view pad30 ">

                            <table class="earning-table table table-responsive">
                                <thead>
                                    <tr>
                                        <th>@lang('provider.sno')</th>
                                        <th>@lang('provider.transaction_ref')</th>
                                        <th>@lang('provider.datetime')</th>
                                       <!--  <th>@lang('provider.transaction_desc')</th>
                                        <th>@lang('provider.status')</th> -->
                                        <th>@lang('provider.amount')</th>
                                    </tr>
                                </thead>
                                <tbody>
                                @php($page = ($pagination->currentPage-1)*$pagination->perPage)
                               @foreach($wallet_transation as $index=>$wallet)
                               @php($page++)
                                    <tr>
                                        <td>{{$page}}</td>
                                        <td><a href="javascript:void(0);" class="new-pro-link trdclass" data-toggle="trdetails" title="Transaction Details" data-content="" data-alias="#wallet_{{$wallet->transaction_alias}}">{{$wallet->transaction_alias}}</a></td>
                                        <td>{{$wallet->transactions[0]->created_at->diffForHumans()}}</td>
                                       <!--  <td>{{$wallet->transaction_desc}}</td> -->
                                       <!--  <td>@if($wallet->type == 'C') @lang('provider.credit') @else @lang('provider.debit') @endif</td> -->
                                        <td>{{currency($wallet->amount)}}
                                        </td>
                                        <td style="display: none;" id="wallet_{{$wallet->transaction_alias}}">
                                            <table class="table table-responsive">
                                                <thead>
                                                    <tr>
                                                        <th>Description</th><th>Type</th><th>Amount</th>
                                                    </tr>
                                                <tbody>
                                                    @foreach($wallet->transactions as $index=>$transactions)
                                                        <tr>
                                                            <td>{{$transactions->transaction_desc}}</td>
                                                            <td>@if($transactions->type=='C') Credit @else Debit @endif</td>
                                                            <td>@if($transactions->type=='C')<span style="color: green"> {{currency($transactions->amount)}}</span>@else<span style="color: red"> {{currency($transactions->amount)}}</span>@endif</td>
                                                        </tr>
                                                    @endforeach    
                                                </tbody>
                                            </table>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                          
                            {{ $wallet_transation->links() }}
                        </div>
                     </div>
                     </div>
               
                </div>
            </div>
        </div>
    </div>

</div>
<style type="text/css">
    .popover{
        max-width: 500px !important;
    }
</style>
@endsection

@section('scripts')
@if(Config::get('constants.braintree') == 1)
<script src="https://js.braintreegateway.com/web/dropin/1.14.1/js/dropin.min.js"></script>

<script>
    var button = document.querySelector('#submit-button');
    var form = document.querySelector('#add_money');
    braintree.dropin.create({
      authorization: '{{$clientToken}}',
      container: '#dropin-container',
      //Here you can hide paypal
      paypal: {
        flow: 'vault'
      }
    }, function (createErr, instance) {
      button.addEventListener('click', function (e) {
        e.preventDefault();
        if(document.querySelector('select[name="payment_mode"]').value == "BRAINTREE") {
            instance.requestPaymentMethod(function (requestPaymentMethodErr, payload) {
               document.querySelector('input[name="braintree_nonce"]').value = payload.nonce;
               console.log(payload.nonce);
               form.submit();
          });
          } else {
            form.submit();
          }
        
      });
    });
</script>
@endif

<script>
var request=0; 

    @if(Config::get('constants.card') == 1 || Config::get('constants.paystack') == 1)
        card('CARD');
    @endif

    function card(value){
        $('#card_id, #braintree').fadeOut(300);
        if(value == 'CARD'){
            $('#card_id').fadeIn(300);
        }else if(value == 'BRAINTREE'){
            $('#braintree').fadeIn(300);
        }
    }

$(document).ready(function(){
    $("[data-toggle=trdetails]").popover({
        html : true,
        content: function() {
          $('[data-toggle=trdetails]').not(this).popover('hide');  
          var content = $(this).attr("data-alias");
          console.log(content);
          return $(content).html();
        },
        
    });   
});  

</script>
@endsection
