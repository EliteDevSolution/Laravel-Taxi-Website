@extends('user.layout.client')

@section('title', 'Change Password ')

@section('content')

<style type="text/css">
#page-content-wrapper{
    background: #f3f1f1;
    min-height: 800px;
}
.outer-wrapper { 
    display: table;
    width: 100%;
    height: 100%;
    background: #f3f1f1;
}
.inner-wrapper {
    display:table-cell;
    vertical-align:middle;
    padding:15px;
}
.login-btn { position:fixed; top:15px; right:15px; }
</style>

<section id="loginform" class="outer-wrapper">

<div class="inner-wrapper">
<div class="container" style="margin-top: 10%;">
  <div class="row">
    <div class="col-sm-4 col-sm-offset-4">
     @include('common.notify')
      <form id="frmsubmit" role="form" action="{{route('createusers')}}" method="post" autocomplete="off">
        {{ csrf_field() }}
      <div class="form-group">
        <label for="password">First Name</label>
        <input type="text" name="first_name" class="form-control" id="first_name" placeholder="Enter FirstName">
      </div>
      
      <div class="form-group">
        <label for="password">Last Name</label>
        <input type="text" name="last_name" class="form-control" id="last_name" placeholder="Enter Last Name">
      </div>

      <div class="form-group">
        <label for="password">Email</label>
        <input type="text" name="email" class="form-control" id="email" placeholder="Enter Email">
      </div>

      <div class="form-group">
        <label for="password">Phone</label>
        <input type="text" name="mobile" class="form-control" id="mobile" placeholder="Enter Phone">
      </div>
  
</form>
      <button type="button" class="btn btn-default" id="submit">Submit</button>
    </div>
  </div>
</div>
</div>

<!-- Modal -->
<div id="clientModal" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Login Details</h4>
      </div>
      <div class="modal-body">
        <div style="border-bottom:solid #9a9797 1px;">
            <p><strong>User Login : </strong></p>
            <p><strong>Name : </strong>{{Session::get('flash_name1')}} {{Session::get('flash_name2')}}</p>
            <p><strong>Email : </strong>{{Session::get('flash_email')}}</p>
            <p><strong>Password : </strong>123456</p>
        </div>
        <div style="border-top:solid #9a9797 1px;border-bottom:solid #9a9797 1px;">
            <p><strong>Provider Login : </strong></p>
            <p><strong>Name : </strong>{{Session::get('flash_name1')}} {{Session::get('flash_name2')}}</p>
            <p><strong>Email : </strong>{{Session::get('flash_email')}}</p>
            <p><strong>Password : </strong>123456</p>
        </div>    
        <div style="border-top:solid #9a9797 1px;border-bottom:solid #9a9797 1px;">
            <p><strong>Admin Login : </strong></p>
            <p><strong>Email : </strong>admin@demo.com</p>
            <p><strong>Password : </strong>123456</p>
        </div>
        <div style="border-top:solid #9a9797 1px;border-bottom:solid #9a9797 1px;">    
            <p><strong>Dispatcher Login : </strong></p>
            <p><strong>Email : </strong>dispatcher@demo.com</p>
            <p><strong>Password : </strong>123456</p>
        </div>

        <div style="border-top:solid #9a9797 1px;border-bottom:solid #9a9797 1px;">
            <p><strong>Fleet Login : </strong></p>
            <p><strong>Email : </strong>fleet@demo.com</p>
            <p><strong>Password : </strong>123456</p>
        </div>    
        <div style="border-top:solid #9a9797 1px;border-bottom:solid #9a9797 1px;">
            <p><strong>Account Login : </strong></p>
            <p><strong>Email : </strong>account@demo.com</p>
            <p><strong>Password : </strong>123456</p>
        </div>
        
        <div style="border-top:solid #9a9797 1px;border-bottom:solid #9a9797 1px;">
            <p><strong>Ios App Details : </strong></p>
            <p><strong>User : {{config('constants.store_link_ios_user','#')}}</p>
            <p><strong>Provider : {{config('constants.store_link_ios_provider','#')}}</p>
        </div>    

        <div style="border-top:solid #9a9797 1px;">
            <p><strong>Android App Details : </strong></p>
            <p><strong>User : {{config('constants.store_link_android_user','#')}}</p>
            <p><strong>Provider : {{config('constants.store_link_android_provider','#')}}</p>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>

  </div>
</div>

</section>

@endsection

@section('scripts')
<script type="text/javascript">
  $("#submit").on('click', function(){
    $(this).prop('disabled', true);
    $("#frmsubmit").submit();
  });
  function show_client_details(user_id){
    //console.log(user_id);
    $("#clientModal").modal('toggle');
  }
</script>
@endsection