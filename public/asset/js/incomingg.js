'use strict';

function interval(){
    clearInterval(this.updateInterval);
}

class MainComponent extends React.Component {
    componentWillMount() {
        //console.log('Mounting');
        this.setState({
            latitude: 0,
            longitude: 0,
            service_status: [],
            account_status: [],
            reasons: [],
            request: {
                user: {
                    picture: '/asset/logo.png',
                    first_name: 'John',
                    last_name: 'Doe'
                },
            }
        });

        this.updateInterval = setInterval(
            () => this._requestPoll(),
            3000
        );

        interval = interval.bind(this);
    }

    componentDidMount() {
        this._requestPoll();
    }

    _requestPoll(){
        //console.log('Polling');
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function (position) {
                this.setState({latitude: position.coords.latitude, longitude: position.coords.longitude});
            }.bind(this));
        }

        $.ajax({
            url: '/provider/incoming',
            dataType: "JSON",
            headers: {'X-CSRF-TOKEN': window.Laravel.csrfToken },
            data: {
                latitude: this.state.latitude,
                longitude: this.state.longitude
            },
            type: "GET",
            success: function(data){
                // this.setState({account_status: data.account_status});
                // this.setState({service_status: data.service_status});
                //console.log('Ajax Response', data);
                if(data.requests.length > 0) {
                    request=1
                    // console.log('data.requests[0]', data.requests[0].request);
                    this.setState({request: data.requests[0].request, reasons: data.reasons,rideOtp: data.ride_otp});
                   
                }else{

                    if($('#incoming').is(':visible')) {
                        window.location.replace("/provider");
                    }
                    if(request==1){
                        localStorage.setItem("cancelled", 1);
                        window.location.replace("/provider");
                    }
                    //console.log(request);
                    this.setState({account_status: data.account_status, service_status: data.service_status, rideOtp: data.ride_otp});
                }
                    //console.log('ss'+this.state.service_status);

            }.bind(this)
        });
    }
    render() {
        //console.log('Trip Prop: ', this.props.trip);
        if(this.props.trip == "true") {
            var location = { latitude: this.state.latitude, longitude: this.state.longitude };
            return (
                <div> 
                    <ModalComponent request={this.state.request} reasons={this.state.reasons} />
                    <TripComponent request={this.state.request} reasons={this.state.reasons} rideOtp={this.state.rideOtp} service_status={this.state.service_status} account_status={this.state.account_status} location={location} />
                </div>
            );
        } else {
            return (
                <div> 
                    <ModalComponent request={this.state.request} />
                </div>
            );
        }
    }
};

class ModalComponent extends React.Component {

    componentDidUpdate(prevProps, prevState) {
        //console.log('Modal Component Updated');
        if(this.props.request.status == "SEARCHING") {
            this._open();
        }
    }

    _accept(event) {
        event.preventDefault();
        //console.log('Accept');
        $.ajax({
            url: '/provider/request/'+this.props.request.id,
            dataType: 'json',
            headers: {'X-CSRF-TOKEN': window.Laravel.csrfToken },
            type: 'POST',
            success: function(data) {
                //console.log('Accept', data);
                if(data.error == undefined) {
                    window.location.replace("/provider");
                }
                this._close();
            }.bind(this),
            error: function(xhr, status, err) {
                //console.error(this.props.url, status, err.toString());
            }.bind(this)
        });
        this._close();
    }

    _reject(event) {
        event.preventDefault();
        //console.log('Reject');
        $.ajax({
            url: '/provider/request/'+this.props.request.id,
            dataType: 'json',
            type: 'DELETE',
            headers: {'X-CSRF-TOKEN': window.Laravel.csrfToken },
            success: function(data) {
                //console.log('Reject', data);
                if(data.error == undefined) {
                    localStorage.setItem("cancelled", 1);
                    window.location.replace("/provider");
                }
                this._close();
            }.bind(this),
            error: function(xhr, status, err) {
                console.error(this.props.url, status, err.toString());
            }.bind(this)
        });
        this._close();
    }

    _open() {
        //console.log('Modal Show');
        $("#incoming").modal('show');
    }

    _close() {
        //console.log('Modal Hide');
        $("#incoming").hide('hide');
    }

