@extends('account.layout.base')

@section('title', $page)

@section('content')

    <div class="content-area py-1">
        <div class="container-fluid">
            <div class="box box-block bg-white">
            	<h3>{{$page}}</h3>

            	<div class="row">

						<div class="row row-md mb-2" style="padding: 15px;">
							<div class="col-md-12">
									<div class="box bg-white">
										<div class="box-block clearfix">
											<h5 class="float-xs-left">@lang('admin.include.user_ride_histroy')</h5>
											<div class="float-xs-right">
											</div>
										</div>

										@if(count($Users) != 0)
								            <table class="table table-striped table-bordered dataTable" id="table-4">
								                <thead>
								                   <tr>
														<td>@lang('admin.fleets.name')</td>
														<td>@lang('admin.mobile')</td>
														<td>@lang('admin.fleets.Total_Rides')</td>
														<td>@lang('admin.fleets.Total_Earnings')</td>
														<td>@lang('admin.fleets.Joined_at')</td>
														<td>@lang('admin.fleets.Details')</td>
													</tr>
								                </thead>
								                <tbody>
								                <?php $diff = ['-success','-info','-warning','-danger']; ?>
														@foreach($Users as $index => $user)
															<tr>
																<td>
																	{{$user->first_name}} 
																	{{$user->last_name}}
																</td>
																<td>
																	{{$user->mobile}}
																</td>
																
																<td>
																	@if($user->rides_count)
																		{{$user->rides_count}}
																	@else
																	 	-
																	@endif
																</td>
																<td>
																	@if($user->payment)
																		{{currency($user->payment[0]->overall)}}
																	@else
																	 	-
																	@endif
																</td>
																<td>
																	@if($user->created_at)
																		<span class="text-muted">{{$user->created_at->diffForHumans()}}</span>
																	@else
																	 	-
																	@endif
																</td>
																<td>
																	<a href="{{route('admin.provider.statement', $user->id)}}">Ride Histroy</a>
																</td>
															</tr>
														@endforeach
															
								                <tfoot>
								                    <tr>
														<td>@lang('admin.fleets.name')</td>
														<td>@lang('admin.mobile')</td>
														<td>@lang('admin.fleets.Total_Rides')</td>
														<td>@lang('admin.fleets.Total_Earnings')</td>
														<td>@lang('admin.fleets.Joined_at')</td>
														<td>@lang('admin.fleets.Details')</td>
													</tr>
								                </tfoot>
								            </table>
								            @include('common.pagination')
								            @else
								            <h6 class="no-result">No results found</h6>
								            @endif 

									</div>
								</div>

							</div>

            	</div>

            </div>
        </div>
    </div>

@endsection
