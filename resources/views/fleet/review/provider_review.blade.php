@extends('fleet.layout.base')

@section('title', 'Provider Reviews ')

@section('content')

    <div class="content-area py-1">
        <div class="container-fluid">
            
            <div class="box box-block bg-white">
                <h5 class="mb-1">@lang('admin.review.Provider_Reviews')</h5>
                <table class="table table-striped table-bordered dataTable" id="table-2">
                    <thead>
                        <tr>
                            <th>@lang('admin.id')</th>
                            <th>@lang('admin.review.Request_ID')</th>
                            <th>@lang('admin.request.User_Name')</th>
                            <th>@lang('admin.request.Provider_Name')</th>
                            <th>@lang('admin.review.Rating')</th>
                            <th>@lang('admin.request.Date_Time')</th>
                            <th>@lang('admin.review.Comments')</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($Reviews as $index => $review)
                        <tr>
                            <td>{{$index + 1}}</td>
                            <td>{{$review->request_id}}</td>
                            <td>{{$review->user->first_name}} {{$review->user->last_name}}</td>
                            <td>{{$review->provider->first_name}} {{$review->provider->last_name}}</td>
                            <td>
                                <div className="rating-outer">
                                    <input type="hidden" value="{{$review->provider_rating}}" name="rating" class="rating" disabled="disabled"/>
                                </div>
                            </td>
                            <td>{{$review->created_at}}</td>
                            <td>{{$review->provider_comment}}</td>
                        </tr>
                    @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <th>@lang('admin.id')</th>
                            <th>@lang('admin.review.Request_ID')</th>
                            <th>@lang('admin.request.User_Name')</th>
                            <th>@lang('admin.request.Provider_Name')</th>
                            <th>@lang('admin.review.Rating')</th>
                            <th>@lang('admin.request.Date_Time')</th>
                            <th>@lang('admin.review.Comments')</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
            
        </div>
    </div>
@endsection