    render() {
        return (
            <div className="modal fade" id="incoming" role="dialog">
                <div className="modal-dialog" role="document">
                    <div className="modal-content">
                        <div className="modal-header">
                            <h4 className="modal-title text-center incoming-tit" id="myModalLabel">Incoming Request</h4>
                        </div>
                        <div className="modal-body">
                            <div className="incoming-img bg-img" id="user-image" style={{ backgroundImage: 'url(' + assetBaseUrl+this.props.request.user.picture + ')' }}></div>
                            <div className="text-center">
                                <h3 id="usser-name">{this.props.request.user.first_name} {this.props.request.user.last_name}</h3>
                                {this.props.request.schedule_at ?
                                    <h5>Scheduled At : {this.props.request.schedule_at}</h5>
                                    : ""
                                }
                            </div>
                        </div>
                        <div className="modal-footer row no-margin">
                            <button type="button" className="btn btn-primary incoming-btn" onClick={this._accept.bind(this)}>Accept</button>
                            <button type="button" className="btn btn-default incoming-btn" onClick={this._reject.bind(this)} data-dismiss="modal">Cancel</button>
                        </div>
                    </div>
                </div>
            </div>
        );
    }
};

ModalComponent.defaultProps = {
    request: {
        user: {
            first_name: "John",
            last_name: "Doe",
            picture: "/asset/logo.png",
        }
    }
};

class TripEmptyActive extends React.Component {
    componentDidMount(){
        initMap();
    }
    constructor(props) {
        super(props);
        this.state = {
            offline: offline
        };
    }
    render() {
        return (
            <div className="row no-margin">
                <div className="col-md-12">
                    <form method="POST" action="provider/profile/available">
                        <input type="hidden" value="offline" name="service_status"/>
                        <div id="map" style={{ width: '100%', height: '425px' }}></div>
                        <button type="submit" className="full-primary-btn fare-btn">{this.state.offline}</button>
                    </form>
                </div>
            </div>
        );
    }
};

class TripEmptyOffline extends React.Component {
    constructor(props) {
        super(props);
        this.state = {
            online: online
        };
    }
    render() {
        return (
            <div className="row no-margin">
                <div className="col-md-12">
                    <form method="POST" action="provider/profile/available">
                        <input type="hidden" value="active" name="service_status"/>
                        <div className="offline">
                            <img src="/asset/img/offline.gif"/>
                        </div>
                        <button type="submit" className="full-primary-btn fare-btn">{this.state.online}</button>
                    </form>
                </div>
            </div>
        );
    }
};

class TripArrivedButton extends React.Component {
    
    constructor(props) {
        super(props);
        if(this.props.reasons.length>0)
            this.state = {reason: this.props.reasons[0].reason};
        else
            this.state = {reason: 'ot'};

        this.handleCancelReason = this.handleCancelReason.bind(this);
    }

    handleCancelButton(event) {
        this.props.cancel(this.state.reason);
    }

    handleCancelReason(event) {
        this.setState({reason: event.target.value});
    }

    render() {
        var optionshtml = [];
        for (var i = 0; i < this.props.reasons.length; i++) {           
          optionshtml.push(<option value={this.props.reasons[i].reason}>{this.props.reasons[i].reason}</option>);
        }
        var textareahtml=[];
        if(optionshtml.length>0){
            textareahtml.push(<textarea className="form-control cancel_hide" id="cancel_text" name="cancel_reason1" placeholder="Cancel Reason"></textarea>);
        }
        else{
            textareahtml.push(<textarea className="form-control" id="cancel_text" name="cancel_reason1" placeholder="Cancel Reason"></textarea>);
        }        
        return (
            <div>
                <button type="submit" className="full-primary-btn fare-btn" onClick={this.props.submit.bind(this)}>Arrived</button>
                <div id="cancel-reason" className="modal fade" role="dialog">
                    <div className="modal-dialog">
                        <div className="modal-content">
                            <div className="modal-header">
                                <button type="button" className="close" data-dismiss="modal">&times;</button>
                                <h4 className="modal-title">Cancel Reason</h4>
                            </div>
                            <div className="modal-body">
                                <div class="reasonvalidate alert-danger">Reason required!</div>
                                <select className="form-control" name="cancel_reason" id="cancel_reason" value={this.state.reason} onChange={this.handleCancelReason}>
                                  {optionshtml}
                                    <option value="ot">Others</option>
                                </select>
                                {textareahtml}
                            </div>
                            <div className="modal-footer">
                                <button type="submit" className="full-primary-btn fare-btn reg-btn" onClick={this.handleCancelButton.bind(this)}>Cancel</button>
                            </div>
                        </div>
                    </div>
                </div>
                <button type="button" className="full-primary-btn fare-btn reg-btn" data-toggle="modal" data-target="#cancel-reason">Cancel</button>
            </div>
        );
    }    
}

