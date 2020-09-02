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
                        <li class="nav-item"><span class="nav-link" href="#">Searching</span></li>
                        <li class="nav-item active"><span class="nav-link" href="#">Cancelled</span></li>
                        <li class="nav-item "><span class="nav-link" href="#">Ongoing</span></li>
                        <li class="nav-item "><span class="nav-link" href="#">Scheduled</span></li>
                    </ul>
                </div>
            </nav>
        </div>
        <div class="dispatch-content row">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header text-uppercase"><b>Cancelled List</b></div>
                    <div class="items-list">
                    	<!-- Item Block Starts -->
                        <div class="il-item">
                            <a class="text-black" href="#">
                                <div class="media">
                                    <div class="media-body">
                                        <p class="mb-0-5">
                                        	Paul Wesley
                                            <span class="tag tag-danger pull-right">CANCELLED</span>
                                       	</p>
                                        <h6 class="media-heading">From: Florida, United States</h6>
                                        <h6 class="media-heading">To: JF, Shirley Ann Trail, Lakeland, Florida, United States</h6>
                                        <h6 class="media-heading">Payment: CASH</h6>
                                        <span class="text-muted">Manual Assignment : 2017-10-24 16:51:15</span>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <!-- Item Block Ends -->
                        <!-- Item Block Starts -->
                        <div class="il-item">
                            <a class="text-black" href="#">
                                <div class="media">
                                    <div class="media-body">
                                        <p class="mb-0-5">
                                        	Paul Wesley
                                            <span class="tag tag-danger pull-right">CANCELLED</span>
                                       	</p>
                                        <h6 class="media-heading">From: Florida, United States</h6>
                                        <h6 class="media-heading">To: JF, Shirley Ann Trail, Lakeland, Florida, United States</h6>
                                        <h6 class="media-heading">Payment: CASH</h6>
                                        <span class="text-muted">Manual Assignment : 2017-10-24 16:51:15</span>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <!-- Item Block Ends -->
                        <!-- Item Block Starts -->
                        <div class="il-item">
                            <a class="text-black" href="#">
                                <div class="media">
                                    <div class="media-body">
                                        <p class="mb-0-5">
                                        	Paul Wesley
                                            <span class="tag tag-danger pull-right">CANCELLED</span>
                                       	</p>
                                        <h6 class="media-heading">From: Florida, United States</h6>
                                        <h6 class="media-heading">To: JF, Shirley Ann Trail, Lakeland, Florida, United States</h6>
                                        <h6 class="media-heading">Payment: CASH</h6>
                                        <span class="text-muted">Manual Assignment : 2017-10-24 16:51:15</span>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <!-- Item Block Ends -->
                        <!-- Item Block Starts -->
                        <div class="il-item">
                            <a class="text-black" href="#">
                                <div class="media">
                                    <div class="media-body">
                                        <p class="mb-0-5">
                                        	Paul Wesley
                                            <span class="tag tag-danger pull-right">CANCELLED</span>
                                       	</p>
                                        <h6 class="media-heading">From: Florida, United States</h6>
                                        <h6 class="media-heading">To: JF, Shirley Ann Trail, Lakeland, Florida, United States</h6>
                                        <h6 class="media-heading">Payment: CASH</h6>
                                        <span class="text-muted">Manual Assignment : 2017-10-24 16:51:15</span>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <!-- Item Block Ends -->
                        <!-- Item Block Starts -->
                        <div class="il-item">
                            <a class="text-black" href="#">
                                <div class="media">
                                    <div class="media-body">
                                        <p class="mb-0-5">
                                        	Paul Wesley
                                            <span class="tag tag-danger pull-right">CANCELLED</span>
                                       	</p>
                                        <h6 class="media-heading">From: Florida, United States</h6>
                                        <h6 class="media-heading">To: JF, Shirley Ann Trail, Lakeland, Florida, United States</h6>
                                        <h6 class="media-heading">Payment: CASH</h6>
                                        <span class="text-muted">Manual Assignment : 2017-10-24 16:51:15</span>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <!-- Item Block Ends -->
                        <!-- Item Block Starts -->
                        <div class="il-item">
                            <a class="text-black" href="#">
                                <div class="media">
                                    <div class="media-body">
                                        <p class="mb-0-5">
                                        	Paul Wesley
                                            <span class="tag tag-danger pull-right">CANCELLED</span>
                                       	</p>
                                        <h6 class="media-heading">From: Florida, United States</h6>
                                        <h6 class="media-heading">To: JF, Shirley Ann Trail, Lakeland, Florida, United States</h6>
                                        <h6 class="media-heading">Payment: CASH</h6>
                                        <span class="text-muted">Manual Assignment : 2017-10-24 16:51:15</span>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <!-- Item Block Ends -->
                        <!-- Item Block Starts -->
                        <div class="il-item">
                            <a class="text-black" href="#">
                                <div class="media">
                                    <div class="media-body">
                                        <p class="mb-0-5">
                                        	Paul Wesley
                                            <span class="tag tag-danger pull-right">CANCELLED</span>
                                       	</p>
                                        <h6 class="media-heading">From: Florida, United States</h6>
                                        <h6 class="media-heading">To: JF, Shirley Ann Trail, Lakeland, Florida, United States</h6>
                                        <h6 class="media-heading">Payment: CASH</h6>
                                        <span class="text-muted">Manual Assignment : 2017-10-24 16:51:15</span>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <!-- Item Block Ends -->
                    </div>
                </div>
            </div>
            <div class="col-md-8">
            	<div class="card my-card">
            		<div class="card-header text-uppercase"><b>MAP</b></div>
            		<div class="card-body">
            			<div id="map" style="height: 450px;"></div>
            		</div>
            	</div>
            </div>
        </div>
    </div>
</div>
@endsection