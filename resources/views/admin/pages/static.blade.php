@extends('admin.layout.base')

@section('title', 'Pages ')

@section('content')
<div class="content-area py-1">
    <div class="container-fluid">
        <div class="box box-block bg-white">
            @if(Setting::get('demo_mode', 0) == 1)
                <div class="col-md-12" style="height:50px;color:red;">
                    ** Demo Mode : @lang('admin.demomode')
                </div>
            @endif
            <h5>@lang('admin.pages.pages')</h5>

            <div className="row">
                 <form id="cmspages" action="{{ route('admin.pages.update') }}" method="POST">
                    <div class="form-group">
                        <select class="form-control" id="types" name="types">
                            <option value="select">---Select Option---</option>
                            <option value="help">Help</option>
                            <option value="page_privacy">Privacy Policy</option>
                            <option value="terms">Terms of Use</option>
                            <option value="cancel">Cancelation Policy</option>
                        </select>
                    </div>
                    {{ csrf_field() }}                    

                    <div class="row">
                        <div class="col-xs-12">
                            <textarea name="content" class="content" id="myeditor"></textarea>
                        </div>
                    </div>

                    <br>

                    <div class="row">
                        <div class="col-xs-12 col-md-3">
                            <a href="{{ route('admin.dashboard') }}" class="btn btn-danger btn-block">Cancel</a>
                        </div>
                        <div class="col-xs-12 col-md-3 offset-md-6">
                            <button type="submit" class="btn btn-primary btn-block">Update</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="//cdn.ckeditor.com/4.6.2/standard/ckeditor.js"></script>
<script type="text/javascript">
    CKEDITOR.replace('myeditor');
</script>
<script type="text/javascript">
    @if(Setting::get('demo_mode', 0) == 1) 
        $("#cmspages :input").prop("disabled", true);
        $("#types").prop("disabled", false);
    @endif

   $(document).ready(function(){
    $("#types").change(function(){
        var types = $("#types").val();
        
        $.ajaxSetup({
                headers: { 'X-CSRF-TOKEN': $('meta[name="_toke-n"]').attr('content') }
            });

        $.ajax({url: "{{ url('admin/pages/search') }}/"+types, 
            success: function(data) {
               // $('#doc_title').val("");
               //alert(data);
               CKEDITOR.instances["myeditor"].setData(data)
                //$('#myeditor').val("data");
                //document.getElementById("myeditor").value = "blah ... "
                //$(".content").val(data);
                 //$("#myeditor").hide();
                //$("#myeditor").hide();
                 // $("#myeditor").append("<textarea id='myeditor'   name='content' >"+data+"</textarea> </br> ");
            }});

        
        
    });
});
</script>

@endsection