class TripPickedButton extends React.Component {

    constructor(props) {
        super(props);
        
        if(this.props.reasons.length>0)
            this.state = {reason: this.props.reasons[0].reason};
        else
            this.state = {reason: 'ot'};

        this.handleCancelReason = this.handleCancelReason.bind(this);
    }

    handleCancelButton(event) {
        this.props.cancel(this.state.reason);
    }

    handleCancelReason(event) {
        this.setState({reason: event.target.value});
    }

    render() {
        var optionshtml = [];
        for (var i = 0; i < this.props.reasons.length; i++) {           
          optionshtml.push(<option value={this.props.reasons[i].reason}>{this.props.reasons[i].reason}</option>);
        }
        var textareahtml=[];
        if(optionshtml.length>0){
            textareahtml.push(<textarea className="form-control cancel_hide" id="cancel_text" name="cancel_reason1" placeholder="Cancel Reason"></textarea>);
        }
        else{
            textareahtml.push(<textarea className="form-control" id="cancel_text" name="cancel_reason1" placeholder="Cancel Reason"></textarea>);
        } 
        var otp=[];
        //   console.log(this.props);
        if(this.props.rideOtp ==1){
            otp.push(<dl class="dl-horizontal left-right"><dt>Otp</dt><dd><input id ="otp" name="otp" class="form-control" type="text" /></dd></dl>);
        }
        return (
            <div>
                
                 {otp}
                <button type="submit" className="full-primary-btn fare-btn" onClick={this.props.submit.bind(this)}>Picked Up</button>
                <div id="cancel-reason" className="modal fade" role="dialog">
                    <div className="modal-dialog">
                        <div className="modal-content">
                            <div className="modal-header">
                                <button type="button" className="close" data-dismiss="modal">&times;</button>
                                <h4 className="modal-title">Cancel Reason</h4>
                            </div>
                            <div className="modal-body">
                                <div class="reasonvalidate alert-danger">Reason required!</div>
                                <select className="form-control" name="cancel_reason" id="cancel_reason" value={this.state.reason} onChange={this.handleCancelReason}>
                                  {optionshtml}
                                    <option value="ot">Others</option>
                                </select>
                                {textareahtml}
                            </div>
                            <div className="modal-footer">
                                <button type="submit" className="full-primary-btn fare-btn reg-btn" onClick={this.handleCancelButton.bind(this)}>Cancel</button>
                            </div>
                        </div>
                    </div>
                </div>
                <button type="button" className="full-primary-btn fare-btn reg-btn" data-toggle="modal" data-target="#cancel-reason">Cancel</button>
            </div>
        );
    }
}

class TripDroppedButton extends React.Component {

    constructor(props) {
        super(props);
        this.state = {
            currency: currency
        };
    }
    render() {
        return (
            <div>
             <dl className="dl-horizontal left-right" id="waiting_div" data-status="0" data-id={this.props.request}><dt>Waiting Time</dt><dd><input type="checkbox" className="js-switch" data-color="#43b968" id="waiting_time" /></dd></dl>
             <dl className="dl-horizontal left-right"><dt>Toll Charges ({this.state.currency})</dt><dd><input name="toll_price" className="form-control price" type="text" /></dd></dl>
             <button type="submit" className="full-primary-btn fare-btn" onClick={ this.props.submit.bind(this) }>Dropped</button>
            </div>
        );
    }
}

class TripCompletedButton extends React.Component {
    render() {
        return (
            <div>
                <button type="submit" className="full-primary-btn fare-btn" onClick={this.props.submit.bind(this)}>Paid</button>
            </div>
        );
    }
}

class TripRatingButton extends React.Component {
    constructor(props) {
        super(props);
        this.state = {            
            comment: ''
        };        
        
        this.handleSubmit = this.handleSubmit.bind(this);
    }

