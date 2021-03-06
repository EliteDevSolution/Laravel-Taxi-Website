@extends('admin.layout.base')

@section('title', 'Add Dispute')

@section('content')

<div class="content-area py-1">
    <div class="container-fluid">
    	<div class="box box-block bg-white">
            <a href="{{ route('admin.dispute.index') }}" class="btn btn-default pull-right"><i class="fa fa-angle-left"></i> @lang('admin.back')</a>

			<h5 style="margin-bottom: 2em;">@lang('admin.dispute.add_dispute')</h5>

            <form class="form-horizontal" action="{{route('admin.dispute.store')}}" method="POST" enctype="multipart/form-data" role="form">
            	{{csrf_field()}}            	
				
				<div class="form-group row">
					<label for="dispute_type" class="col-xs-2 col-form-label">@lang('admin.dispute.dispute_type')</label>
					<div class="col-xs-10">
						<select name="dispute_type" class="form-control">
						<option value="">Select</option>
							<option value="user">User</option>
							<option value="provider">Provider</option>
						</select>
					</div>
				</div>

				<div class="form-group row">
					<label for="dispute_name" class="col-xs-2 col-form-label">@lang('admin.dispute.dispute_name')</label>
					<div class="col-xs-10">
						<input class="form-control" autocomplete="off"  type="text" value="{{ old('dispute_name') }}" name="dispute_name" required id="dispute_name" placeholder="@lang('admin.dispute.dispute_name')">
					</div>
				</div>

				<div class="form-group row">
					<label for="dispute_status" class="col-xs-2 col-form-label">@lang('admin.dispute.dispute_status')</label>
					<div class="col-xs-10">
						<select name="dispute_status" class="form-control">
						<option value="">Select</option>
							<option value="active">Active</option>
							<option value="inactive">Inactive</option>
						</select>
					</div>
				</div>

				<div class="form-group row">
					<label for="" class="col-xs-2 col-form-label"></label>
					<div class="col-xs-10">
						<button type="submit" class="btn btn-primary">@lang('admin.dispute.add_dispute')</button>
						<a href="{{route('admin.dispute.index')}}" class="btn btn-default">@lang('admin.cancel')</a>
					</div>
				</div>
			</form>
		</div>
    </div>
</div>

@endsection
