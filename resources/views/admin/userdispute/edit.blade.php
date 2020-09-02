@extends('admin.layout.base')

@section('title', 'Edit User Dispute')

@section('content')
<style>
.input-group{
	width: none;
}
.input-group .fa-search{
  display: table-cell;
}
</style>
<div class="content-area py-1">
    <div class="container-fluid">
    	<div class="box box-block bg-white">
            <a href="{{ route('admin.userdisputes') }}" class="btn btn-default pull-right"><i class="fa fa-angle-left"></i> @lang('admin.back')</a>

			<h5 style="margin-bottom: 2em;">@lang('admin.dispute.update_dispute')</h5>

            <form class="form-horizontal" action="{{route('admin.userdisputeupdate',$dispute->id)}}" method="POST" enctype="multipart/form-data" role="form">
            	{{csrf_field()}}
				
				<div class="form-group row">
					<label for="user" class="col-xs-2 col-form-label">@lang('admin.dispute.dispute_type')</label>
					<div class="col-xs-5">
						<input class="form-control" type="text" value="{{ ucfirst($dispute->dispute_type) }}" name="name" id="namesearch" disabled="">
						<input type="hidden" name="request_id" id="request_id" value="">
					</div>
				</div>		

				<div class="form-group row">
					<label for="user" class="col-xs-2 col-form-label">@lang('admin.dispute.dispute_user') / @lang('admin.dispute.dispute_provider')</label>
					
					<div class="col-xs-5">
						<input class="form-control" type="text" value="{{ $dispute->user->first_name }} {{ $dispute->user->last_name }}" name="name" id="namesearch" disabled="">
						<input type="hidden" name="request_id" id="request_id" value="">
					</div>
				</div>				

				<div class="form-group row">
					<label for="lost_item_name" class="col-xs-2 col-form-label">@lang('admin.lostitem.request')</label>
					<div class="col-xs-5">
		                <table class="table table-striped table-bordered dataTable requestList">
		                    <thead>
		                        <tr>
		                            <th>Request Id</th>
		                            <th>From </th>
		                            <th>To </th>
		                        </tr>
		                    </thead>
		                    <tbody>
		                   		<tr>
		                   			<td>{{ $dispute->request->booking_id }}</td>
		                   			<td>{{ $dispute->request->s_address }}</td>
		                   			<td>{{ $dispute->request->d_address }}</td>
		                   		</tr>
		                    </tbody>
		                   
		                </table>
					</div>
				</div>

				<div class="form-group row">
					<label for="lost_item_name" class="col-xs-2 col-form-label">@lang('admin.dispute.dispute_name')</label>
					<div class="col-xs-5">
						<textarea class="form-control" name="dispute_other" placeholder="@lang('admin.dispute.dispute_name')" disabled="">{{ $dispute->dispute_name }}</textarea>
					</div>
				</div>

				<div class="form-group row">
					<label for="lost_item_name" class="col-xs-2 col-form-label">@lang('admin.dispute.dispute_comments')</label>
					<div class="col-xs-5">
						<textarea class="form-control" name="comments" placeholder="@lang('admin.dispute.dispute_comments')" required="">{{ $dispute->comments }}</textarea>
					</div>
				</div>

				<div class="form-group row">
					<label for="lost_item_name" class="col-xs-2 col-form-label">@lang('admin.dispute.dispute_refund') ({{config('constants.currency', '$')}})</label>
					<div class="col-xs-5">
						<input class="form-control" type="number" value="{{ $dispute->refund_amount }}" name="refund_amount" id="refund_amount">
					</div>
				</div>

				<div class="form-group row">
					<label for="status" class="col-xs-2 col-form-label">@lang('admin.dispute.dispute_status')</label>
					<div class="col-xs-5">
					<input class="form-control" type="text" readonly value="closed" name="status">
					</div>
				</div>

				<div class="form-group row">
					<label for="" class="col-xs-2 col-form-label"></label>
					<div class="col-xs-5">
						<input type="hidden" name="is_admin" value="1" />
						<button type="submit" class="btn btn-primary">@lang('admin.dispute.update_dispute')</button>
						<a href="{{route('admin.userdisputes')}}" class="btn btn-default">@lang('admin.cancel')</a>
					</div>
				</div>
			</form>
		</div>
    </div>
