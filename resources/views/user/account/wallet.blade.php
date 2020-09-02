@extends('user.layout.base')

@section('title', 'Wallet ')

@section('content')

<div class="col-md-9">
    <div class="dash-content">
        <div class="row no-margin">
            <div class="col-md-12">
                <h4 class="page-title">@lang('user.my_wallet')</h4>
            </div>
        </div>
        @include('common.notify')

        <div class="row no-margin">
            <form action="{{url('add/money')}}" id="add_money" method="POST">
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
                    
                    @if(Config('constants.paystack') == 1)
                    <select style="display: none;" class="form-control" name="card_id" id="card_id">
                      @foreach($cards as $card)
                        <option @if($card->is_default == 1) selected @endif value="{{$card->card_id}}">{{strtoupper($card->card_name)}} **** **** **** {{$card->last4}}</option>
                      @endforeach
                    </select>
                    @endif

                    @if(Config('constants.card') == 1)
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
                    <input type="hidden" name="user_type" value="user" />
                    <div class="input-group full-input">
                        <input type="number" class="form-control" min=10 name="amount" placeholder="@lang('user.enter_amount')" required>
                    </div>

                    
                    <button type="submit" id="submit-button" class="full-primary-btn fare-btn">@lang('user.add_money')</button> 

                </div>
            </form>

        </div>

        <div class="manage-doc-section-content border-top">
             <div class="tab-content list-content">
                <div class="list-view pad30 ">
                    <table class="earning-table table table-responsive">
                        <thead>
                            <tr>
                                <th>@lang('provider.sno')</th>
                                <th>@lang('provider.transaction_ref')</th>
                                <th>@lang('provider.transaction_desc')</th>
                                <th>@lang('provider.status')</th>
                                <th>@lang('provider.amount')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php($page = ($pagination->currentPage-1)*$pagination->perPage)
                               @foreach($wallet_transation as $index=>$wallet)
                               @php($page++)
                                    <tr>
                                        <td>{{$page}}</td>
                                        <td>{{$wallet->transaction_alias}}</td>
                                        <td>{{$wallet->transaction_desc}}</td>
                                        <td>@if($wallet->type == 'C')  @lang('user.credit') @else @lang('user.debit') @endif</td>
                                        <td>{{currency($wallet->amount)}}
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

<script type="text/javascript">
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

</script>
@endsection