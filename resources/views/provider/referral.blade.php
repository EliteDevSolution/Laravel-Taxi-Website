@extends('provider.layout.app')

@section('title', 'Refer a Friend ')

@section('content')
<div class="pro-dashboard-head">
    <div class="container">
        <a href="#" class="pro-head-link active">@lang('provider.profile.profile')</a>
        <a href="{{ route('provider.documents.index') }}" class="pro-head-link">@lang('provider.profile.manage_documents')</a>
        <a href="{{ route('provider.location.index') }}" class="pro-head-link">@lang('provider.profile.update_location')</a>        
        <a href="{{route('provider.wallet.transation')}}" class="pro-head-link">@lang('provider.profile.wallet_transaction')</a>
        @if(config('constants.card')==1)
            <a href="{{ route('provider.cards') }}" class="pro-head-link">@lang('provider.card.list')</a>
        @endif
        <a href="{{ route('provider.transfer') }}" class="pro-head-link">@lang('provider.profile.transfer')</a>
        @if(config('constants.referral')==1)
            <a href="{{ route('provider.referral') }}" class="pro-head-link">@lang('provider.profile.refer_friend')</a>
        @endif
    </div>
</div>
 <div class="profile-content gray-bg pad50">
    <div class="container">
        <div class="dash-content">
            <div class="row no-margin">
                <div class="col-md-12">
                    <h4 class="page-title">@lang('provider.profile.refer_friend')</h4>
                </div>
            </div>
           
            <div class="row no-margin edit-pro">
               <div class="col-md-8 wallet">
                 <div class="refer-box">
                    <h4>
                        Share Your Referral Code : 
                        <span class="txt">
                        @if(!empty(Auth::user()->referral_unique_id)){{Auth::user()->referral_unique_id}}@else - @endif
                        </span>
                    </h4>
                    <h4>
                        Referral Count : 
                        <span class="txt">
                        @if(!empty($referrals[0]->total_count)){{$referrals[0]->total_count}}@else 0 @endif
                        </span>
                    </h4>
                    <h4>
                        Referral Amount : 
                        <span class="txt">
                        @if(!empty($referrals[0]->total_amount)){{$referrals[0]->total_amount}}@else 0 @endif
                        </span>
                    </h4>
                    </div>
                </div>
               
            </div>
            <div class="row">
            <div class="col-md-12">
               
               <div class="col-md-8">
                <div class="refer-box">
                    <h3>Refer Your Friends & Earn upto 20%</h3>
               <p>Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa.</p>
                   <form>
                    <div class="clearfix form-row">
  <div class="form-group col-md-10">
    <label for="exampleInputEmail1">Email address</label>
    <input type="email" class="form-control" id="inviteEmail" aria-describedby="emailHelp" placeholder="Enter email">
    </div>
  </div>
   <div class="form-row clearfix">
  <div class="form-group col-md-4">
    <label for="exampleInputEmail1"></label>
    <a id="invite" href="mailto:testmail?subject=Invitation to join {{config('constants.site_title','Tranxit')}}&body=Hi,%0A%0A I found this website and thought you might like it. Use my referral code({{\Auth::guard('provider')->user()->referral_unique_id}}) on registering in the application.%0A%0AWebsite: {{url('/')}}/provider/login %0AReferral Code: {{\Auth::guard('provider')->user()->referral_unique_id}}" class="btn btn-invite">Invite</a> 
</div>
</div>
</form>
                      
                </div>

                <div class="refer-box">
                     <h3>Refer Your Friends via Social Media</h3>
<div class="refer-social">
    <div class="row">
        <div class="col-md-12">
            <ul class="refersocial-icon">
                <li><a class="" target="_blank" href="https://www.facebook.com/share?url"><i class="fa fa-2x fa-facebook-official" aria-hidden="true"></i>
</a></li>
<li><a class="" target="_blank" href="https://twitter.com/share?url"><i class="fa fa-2x fa-twitter-square" aria-hidden="true"></i>
</a></li>

            </ul>
        </div>
    </div>
</div> 
                </div>
               </div>
            </div>
        </div>
        </div>
        
    </div>
</div>

@endsection
@section('scripts')
<script type="text/javascript">
    $('#invite').on('click', function(e){
      e.preventDefault();
      var href = $('#invite').attr('href');
      var start = href.indexOf(":");
      var end = href.indexOf("?");
      var email = $('#inviteEmail').val();
      href.substr(start+1, (end-start)-1);
      var url = href.replace(href.substr(start+1, (end-start)-1), email);
      window.location = url;
    });
</script>
@endsection