</div>
<link href="{{ asset('asset/css/jquery-ui.css') }}" rel="stylesheet"> 
@endsection

@section('scripts')
<script type="text/javascript" src="{{ asset('asset/js/jquery-ui.js') }}"></script>

<script type="text/javascript">
var sflag='';
get_disputes('user');
$("#dispute_type").on('change', function(){
	$("#namesearch").val('');
	$('.requestList tbody').html('<tr><td colspan="4">No Results</td></tr>');
	get_disputes($(this).val());
	$("#dispute_other").hide();
	$("#dispute_other").attr('required', false);
});

$('#namesearch').autocomplete({
    source: function(request, response) {
    	var url='{{ route("admin.usersearch") }}';
    	sflag=0;
    	if($("#dispute_type").val()=='provider'){
    		sflag=1;
    		url='{{ route("admin.userprovider") }}';
    	}
	    $.ajax
	    ({
	        type: "GET",
	        url: url,
	        data: {stext:request.term},
	        dataType: "json",
	        success: function(responsedata, status, xhr)
	        {
	            if (!responsedata.data.length) {
	                var data=[];
	                data.push({
	                        id: 0,
	                        label:"@lang('No Records')"
	                });
	                response(data);
	            }
	            else{
	             response( $.map(responsedata.data, function( item ) {                          
	                    var name_alias=item.first_name+" - "+item.id;
	                  	$('#user_id').val(item.id);
	                    return {                                
	                        value: name_alias,
	                        id: item.id                             
	                    }
	                }));
	            }                   
	        }
	    });
	},
	minLength: 2,
	change:function(event,ui)
	{
	    if (ui.item==null){           
	        $("#namesearch").val('');
	        $("#namesearch").focus();	        
	    }
	    else{
	        if(ui.item.id==0){
	            $("#namesearch").val('');
	            $("#namesearch").focus();	            
	        }
	    }            
	},
	select: function (event, ui) {  

		$.ajax({
			url: "{{ route('admin.ridesearch') }}",
			type: 'post',
			data: {
				_token : '{{ csrf_token() }}',
				id: ui.item.id,
				sflag:sflag
			},
			success:function(data, textStatus, jqXHR) {
				var requestList = $('.requestList tbody');
				requestList.html(`<tr><td colspan="4">@lang('No Records')</td></tr>`);
				if(data.data.length > 0) {
					var result = data.data;
					for(var i in result) {
						requestList.html(`<tr><td>`+result[i].booking_id+`</td><td>`+result[i].s_address+`</td><td>`+result[i].d_address+`</td><td><input name="request_id" value="`+result[i].id+`" type="radio" /><input name="user_id" value="`+result[i].user_id+`" type="hidden" /><input name="provider_id" value="`+result[i].provider_id+`" type="hidden" /></td></tr>`);
					}
				} else {
					requestList.html(`<tr><td colspan="4">No Results</td></tr>`);
				}
			}
		});
  
	    $("#user_id1").val(ui.item.id);
	} 
});

function get_disputes(dispute_type){
	$.ajax({
		url: "{{ url('admin/disputelist') }}",
		type: 'get',
		data: {			
			dispute_type: dispute_type
		},
		success:function(data, textStatus, jqXHR) {
			$('#dispute_name').empty();
			$.each(data, function(key, value) {
			    $('#dispute_name').append($("<option/>", {
			        value: value.dispute_name,
			        text: value.dispute_name
			    }));
			});
			$('#dispute_name').append($("<option/>", {
		        value: 'others',
		        text: 'others'
			}));
			if(data.length > 0) {
				$("#dispute_other").hide();
				$("#dispute_other").attr('required', false);
			}
			else{
				$("#dispute_other").show();
				$("#dispute_other").attr('required', true);			
			}	
		}
	});
}

</script>    
@endsection