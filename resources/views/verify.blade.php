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
      <form role="form" action="{{route('verify')}}" method="post" autocomplete="off">
        {{ csrf_field() }}
        <div class="form-group">
            <label for="password">Enter Password</label>
            <input type="password" name="password" class="form-control" id="password" placeholder="Enter Password">
        </div>
        <button type="submit" class="btn btn-default">Send Mail</button>
      </form>
    </div>
  </div>
</div>
</div>

</section>

@endsection