@extends('admin.layout.base')

@section('title', 'Payment History ')

@section('content')

    <div class="content-area py-1">
        <div class="container-fluid">
            <div class="box box-block bg-white">
                <h5 class="mb-1">@lang('admin.payment.payment_history')</h5>
                <table class="table table-striped table-bordered dataTable" id="table-4">
                    <thead>
                        <tr>
                            <th>@lang('admin.payment.request_id')</th>
                            <th>@lang('admin.payment.transaction_id')</th>
                            <!-- <th>@lang('admin.payment.from')</th>
                            <th>@lang('admin.payment.to')</th> -->
                            <th>@lang('admin.payment.total_amount')</th>
                            <th>@lang('admin.payment.provider_amount')</th>
                            <th>@lang('admin.payment.payment_mode')</th>
                            <th>@lang('admin.payment.payment_status')</th>
                            <th>@lang('admin.action')</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($payments as $index => $payment)
                        <tr>
                            <td>{{$payment->id}}</td>
                            <td>@if(!empty($payment->payment->payment_id)){{$payment->payment->payment_id}}@else NA @endif</td>
                            <!-- <td>{{$payment->user?$payment->user->first_name:''}} {{$payment->user?$payment->user->last_name:''}}</td>
                            <td>{{$payment->provider?$payment->provider->first_name:''}} {{$payment->provider?$payment->provider->last_name:''}}</td> -->
                            <td>{{currency($payment->payment->total)}}</td>
                            <td>{{currency($payment->payment->provider_pay)}}</td>
                            <td>{{$payment->payment_mode}}</td>
                            <td>
                                @if($payment->paid)
                                    Paid
                                @else
                                    Not Paid
                                @endif
                            </td>
                            <td>
                            <div class="btn-group" role="group">
                                <button type="button" class="btn btn-primary waves-effect dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                                    Action
                                </button>
                                <div class="dropdown-menu">
                                    <a href="{{ route('admin.requests.show', $payment->id) }}" class="dropdown-item">
                                        <i class="fa fa-search"></i> More Details
                                    </a>
                                </div>
                            </div>
                        </td>
                        </tr>
                    @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <th>@lang('admin.payment.request_id')</th>
                            <th>@lang('admin.payment.transaction_id')</th>
                            <!-- <th>@lang('admin.payment.from')</th>
                            <th>@lang('admin.payment.to')</th> -->
                            <th>@lang('admin.payment.total_amount')</th>
                            <th>@lang('admin.payment.provider_amount')</th>
                            <th>@lang('admin.payment.payment_mode')</th>
                            <th>@lang('admin.payment.payment_status')</th>
                            <th>@lang('admin.action')</th>
                        </tr>
                    </tfoot>
                </table>
                @include('common.pagination')
            </div>
            
        </div>
    </div>
@endsection