    handleSubmit(event) {
        event.preventDefault();
    }

    componentDidMount() {
        $('.rating').rating();
    }

    _submit(event) {
        event.preventDefault();        
        $.ajax({
            url: '/provider/request/'+this.props.request+'/rate',
            dataType: 'json',
            data: {
                comment: $("#ratecmt").val(),
                rating: $("#rateip").val(),
            },
            headers: {'X-CSRF-TOKEN': window.Laravel.csrfToken },
            type: 'POST',
            success: function(data) {
                window.location.replace("/provider");
                //console.log('Accept', data);
            }.bind(this),
            error: function(xhr, status, err) {
                console.error(this.props.url, status, err.toString());
            }.bind(this)
        });
    }

    render() {
        interval();
        return (
            <div>
                <div className="rate-review">
                    <label>Rating</label>
                    <div className="rating-outer">
                        <input type="hidden" value="1" id="rateip" name="rating" className="rating" />
                    </div>
                    <label>Your Comments</label>
                    <textarea className="form-control" id="ratecmt" name="comment" placeholder="Write Comment" />
                </div>
                <button type="submit" className="full-primary-btn fare-btn" onClick={this._submit.bind(this)}>SUBMIT REVIEW</button>   
            </div>
        );
    }
}

class TripDetails extends React.Component {
    componentDidMount() {
        initMap();
        $('.rating').rating();
        var elems = Array.prototype.slice.call(document.querySelectorAll('.js-switch'));

elems.forEach(function(html) {
  var switchery = new Switchery(html);
});
    }

    render() {
        let picture = "/asset/logo.png";
        // console.log("this.props.request.user.picture", this.props.request.user.picture);
        if(this.props.request.user.picture != null) {
            picture = this.props.request.user.picture;
        }
        return (
            <div className="row no-margin">
                <div className="col-md-6">
                    <div className="provider-info">
                        <div className="img" style={{ backgroundImage: 'url(storage/' + picture + ')'}}></div>
                        <div className="content">
                            <h6>{this.props.request.user.first_name} {this.props.request.user.last_name}</h6>
                            <div className="rating-outer">
                                <input type="hidden" className="rating" value={this.props.request.user.rating} disabled />
                            </div>
                        </div>
                    </div>
                    <br />
                    <dl className="dl-horizontal left-right">
                        <dt>Request ID</dt>
                        <dd id="current_request_id" data-id={this.props.request.id}>{this.props.request.id}</dd>
                        <dt>Payment Mode</dt>
                        <dd>{this.props.request.payment_mode}</dd>
                    </dl>

                    {this.props.button}

                </div>
                <div className="col-md-6">
                    <div className="user-request-map">
                        <div className="from-to row no-margin">
                            <div className="from">
                                <h5>FROM</h5>
                                <p>{this.props.request.s_address}</p>
                            </div>
                            <div className="to">
                                <h5>TO</h5>
                                <p>{this.props.request.d_address}</p>
                            </div>
                        </div>
                        <div className="map-responsive-trip" id="map"></div>
                    </div>
                </div>
            </div>
        );
    }
}

class TripComponent extends React.Component {

    componentDidUpdate() {
        // console.log('Trip Component '+this.props.request.id);
        // console.log('Trip Component '+this.props.service_status);
        switch(this.props.request.status) {
            case "STARTED":
                this.form= {
                        status: "ARRIVED",
                        _method: "PATCH",
                    };

                updateMap({
                    source: {
                        lat: this.props.request.s_latitude,
                        lng: this.props.request.s_longitude,
                    },
                    destination: {
                         lat: this.props.request.d_latitude,
                        lng: this.props.request.d_longitude,
                    }
                });
                break;
            case "ARRIVED": 
                this.form= {
                        status: "PICKEDUP",
                        _method: "PATCH",
                        otp: $("#otp").val(),
                    };

                updateMap({
                    source: {
                        lat: this.props.request.s_latitude,
                        lng: this.props.request.s_longitude,
                    },
                    destination: {
                        lat: this.props.request.d_latitude,
                        lng: this.props.request.d_longitude,
                    }
                });

                break;
            case "PICKEDUP": 
                this.form= {
                        status: "DROPPED",
                        _method: "PATCH",
                        toll_price: $("input[name=toll_price]").val(),
                    };

                updateMap({
                    source: {
                        lat: this.props.request.s_latitude,
                        lng: this.props.request.s_longitude,
                    },
                    destination: {
                        lat: this.props.request.d_latitude,
                        lng: this.props.request.d_longitude,
                    }
                });

                break;
            case "DROPPED": 
                this.form= {
                        status: "COMPLETED",
                        _method: "PATCH",
                    };
                break;
            default:
                break;
        }
    }

