@extends('admin.layout.base')
<!-- @section('title', 'Dispatcher ') -->
@section('content')
<div class="content-area py-1">
    <div class="container-fluid">
        <h4>Dispatcher</h4>
        <div class="dispatch-head">
            <nav class="navbar navbar-light bg-white b-a mb-2">
                <button class="navbar-toggler hidden-md-up" data-toggle="collapse" data-target="#process-filters" aria-controls="process-filters" aria-expanded="false" aria-label="Toggle Navigation"></button>
                <form class="form-inline navbar-item ml-1 float-xs-right">
                    <div class="input-group">
                        <input type="text" class="form-control b-a" placeholder="Search for...">
                        <span class="input-group-btn">
                        		<button type="submit" class="btn btn-primary b-a"><i class="ti-search"></i></button>
                        	</span>
                    </div>
                </form>
                <ul class="nav navbar-nav float-xs-right">
                    @can('dispatcher-panel-add')
                    <li class="nav-item">
                        <button type="button" class="btn btn-success btn-md label-right b-a-0 waves-effect waves-light"><span class="btn-label"><i class="ti-plus"></i></span>ADD
                        </button>
                    </li>
                    @endcan
                </ul>
                <div class="collapse navbar-toggleable-sm" id="process-filters">
                    <ul class="nav navbar-nav dispatcher-nav">
                        <li class="nav-item"><span class="nav-link" href="#">Open</span></li>
                        <li class="nav-item"><span class="nav-link" href="#">Ongoing</span></li>
                        <li class="nav-item"><span class="nav-link" href="#">Scheduled</span></li>
                    </ul>
                </div>
            </nav>
        </div>
        <div class="dispatch-content row">
            <div class="col-md-4">
                <div class="card">
                    <div class="card card-block" id="create-ride">
                        <h3 class="card-title text-uppercase">Ride Details</h3>
                        <form id="form-create-ride" method="POST">
                            <div class="row">
                                <div class="col-xs-6">
                                <div class="form-group">
                                <label for="first_name">First Name</label>
                                <input type="text" class="form-control" name="first_name" id="first_name" placeholder="First Name" required="">
                            </div>
                        </div>
                        <div class="col-xs-6">
                            <div class="form-group">
                                <label for="last_name">Last Name</label>
                                <input type="text" class="form-control" name="last_name" id="last_name" placeholder="Last Name" required="">
                            </div>
                        </div>
                        <div class="col-xs-6">
                                <div class="form-group">
                                    <label for="email">Email</label>
                                    <input type="email" class="form-control" name="email" id="email" placeholder="Email" required="">
                                </div>
                            </div>
                            <div class="col-xs-6">
                                <div class="form-group">
                                    <label for="mobile">Phone</label>
                                    <input type="text" class="form-control" name="mobile" id="mobile" placeholder="Phone" required="">
                                </div>
                            </div>
                            <div class="col-xs-12">
                                <div class="form-group">
                                    <label for="s_address">Pickup Address</label>
                                    <input type="text" name="s_address" class="form-control" id="s_address" placeholder="Pickup Address" required="" autocomplete="off">
                                    <input type="hidden" name="s_latitude" id="s_latitude" value="">
                                    <input type="hidden" name="s_longitude" id="s_longitude" value=""></div>
                                    <div class="form-group">
                                        <label for="d_address">Dropoff Address</label>
                                        <input type="text" name="d_address" class="form-control" id="d_address" placeholder="Dropoff Address" required="" autocomplete="off">
                                        <input type="hidden" name="d_latitude" id="d_latitude" value="">
                                        <input type="hidden" name="d_longitude" id="d_longitude" value="">
                                        <input type="hidden" name="distance" id="distance" value="">
                                    </div>
                                    <div class="form-group">
                                        <label for="schedule_time">Schedule Date</label>
                                        <input type="text" class="form-control" id="datepicker-autoclose" placeholder="mm/dd/yyyy">

                                    </div>
                                    <div class="form-group">
                                        <label for="service_types">Service Type</label>
                                        <select name="service_type" class="form-control"></select>
                                    </div>
                                    <div class="form-group">
                                        <label for="provider_auto_assign">Auto Assign Provider</label>
                                        <br>
                                        <input type="checkbox" id="provider_auto_assign" name="provider_auto_assign" class="js-switch" data-color="#f59345" value="on" style="display: none;" data-switchery="true">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xs-6">
                                    <button type="button" class="btn btn-lg btn-danger btn-block waves-effect waves-light">CANCEL</button>
                                </div>
                                <div class="col-xs-6">
                                    <button class="btn btn-lg btn-success btn-block waves-effect waves-light">SUBMIT</button>
                                </div>
                            </div>
                        </form>
                    </div>                    
                </div>
            </div>
            <div class="col-md-8">
            	<div class="card my-card">
            		<div class="card-header text-uppercase"><b>MAP</b></div>
            		<div class="card-body">
            			<div style="width: 100%"><iframe width="100%" height="450" src="https://www.mapsdirections.info/en/custom-google-maps/map.php?width=100%&height=600&hl=ru&q=Malet%20St%2C%20London%20WC1E%207HU%2C%20United%20Kingdom+(Provider)&ie=UTF8&t=&z=14&iwloc=A&output=embed" frameborder="0" scrolling="no" marginheight="0" marginwidth="0"><a href="https://www.mapsdirections.info/en/custom-google-maps/">Embed Google Map</a> by <a href="https://www.mapsdirections.info/en/">Measure area on map</a></iframe></div><br />
            		</div>
            	</div>
            </div>
        </div>
    </div>
</div>
@endsection