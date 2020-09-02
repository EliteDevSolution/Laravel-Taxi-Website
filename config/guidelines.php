
<?php 

return array(
	'demo_mode_dialog' =>array(
		                  'admin'=>array(
							"admin.dashboard" => "<h3>Dashboard</h3>
							<li>View the summary of the entire system like number of Rides, Drivers registered, Total Earnings, Total Trips Taken, Ongoing Trips, Latest Trips</li>
							<li>In the Dashboard we can view all the data of wallet summary and recent rides.</li>
							<li>Super Admin can view the complete data of the user and provider’s information.</li>
							",

							"admin.dispatcher.index" => "<h3>Dispatcher</h3>
							<li>Using the dispatcher panel, rides can be assigned manually to drivers. </li> <li>It has the option subfields of Searching, Assigned and Cancelled Rides.</li> <li>
							It will show the drivers’ details and map location from start to end point.</li>",

							"admin.dispute.index" => "<h3>Dispute</h3>
							<li>In the disputes panel, we can create the disputes manually
							The dispute which has been added in admin will reflect in front end
							In this panel, we can create, Edit, Delete and Search for the disputes.</li><li>
							We can  Export the disputes via CSV, PDF and Excel sheet.
							Also, we can monitor which of the disputes are active and inactive.</li><li>
							This panel won't be visible in the demo mode.</li>",

							"admin.heatmap" =>"<h3>Heatmap</h3><li>In the Heatmaps, the admin can view all the Users’ and Drivers’ location.</li>
							<li>We can view the complete maps which allows Zooming in and Zooming out.</li>
							<li>In the heatmaps, the admin can view all the drivers’ ratings in the complete page.</li>",

							"admin.godseye" =>"<h3>Godseye</h3><li>Super admin can view all the maps of the user, Provider, Available user, and Available provider in the map view.</li>
							<li>Super Admin can click and view the user and provider in the application.</li>
							<li>In the map view, we can view the user and provider in the satellite view.  We can zoom out also.</li>
							",

							"admin.user.index" =>"<h3>Users</h3><li>The Super admin can do user management
							Admin can add, edit, delete the user and their rights.
							Admin can view the complete history of the user.</li>
							<li>Super Admin has the rights to delete the user in this module The complete user fields can be accessed by the admin.</li><li>
							Admin can export the file as CSV or Excel or PDF.</li>
							<li>In the User List, the super admin can view the Id, First and Last name, Email, Mobile, Rating, Wallet Amount and Action.</li>
							<li>The super admin can search for a  specific user in the search fields.</li><li>
							Admin has the rights to Edit and Delete a user in the web application.</li><li>
							The record of new users who have joined using the application will be stored here.</li>
							
							<b>In the Demo mode, You cannot Add,Edit and delete any user </b>
							",

							"admin.provider.index" =>"<h3>Providers</h3><li>Admin can manage the Drivers registered on the System.</li><li> 
							Admin can review the Documents and Vehicles added by them.</li><li> 
							Admin can approve/reject any Driver's account. </li><li>
							The driver would be able to get online only if their account has been approved by the Admin.</li><li> 
							Admin can add/modify/delete any Driver.</li><li>
						   Admin can export the file as CSV or Excel or PDF.</li><li>
						   New providers’ record who have joined through the application record will be stored here.</li><li>
						   Super Admin has the rights to make a driver online or offline.</li><li>
						   Super Admin has the rights to make a driver Active and inactive
						   Admin has the permission to Add, Edit and delete the provider.</li><li>
						   Admin has the rights to assign the service type based on the Driver vehicle’s application.</li><li>
						   Admin can approve the documentation of the driver and he can view the full statement. </li>
						   
						   <b>In the Demo mode, You cannot add, edit and delete any user.</b> 
						   ",

							"admin.dispatch-manager.index" =>"<h3>Dispatcher Panel</h3><li>Admin can manage dispatcher details who has been assigned for dispatching the taxi manually</li><li>
							The dispatcher details like name, email and mobile number will be stored</li><li>
							Super admin can Add, Edit and Delete the files.</li><li>
							Admin can export the file as CSV or Excel or PDF.</li>
							<b>In the Demo mode, You cannot add, Edit and delete the dispatcher user</b>
							",

							"admin.fleet.index" =>"<h3>Fleet Panel</h3><li>Admin manages fleet users.</li><li>
							Fleet owner manage the details of a fleet owner like name, email, image and company name</li><li>
							The list of providers under each fleet can be viewed based on each company</li><li>
							Admin can add fleet owners manually from admin panel</li><li>
							This process happens when a user wants to lend their car to the third party and approaches the company and provides the details</li><li>
							Admin can export the file as CSV or Excel or PDF.</li><li>
							The details include name, email, phone number, password  and company logo</li>
							<b>In the demo mode, You cannot  Add, Edit, Delete the fleet user</b>
							",

							"admin.account-manager.index" =>"<h3>Account Manager</h3><li>Admin can manage the account manager details in the admin panel</li><li>
							The account manager is the one who manages the complete track of providers earning</li><li>
							Admin stores details like name, mobile, mail of account manager</li><li>
							Admin can add account manager manually from the panel</li><li>
							Admin adds the details like name, email, password and mobile number</li><li>
							Once added, the account manager can log in with the credentials provided and can manage payment statement of provider</li><li>
							Admin can export the file as CSV or Excel or PDF.</li><li>
							In this list of account the Account manager the list of the account it will be displaying </li>
							<b>In the Demo mode, You cannot  Add, Edit, Delete the account manager </b>
							",

							"admin.ride.statement" =>"<h3>Overall Statements</h3><li>In the Overall statement, the super admin can see all the ride statement.</li><li>
							In the Overall statement, we can filter the ride with the From and To date.</li><li>
							On this page, we can view all the Total Rides, Cancelled rides and Revenue of the  user </li><li>
							Admin canexport the file as CSV or Excel or PDF.</li><li>
							Admin has the access to view all the records and Search the records.</li><li>
							In the overall Statement, it has the Booking ID, Picked UP, Dropped, Request Details, Commissions, Dated On, Status and Earned.</li>
							",

							"admin.ride.statement.provider" =>"<h3>Provider Statements</h3><li>In the Provider statement, the super admin can see all the  Provider statement.</li><li>
							Admin can export the file as CSV or Excel or PDF.</li><li>
							In the Provider statement it has Provider Name, Mobile, Status, Total Rides, Total Earnings, commissions,Joined At and Details.</li><li>
							Admin has the access to view the ride details in the provider statement.</li>
							",

							"admin.ride.statement.today" =>"<h3>Daily Statements</h3><li>In the Overall statement, the super admin can see all the ride statement.</li><li>
							In the Overall statement, we can filter the ride with the From and To date.</li><li>
							On this page, we can view all the Total Rides, Cancelled rides and Revenue of the  user </li><li>
							Admin canexport the file as CSV or Excel or PDF.</li><li>
							Admin has the access to view all the records and Search the records.</li><li>
							In the overall Statement, it has the Booking ID, Picked UP, Dropped, Request Details, Commissions, Dated On, Status and Earned.</li><li>
							In this, we can get the daily statement of the ride details.</li>
							",

							"admin.ride.statement.monthly" =>"<h3>Monthly Statements</h3><li>In the Overall statement, the super admin can see all the ride statement.</li><li>
							In the Overall statement, we can filter the ride with the From and To date.</li><li>
							On this page, we can view all the Total Rides, Cancelled rides and Revenue of the  user </li><li>
							Admin can export the file as CSV or Excel or PDF.</li><li>
							Admin has the access to view all the records and Search the records.</li><li>
							In the overall Statement, it has the Booking ID, Picked UP, Dropped, Request Details, Commissions, Dated On, Status and Earned.</li><li>
							In this, we can get the Monthly statement of the ride details.</li>
							",

							"admin.ride.statement.yearly" =>"<h3>Yearly Statements</h3><li>In the Overall statement, the super admin can see all the ride statement.</li><li>
							In the Overall statement, we can filter the ride with the From and To date.</li><li>
							On this page, we can view all the  Total Rides, Cancelled rides and Revenue of the  user </li><li>
							Admin canexport the file as CSV or Excel or PDF.</li><li>
							Admin has the access to view all the records and Search the records.</li><li>
							The overall Statement has the Booking ID, Picked UP, Dropped, Request Details, Commissions, Dated On, Status and Earned.</li><li>
							In this, we can get the Yearly statement of the ride details.</li>
							",

							"admin.providertransfer" =>"<h3>Provider Settlements</h3><li>Admin should have a debit card to be added to have a transaction with driver account</li>
							<li>The driver will have tax and commission amount of admin in his/her earnings, so the driver can request admin for the transaction</li>
							<li>Admin canexport the file as CSV or Excel or PDF.</li>
							<li>The admin can view the  Transaction, Date&Time, Provider Name, Amount and Action.</li>
							<b>In the demo mode, there is no option to create and send the settlements</b>
							",

							"admin.fleettransfer" =>"<h3>Fleet Settlements</h3><li>Admin should have a debit card to make a transaction with driver account</li>
							<li>The driver will have to pay tax and commission amount to admin from his/her earnings, so the driver can request admin for the transaction</li>
							<li>Admin can export the file as CSV or Excel or PDF.</li>
							<li>The admin can view the Transaction, Date & Time, Provider Name, Amount and Action.</li>
							<b>In the Demo mode, there is no option to create and send the settlements</b>
							",

							"admin.transactions" =>"<h3>Transactions</h3><li>Admin can view all the in one page.</li>
							<li>Admin can export the file as CSV or Excel or PDF.</li>
							<li>Super admin has the option to view of Transaction ID, Date&Time, Description, Status and Amount.</li>
							",

							"admin.user.review" =>"<h3>User Review</h3><li>Super admin can view all the reviews and ratings in the web application.</li>
							<li>Super Admin can view the user ratings and providers ratings separately.</li>
							<li>Super Admin can export the file as CSV or Excel or PDF.</li>
							<li>In the User and Provider rating the request id, Username, Provider name, Request ID, Rating, Date and Time and comments.</li>
							",

							"admin.provider.review" =>"<h3>Provider Review</h3><li>Super admin can view all the reviews and ratings in the web application.</li>
							<li>Super Admin can view the user ratings and providers ratings separately.</li>
							<li>Super Admin can export the file as CSV or Excel or PDF.</li>
							<li>In the User and Provider rating the request id, Username, Provider name, Request ID, Rating, Date and Time and comments.</li>
							",

							"admin.requests.index" =>"<h3>Request History</h3><li>Super Admin can view all the request history of the ride.</li>
							<li>Super Admin can export the file as CSV or Excel or PDF.</li>
							<li>he super admin, can view all the details like Booking ID, User Name, Provider Name, etc</li>
							<li>Super admin can track the status of all the rides of the passenger and provider</li>
							<b>In the Demo mode, You cannot edit and delete the records.</b>
							",

							"admin.requests.scheduled" =>"<h3>Scheduled Rides</h3><li>Super Admin can view all the Scheduled History of the ride.</li>
							<li>Super Admin can export the file as CSV or Excel or PDF.</li>
							<li>A super admin can view all the details like Booking ID, User Name, Provider Name, etc</li>
							<li>Super admin edits and deletes the scheduled ride.</li> 
							<b>In the Demo mode, You cannot edit and delete the records.</b>
							",

							"admin.promocode.index" =>"<h3>Promocode</h3><li>Admin can maintain the promo code and the promo code can be added manually from the admin panel.</li>
							<li>The promo code will have an expiry date, discount amount and the code to enter manually</li>
							<li>The promo code will not work if it is  expired.</li>
							<li>Admin can edit and delete the coupon from the panel</li>
							<li>The logic of promo code will be percentage over the max amount.</li>
							<li>Super Admin only has the access to add the promo code.</li>
							<b>In the Demo mode, You cannot Add, Edit and Delete it.</b>
							",

							"admin.service.index" =>"<h3>Service Type</h3><li>The list of service types can be managed from admin panel</li>
							<li>Admin can add, edit and delete the service types</li>
							<li>Admin can manage the pricing logic for each service type</li>
							<li>Admin has the access to declare base price, distance price, hour price and price logic for each service type</li>
							<li>Admin can add ‘N’ number of services from admin panel where it gets information like service name, service image, unit time price, hour pricing and logic for the service type</li>
							<li>Admin can also enter the seat capacity and description for the specific service type</li>
							<li>The pricing logic of each service type will be displayed and it changes based on pricing logic we choose</li>
							<li>Super Admin can export the file as CSV or Excel or PDF.</li>
							<li>Super Admin would set the Price per Kms, Price Per minute, Minimum Fare, Base Fare, Commission (%), Cancellation Charges and Peak time charges for each Vehicle Type.</li>
							<li>Admin can update or edit service from the panel.</li>
							<li>Admin can change its base price, hour price, pricing logic for its service type provided.</li>
							<b>In the Demo mode, You cannot Add, Edit and Delete it </b>
							",

							"admin.document.index" =>"<h3>Documents</h3><li>Super admin can view all the documents which is uploaded by the user and provider in the application and web application.</li>
							<li>Super admin can filter the review document in the vehicle review and driver review.</li>
							<li>Super Admin can export the file as CSV or Excel or PDF.</li>
							<li>Super admin can search all the documents in the search module.</li>
							<li>Super admin can add the documents for the driver or for the vehicle and he can map from here.</li>
							<b>In the Demo mode, You cannot Add, Edit and Delete it </b>
							",

							// "admin.notification.index" =>"<h3>Heatmap</h3>",

							// "admin.reason.index" =>"<h3>Heatmap</h3>",

							"admin.payment" =>"<h3>Payment History</h3><li>Super admin has the full access to view the payment history page.</li>
							<li>Super Admin can export the file as CSV or Excel or PDF.</li>
							<li>In the payment history page, admin can search all the history in the search module.</li>
							<li>In the payment history page, we can view the payment mode, payment status, payment Status etc.</li>
							",

							"admin.settings.payment" =>"<h3>Payment Settings</h3><li>Admin has the complete access to payment settings</li>
							<li>The payment settings include information like dynamic payment gateway switchover, Target for per day task, surge trigger point, tax percentage, commission percentage, and currency.</li>
							<li>A dynamic payment gateway is the one where admin can turn on/off the option and that reflects in the front end
							Surge trigger point and surge point displays the demand of provider in a specific area, if any.</li>
							<li>Those information can be edited and updated using the admin panel</li>
							<li>The commission and tax amount should be specified by admin and the amount will be added up to the user’s invoice</li>
							<b>In the Demo mode, You cannot Add, Edit and Delete it.</b>
							",

							"admin.settings" =>"<h3>Site Settings</h3><li>Super admin has the access to change all the settings </li>
							<li>The site settings is where the complete details of the product will be displayed.</li>
							<li>The site settings include information like an app store, play store link, search distance, response details, SOS, contact email, contact number.</li>
							<li>The admin can opt manual and broadcasting option, where admin can manage the manual assigning of project and broadcasting will happen automatically</li>
							<b>In the demo mode, You cannot Add, Edit and Delete it.</b>",

							// "admin.role.index" =>"<h3>Heatmap</h3>",

							// "admin.sub-admins.index" =>"<h3>Heatmap</h3>",

							"admin.cmspages" =>"<h3>CMS Page</h3> <li>Super admin has the access to CMS page.</li>
							<li>Super admin can add, Edit and Delete CMS content pages</li>
							<li>Super Admin can change any page in the CMS module.</li>
							<b>In the Demo mode, You cannot Add, Edit and Delete it.</b>",

							"admin.help" =>"<h3>Help Page</h3> <li>Super admin has the access to the Help page.</li>
							<li>Super admin can add, Edit and Delete Help content pages</li>
							<li>Super Admin can change any page in the Help module.</li>
							<b>In the Demo mode, You cannot Add, Edit and Delete it.</b>
						   ",

							"admin.push" =>"<h3>Push Notification</h3><li>Super admin has the access to the push notification page.</li>
							<li>Super Admin can export the file as CSV or Excel or PDF.</li>
							<li>Admin has the option called custom push where some information can be sent to the user or driver.</li>
							<li>The custom push can be sent to a specific user or a user in a specific location or to users who were active for past one hour</li>
							<li>The message reaches the user and driver as ‘push notification’ in the application.</li>
							<b>In the Demo mode, You cannot Add, Edit and Delete it.</b>
							",

							"admin.translation" =>"<h3>Account Details</h3><li>Admin can manage his account details<li>
							<li>Admin can change the account details using this panel<li>
							<li>The admin can edit and update the account details<li>
							<b>In the Demo mode, You cannot Add, Edit and Delete it.</b>
							",

							// "admin.lostitem.index" =>"<h3>Heatmap</h3>",

							"admin.password" =>"<h3>Change Password</h3><li>Super admin has the access to change password in this page.</li>
							<li>Only super admin can edit, add and delete them.</li>
							<b>In the Demo mode, You cannot Edit and Delete it.</b>
							",
						  ),

						  'user'=>array(
							"dashboard" => "<h3>Dashboard:</h3>To request a ride, follow the steps 
							<li>Provide Pickup and Drop location</li>
							<li>Choose a service from the list of service type</li>
							<li>Once done, you can view the ETA and Estimated fare can be </li><li>viewed and verified</li>
							<li>Payment mode and promo code can be specified</li>
							<li>You can either ride now or book the ride for a later time</li>
							<li>Once ride now chosen, it will search for a nearby provider</li>
							",
							 "notifications" => "<h3>Notification</h3>
							 If any information is added in admin panel regarding the application, the user will be notified in notification manager with an image and a description.",
							"trips" => "<h3>My Trips</h3>In this board, you can see the detailed report of the past trips report.
							<li>The details include pick-up location and destination location, fare calculation cutdown, mode of payment</li>
							<h3>Dispute</h3>A user can raise dispute regarding the trips with a description and it will be listed in the admin panel with the trip details. Admin will recheck the dispute raised and change the status accordingly.
							<h3>Lost Item</h3>A user can request or can contact admin directly to get the items lost during a ride.",
							

							"upcoming/trips" => "<h3>Upcoming Trips</h3>In this screen, you can see the upcoming trips’ details, that is, the ones the driver accepted

							<li>The details include  Pick-up location and destination location, fare calculation, mode of payment, schedule booking time and date</li>
							<li>If you want to the cancel request, that can be done in request details</li>
							",
							"profile" => "<h3>Profile</h3>Here, you can edit the profile details
							<li>To edit, tap edit and update the details like name, profile picture and language</li>
							<li>Mobile number and email cannot be edited, as that’s the unique field used to login with application</li>
							",
							"change/password" => "<h3>Change Password</h3>Here you can change the existing password and update the new password
							To change the password
							<li>Provide old password</li>
							<li>Enter the new password and confirm it.</li>
							As we are in Demo mode, you can’t change the password
							But in the app, you can change password and login with the password changed credentials
							",
							"payment" => "<h3>Payment</h3>The mode of payment available by default
							<li>Cash</li>
							<li>Card</li>
							We can customise other payment gateways as per your requirement
							",
							"referral" => "<h3>Refer a friend</h3>A user can refer another user/driver using a referral code which can be shared via mail/SMS and the same is used during the signup process to earn referral count and amount.
							The referral and amount can be modified by the admin",
							
							"wallet" => "<h3>Wallet</h3>The payment for the ride can be even paid through wallet if the amount available
							To recharge the wallet
							<li>Specify amount to be added in the wallet</li>
							<li>Add a card or if it already exists choose a card and proceed with add money option.</li>
							",
						   ),

						   'provider'=>array(
							"provider" => "<h3>Provider</h3>You have successfully registered as a driver
							A few more steps need to be done to complete the profile. To be a  driver there are  two things that are mandatory in a profile
							<li>Add card (Debit card)</li>
							<li>Upload document</li>
							Driver can upload the document and after that, the card should be added in the profile
							In the Demo mode, If you log in, you can’t view card and document details in application. In the real environment, those two features are mandatory
							You can specify the availability of the driver using the toggle option.
                            If online, you can receive the request and if offline you can’t receive a request .
							",
							"provider/earnings" => "<h3>Partner Earnings</h3>In this screen,you can view the earnings of the driver for a day
							You can view total trips completed, the no of trips cancelled and accepted ride rate of driver
							",
							"provider/notifications"=> "<h3>Notification</h3>If any information is added in admin panel regarding the application, the driver will be notified in notification manager with an image and a description.",

							
							"provider/profile"=> "<h3>Profile</h3>
							<li>You can edit the details you provided like name, profile picture, language, phone number, address, service type, service model and car number</li>
							<li>You can even change the car number, car model</li>
						   ",
							"provider/documents"=> "<h3>Manage Documents</h3>
							To upload the document
							<li>Upload the specified document</li>
							<li>Document format should be doc or pdf or image</li>
							<li>Once uploaded, confirm the document. The document will be</li> <li>reviewed and approved by admin</li>
							",
							"provider/location" => "<h3>Update Location</h3>If location is enabled in the web, driver can be tracked using GPS and current location will be displayed
							If the location is not enabled, follow the steps,
							<li>Tap on update location</li>
							<li>Provide the location you were in</li>
							<li>Update and proceed</li>
							",
							"provider/wallet_transation" => "<h3>Wallet Transation</h3>The amount requested from admin and the amount transacted from admin will be displayed in the wallet history.",
							"provider/transfer"=> "<h3>Transfer</h3>
							If the driver needs amount/transfer to admin, he can request/transfer amount to/from admin panel. To request/transfer amount.
							<li>Specify the amount needed/need to transfer</li>
							<li>Tap on transfer</li>
							<li>Admin will review and approve</li>
							",
							 "provider/referral"=> "<h3>Refer a friend</h3>
							A driver can refer another user/driver using a referral code which can be shared via mail/SMS and the same is used during the signup process to earn referral count and amount.
							The referral and amount can be modified by the admin",
						),

	                     ),
);
