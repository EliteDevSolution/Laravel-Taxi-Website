<?php
if(config('constants.card', 0) == 0){
    header('Location:/fleet/dashboard');
    exit;
}   
?>
@extends('fleet.layout.base')

@section('title', 'Fleet Debit Cards')

@section('content')

<div class="content-area py-1">
        <div class="container-fluid">
            
            <div class="box box-block bg-white">
                <h5 class="mb-1">Debit Cards</h5>               
                <a href="#" style="margin-left: 1em;" class="btn btn-primary pull-right" data-toggle="modal" data-target="#add-card-modal"><i class="fa fa-plus"></i> @lang('provider.card.add_debit_card')</a>
                <table class="table table-striped table-bordered dataTable" id="table-4">
                    <thead>
                        <tr>
                            <th>@lang('provider.card.type')</th>
                            <th>@lang('provider.card.four')</th>
                        </tr>
                    </thead>
                    <tbody>
                       @if(count($cards)!='0')    
                        @foreach($cards as $each)
                            <tr>
                                <td>{{ $each->brand }}</td>
                                <td>{{ $each->last_four }}</td>
                            </tr>
                        @endforeach                        
                        @endif
                    </tbody>
                </table>                
            </div>
            
        </div>

        <!-- Add Card Modal -->
    <div id="add-card-modal" class="modal fade" role="dialog">
      <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content" >
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <h4 class="modal-title">@lang('provider.card.add_debit_card')</h4>
          </div>
            <form id="payment-form" action="{{ url('fleet/card/store') }}" method="POST" >
                {{ csrf_field() }}

          <input type="hidden" data-stripe="currency" value="usd">
          <div class="modal-body">
            <div class="row no-margin" id="card-payment">
                <div class="payment-errors" style="display: none">
                    <div class="alert alert-danger">
                        <button type="button" class="close" data-dismiss="alert">×</button>
                        <span id="errortxt"></span>
                    </div>
                </div>    
                <div class="form-group col-md-12 col-sm-12">
                    <label>@lang('provider.card.fullname')</label>
                    <input data-stripe="name" autocomplete="off" required type="text" class="form-control" placeholder="@lang('provider.card.fullname')">
                </div>
                <div class="form-group col-md-12 col-sm-12">
                    <label>@lang('provider.card.card_no')</label>
                    <input data-stripe="number" type="text" onkeypress="return isNumberKey(event);" required autocomplete="off" maxlength="16" class="form-control" placeholder="@lang('provider.card.card_no')">
                </div>
                <div class="form-group col-md-4 col-sm-12">
                    <label>@lang('provider.card.month')</label>
                    <input type="text" onkeypress="return isNumberKey(event);" maxlength="2" required autocomplete="off" class="form-control" data-stripe="exp-month" placeholder="MM">
                </div>
                <div class="form-group col-md-4 col-sm-12">
                    <label>@lang('provider.card.year')</label>
                    <input type="text" onkeypress="return isNumberKey(event);" maxlength="2" required autocomplete="off" data-stripe="exp-year" class="form-control" placeholder="YY">
                </div>
                <div class="form-group col-md-4 col-sm-12">
                    <label>@lang('provider.card.cvv')</label>
                    <input type="text" data-stripe="cvc" onkeypress="return isNumberKey(event);" required autocomplete="off" maxlength="4" class="form-control" placeholder="@lang('provider.card.cvv')">
                </div>
            </div>
          </div>

          <div class="modal-footer">
            <button type="submit" class="btn btn-default" >@lang('provider.card.add_card')</button>
          </div>
        </form>

        </div>

      </div>
    </div> 

    </div>
@endsection


@section('scripts')
<script type="text/javascript" src="https://js.stripe.com/v2/"></script>

<script type="text/javascript">
    Stripe.setPublishableKey("{{ config('constants.stripe_publishable_key', '')}}");

     var stripeResponseHandler = function (status, response) {
        var $form = $('#payment-form');

        if (response.error) {
            // Show the errors on the form
            $form.find('.payment-errors').text(response.error.message);
            $form.find('button').prop('disabled', false);
            alert(response.error.message);

        } else {
            // token contains id, last4, and card type
            var token = response.id;

            // Insert the token into the form so it gets submitted to the server
            $form.append($('<input type="hidden" id="stripeToken" name="stripe_token" />').val(token));

            jQuery($form.get(0)).submit();
            $("#add-card-modal").modal('toggle');
        }


    };
            
    $('#payment-form').submit(function (e) {            
        if ($('#stripeToken').length == 0)
        {
            
            var $form = $(this);
            $form.find('button').prop('disabled', true);                
            Stripe.card.createToken($form, stripeResponseHandler);
            return false;
        }
    });

    function isNumberKey(evt)
    {
        var charCode = (evt.which) ? evt.which : event.keyCode;
        if (charCode != 46 && charCode > 31 
        && (charCode < 48 || charCode > 57))
            return false;

        return true;
    }

    function set_default(id)
    {
        $.ajax({
            method : 'POST',
            url : '{{ url('fleet/card/set') }}',
            data : '_token={{csrf_token()}}&id='+id,
            success:function(html)
            {
                if(html=='success')
                {
                    alert('Successfully made changes');
                }
                else{
                    alert('Something Went wrong'); 
                }
            }

        })
    }
</script>    
@endsection
