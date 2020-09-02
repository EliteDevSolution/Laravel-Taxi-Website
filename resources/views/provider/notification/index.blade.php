
@extends('provider.layout.app')

@section('title', 'Notifications')

@section('content')

<div class="col-md-9">
<div class="dash-content">
<!-- notifications start-->
<div class="notify">
    <h2>notifications</h2>
    @foreach($notifications as $index => $notify)
    <div class="notify-sec">    
       <div class="row m-0 whlnot">
           <div class="notify-img no ">
           @if($notify->image) 
            <img src="{{$notify->image}}" class="img-responsive" alt="image">
             @else
             N/A
            @endif
            </div>
            <div class="notify-content">
                <h5>{{ date('F d, Y, h:i A', strtotime($notify->created_at)) }}</h5>
                <p>{{ str_limit($notify->description, $limit = 100, $end = '...') }}</p>
            </div>
       </div>
            
    </div>
     @endforeach     
</div>
<!-- notifications end-->
    </div>
    </div>
@endsection

@section('scripts')
<script type="text/javascript">
</script>

@endsection