    _submit(event) {
        event.preventDefault();
        // console.log(this.form);
        $.ajax({
            url: '/provider/request/'+this.props.request.id,
            dataType: 'json',
            data: this.form,
            headers: {'X-CSRF-TOKEN': window.Laravel.csrfToken, 'X-Requested-With' : 'XMLHttpRequest' },
            type: 'POST',
            success: function(data) {
                if(data.error){
                 alert(data.error);
                }
                // $('#msgs').html("<div class='alert alert-danger'>"+data.error+"</div>");
                console.log('Updated', data);
            }.bind(this),
            error: function(xhr, status, err) {
                // alert("Otp Is Wrong");
                // $('#msgs').html("<div class='alert alert-danger'>dddd"+err+"</div>");
                console.error(this.props.url, status, err.toString());
            }.bind(this)
        });
    }

    _cancel(reason) {
        // event.preventDefault();
        $(".reasonvalidate").hide();

        if(reason.length<=0){
            $(".reasonvalidate").show();
            return false;
        }
        
        if(reason=='ot'){
            var reason_opt=$("#cancel_text").val();
            if(reason_opt.length<=0){
                $(".reasonvalidate").show();
                return false;
            }
            reason=reason_opt;
        }        
        $("#cancel-reason").modal('hide');
        $.ajax({
            url: '/provider/cancel',
            dataType: 'json',
            data: {cancel_reason:reason, id: this.props.request.id},
            headers: {'X-CSRF-TOKEN': window.Laravel.csrfToken },
            type: 'POST',
            success: function(data) {
                window.location.replace("/provider");
            }.bind(this),
            error: function(xhr) {
                window.location.replace("/provider");
            }.bind(this)
        });
    }

    getButtons() {
        switch(this.props.request.status) {
            case "STARTED": 
                return (
                    <TripArrivedButton submit={this._submit.bind(this)} cancel={this._cancel.bind(this)} reasons={this.props.reasons} />
                );
                break;
            case "ARRIVED": 
                return (
                    <TripPickedButton submit={this._submit.bind(this)} cancel={this._cancel.bind(this)} reasons={this.props.reasons} rideOtp={this.props.rideOtp} />
                );
                break;
            case "PICKEDUP": 
                return (
                    <TripDroppedButton submit={this._submit.bind(this)} cancel={this._cancel.bind(this)} request={this.props.request.id} />
                );
                break;
            case "DROPPED": 
                return (
                    <TripCompletedButton submit={this._submit.bind(this)} />
                );
                break;
            case "COMPLETED": 
                return (
                    <TripRatingButton request={this.props.request.id} />
                );
                break;
            default:
                return null;
        }
    }

    render() {
        //console.log('Check after request completed', this.props.service_status);
        if(this.props.request.id == undefined) {
            if(this.props.service_status == 'active'){
                return (
                    <TripEmptyActive />
                );
            }else{
                return (
                    <TripEmptyOffline />
                );
            }
        } else {
            //console.log('Rendering Trip Details');
            return (
                <TripDetails request={this.props.request} button={this.getButtons()} />
            );
        }
    }
};

ReactDOM.render(
    <ModalComponent />,
    document.getElementById('modal-incoming')
);

if(document.getElementById('trip-container')) {
    //console.log('Rendering to Trip Container');
    ReactDOM.render(
        <MainComponent trip="true" />,
        document.getElementById('trip-container')
    );
    setTimeout(function(){ 
        var current_request_id=$("#current_request_id").attr('data-id');
        $("#waiting_div").attr('data-status',1);
        if(current_request_id>0){
            check_waiting(current_request_id);
        }
    }, 1000);
    
} else {
    //console.log('Rendering to Modal Container');
    ReactDOM.render(
        <MainComponent trip="false" />,
        document.getElementById('modal-incoming')
    );
}