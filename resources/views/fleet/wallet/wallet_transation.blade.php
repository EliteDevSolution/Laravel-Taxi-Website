@extends('fleet.layout.base')

@section('title', 'Fleet Wallet Transactions')

@section('content')

<div class="content-area py-1">
        <div class="container-fluid">
            
            <div class="box box-block bg-white">
                <h5 class="mb-1">Fleet Wallet Transactions (@lang('provider.current_balance') : {{currency($wallet_balance)}})</h5>
                <table class="table table-striped table-bordered dataTable" id="table-4">
                    <thead>
                        <tr>
                            <th>@lang('provider.sno')</th>
                            <th>@lang('provider.transaction_ref')</th>
                            <th>@lang('provider.datetime')</th>
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
                                <td>{{$wallet->created_at->diffForHumans()}}</td>
                                <td>{{$wallet->transaction_desc}}</td>
                                <td>{{$wallet->type == 'C' ? 'Credit' : 'Debit'}}</td>
                                <td>{{currency($wallet->amount)}}
                                </td>
                               
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                @include('common.pagination')
            </div>
            
        </div>
    </div>
@endsection



