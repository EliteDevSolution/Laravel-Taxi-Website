@extends('admin.layout.base')

@section('title', 'Update Reason ')

@section('content')
<div class="content-area py-1">
    <div class="container-fluid">
    	<div class="box box-block bg-white">
    	    <a href="{{ route('admin.reason.index') }}" class="btn btn-default pull-right"><i class="fa fa-angle-left"></i> @lang('admin.back')</a>

			<h5 style="margin-bottom: 2em;">@lang('admin.reason.update_reason')</h5>

            <form class="form-horizontal" action="{{route('admin.reason.update', $reason->id )}}" method="POST" enctype="multipart/form-data" role="form">
            	{{csrf_field()}}
            	<input type="hidden" name="_method" value="PATCH">
				<div class="form-group row">
					<label for="type" class="col-xs-2 col-form-label">@lang('admin.reason.type')</label>
					<div class="col-xs-10">
						<select class="form-control" name="type" id="type">
							<option value="USER" @if($reason->type=='USER')selected @endif>USER</option>
							<option value="PROVIDER" @if($reason->type=='PROVIDER')selected @endif>PROVIDER</option>
						</select>
					</div>
				</div>
				
				<div class="form-group row">
					<label for="reason" class="col-xs-2 col-form-label">@lang('admin.reason.reason')</label>
					<div class="col-xs-10">
						<input class="form-control" autocomplete="off"  type="text" value="{{ $reason->reason }}" name="reason" required id="reason" placeholder="Reason">
					</div>
				</div>
				
				<div class="form-group row">
					<label for="max_amount" class="col-xs-2 col-form-label">@lang('admin.reason.status')</label>
					<div class="col-xs-10">
						<select class="form-control" name="status" id="status">
							<option value="1" @if($reason->status==1)selected @endif>Active</option>
							<option value="0" @if($reason->status==0)selected @endif>Inactive</option>
						</select>
					</div>
				</div>


				
				<div class="form-group row">
					<label for="" class="col-xs-2 col-form-label"></label>
					<div class="col-xs-10">
						<button type="submit" class="btn btn-primary">@lang('admin.reason.update_reason')</button>
						<a href="{{route('admin.reason.index')}}" class="btn btn-default">@lang('admin.cancel')</a>
					</div>
				</div>
			</form>
		</div>
    </div>
</div>

@endsection


