@extends('admin.layout.base')

@section('title', 'Add Peak Hour')

@section('content')
<link rel="stylesheet" href="{{ asset('asset/css/bootstrap-material-datetimepicker.css') }}" />
<link href='https://fonts.googleapis.com/css?family=Roboto:400,500' rel='stylesheet' type='text/css'>
<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
<div class="content-area py-1">
    <div class="container-fluid">
    	<div class="box box-block bg-white">
            <a href="{{ route('admin.peakhour.index') }}" class="btn btn-default pull-right"><i class="fa fa-angle-left"></i> @lang('admin.back')</a>

			<h5 style="margin-bottom: 2em;">@lang('admin.peakhour.add_time')</h5>

            <form class="form-horizontal" action="{{route('admin.peakhour.store')}}" method="POST" enctype="multipart/form-data" role="form">
            	{{csrf_field()}}            	
				
				<div class="form-group row">
					<label for="start_time" class="col-xs-2 col-form-label">@lang('admin.peakhour.start_time')</label>
					<div class="col-xs-10">
						<input class="form-control" autocomplete="off"  type="text" value="{{ old('start_time') }}" name="start_time"  id="start_time" placeholder="@lang('admin.peakhour.start_time')">
					</div>
				</div>

				<div class="form-group row">
					<label for="end_time" class="col-xs-2 col-form-label">@lang('admin.peakhour.end_time')</label>
					<div class="col-xs-10">
						<input class="form-control" autocomplete="off"  type="text" value="{{ old('end_time') }}" name="end_time"  id="end_time" placeholder="@lang('admin.peakhour.end_time')">
					</div>
				</div>

				<div class="form-group row">
					<label for="" class="col-xs-2 col-form-label"></label>
					<div class="col-xs-10">
						<button type="submit" class="btn btn-primary">@lang('admin.peakhour.add_time')</button>
						<a href="{{route('admin.peakhour.index')}}" class="btn btn-default">@lang('admin.cancel')</a>
					</div>
				</div>
			</form>
		</div>
    </div>
</div>

@endsection

@section('scripts')
<script type="text/javascript" src="{{asset('asset/js/moment.js')}}"></script>
<script type="text/javascript" src="{{asset('asset/js/bootstrap-material-datetimepicker.js')}}"></script>

<script type="text/javascript">
$(document).ready(function()
{    
    $('#start_time').bootstrapMaterialDatePicker({  
        format: 'hh:mm A' ,
        date: false,
     });
    $('#end_time').bootstrapMaterialDatePicker({  
        format: 'hh:mm A' ,
        date: false,
     });

});  
</script>
@endsection