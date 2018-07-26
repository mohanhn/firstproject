<?php
session_start();
require_once("assets/include/membersite_config.php");
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="description" content="A fully featured admin theme which can be used build CRM ,CMS ,etc">
<meta name="author" content="pixel Effects">
<!-- App Favicon -->
<link rel="shortcut icon" href="assets/images/favicon.ico">
<!-- App title -->
<title>Leasing pro &trade;</title>
<!-- App css -->
<link href="assets/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
<link href="assets/css/core.css" rel="stylesheet" type="text/css" />
<!-- <link href="assets/css/components.css" rel="stylesheet" type="text/css" /> -->
<link href="assets/css/icons.css" rel="stylesheet" type="text/css" />

<link href="assets/css/menu.css" rel="stylesheet" type="text/css" />
<link href="assets/css/responsive.css" rel="stylesheet" type="text/css" />
<script src="assets/js/modernizr.min.js"></script>
<link rel="stylesheet" type="text/css" href="assets/sweetalert/sweetalert.css">
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs/jq-2.2.4/dt-1.10.15/af-2.2.0/b-1.3.1/b-print-1.3.1/fc-3.2.2/fh-3.1.2/datatables.min.css"/>
<link href="assets/css/leasingpro.css" rel="stylesheet" type="text/css" />
<style>
.mn_ab
{
	background-color: #ebeff2;
    padding: 7px;
}
.mn_ac
{
	float:right;
}
.package_selection
{
	max-width:268px;
	width:100%;
	float:right;
	
	
}
.button_section{
	   
		font-weight: 600;
}
.button_selection
{
	padding:16px;
}
.span_or{
	padding-right:15px;
}
</style>
</head>
<body class="fixed-left">
<!--page bigin-->
<div id="wrapper">
	<div class="topbar">
	<?php include('header.php');?>
	</div>
	<div class="content-page-nonav">
		<div class="content">
			<div class="container">
				<div class="row">
					<div class="col-xs-12" style="margin-top: 63px;">
						<div class="card-box">
							<div class="row">
								<div class="col-xs-12">
								<h3 class="page_title">Package Setup</h3>
								</div>
							</div>
							<div class="row">
								<div class="col-xs-12">
									<div style="text-align:center;">
										 <span class="mn_ab ">Member ID</span>
									      <span class="mn_ab">DSR</span>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-xs-7" style="margin-top:15px;">
								<select class="form-control package_selection">
								<option selected disabled value="00">Select package to Edit</option>
								<option  value="">package 1</option>
								<option  value="">package 2</option>
								<option  value="">package 3</option>
								<option  value="">package 4</option>
								<option  value="">package 5</option>
								</select>
								</div>
								<div class="col-xs-5 button_selection">
									<span class="span_or" >OR</span> <button  type="button"  data-toggle="modal" data-target="#myModal" class="btn btn-primary button_section">Add new</button>
								</div>
							</div>
						</div>
						     <div class="card-box">
						        <div class="row">
								    <div class="col-xs-12 full_data">
									    
									</div>
								</div>
						   </div>
						
					</div>
				</div>
			</div>
		</div>
	</div>
	
</div>


<!--MODAL BIGIN-->


  <div class="modal fade" id="myModal" role="dialog">
		<div class="modal-dialog modal-lg">
			  <div class="modal-content">
					<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal">&times;</button>
							<h2>ALL DETAILS</h2>
					</div>
					
												
					<div class="modal-body">
					  <div class="container">
							 <form class="form-horizontal" action="" id="form_details">
							 <input type="hidden" name="saveall" id="saveall" value="">
							 <div class="form-group">
								  <label class="control-label col-xs-2" for="pwd">Category:</label>
								  <div class="col-xs-10">          
										<select class="form-control" name="main_cat" id="main_cat">
											<option value="" selected>Select the category</option>
											<option value="property">Property</option>
											<option value="vehicles">Vehicles</option>
											<option value="ps">Parking and storages</option>
											<option value="unitinfo">unitinfo</option>
											<option value="lt">Lease terms</option>
											<option value="financial">financial</option>
											<option value="limits">Limits</option>
											<option value="resident">Resident</option>
											<option value="contact">Contact</option>
											<option value="pets">Pets</option>
											<option value="appliance">Appliance</option>
											<option value="utilities">Utilities</option>
											<option value="miscellaneous">Miscellaneous</option>
										</select>
										<span class="maincat_span" style="color:red; display:none;" >Please select the Category<span>
								  </div>
								</div>
								<div class="form-group">
									<label class="control-label col-xs-2" for="email">Sub category:</label>
								    <div class="col-xs-10">
									    <select class="form-control" name="sub_cat" id="sub_cat">
											<option value="" selected>select the sub category</option>
											<option value="volvo">RESIDENTS</option>
											<option value="saab">Saab</option>
											<option value="mercedes">Mercedes</option>
											<option value="audi">Audi</option>
										</select>
										<span class="subcat_span" style="color:red; display:none;" >Please select the sub-Category<span>
								    </div>
								</div>
								<div class="form-group">
								  <label class="control-label col-xs-2" for="pwd">Do You have vehicles?:</label>
								          <div class="col-xs-10">
											   <select class="form-control" name="vehicle" id="vehicle">
											     <option value="" selected >Please select YES or NO</option>
											     <option value="Y" >YES</option>
											     <option value="N">NO</option>
											    </select>
										   </div>
											
								   <span class="vehicle_span" style="color:red; display:none;" >Please select Yes or No<span>
								</div>
								
								<div class="form-group">
								    <label class="control-label col-xs-2" for="email">Email:</label>
									<div class="col-xs-10">   
										<input type="email" class="form-control" id="email" placeholder="Enter email"  name="email">
										<span class="email_span" style="color:red; display:none;" >Please enter your email ID</span>
								    </div>
									
								</div>
								<div class="form-group">
								    <label class="control-label col-xs-2" for="pwd">Password:</label>
								  <div class="col-xs-10">          
									<label class="sr-only" for="pwd">Password:</label>
									<input type="password" class="form-control" id="pwd" placeholder="Enter password" name="pwd">
                                    <span class="password_span" style="color:red; display:none;" >Please enter your password</span>									
								 </div>
								</div>
								<div class="form-group">
								    <label class="control-label col-xs-2" for="pwd"> Mobile Number:</label>
									<div class="col-xs-10">  
										<input type="text" class="form-control" id="number" placeholder="Enter your mobile number" name="mnumber">
										<span class="number_span" style="color:red; display:none;" >Please enter your mobile number</span>	
									</div>
								</div>
								 <div class="form-group">
									<label class="control-label col-xs-2" for="comment">Leave your Comment:</label>
									<div class="col-xs-10">
										<textarea class="form-control" rows="5" id="comment" name="comment"></textarea>
									</div>
								 </div>
								<div class="form-group">        
								  <div class="col-xs-offset-2 col-xs-10">
									<div class="checkbox">
										<label><input type="checkbox" name="remember" id="remember"> Select to save details</label> 
									</div>
								  </div>
								</div>
							
							 </form>
					</div>
					</div>
					<div class="modal-footer">
					  <button type="button" class="btn btn-default" data-dismiss="modal" id="closewindow">Close</button>
					  <button type="button" class="btn btn-primary"  disabled id="save_details"> Save </button>
					</div>
					 
				 </div>
		  </div>
    </div>





<script src="assets/js/jquery.min.js"></script>
<script src="assets/js/bootstrap.min.js"></script>      
<script type='text/javascript' src="assets/js/package_new.js"></script>
<script src="assets/sweetalert/sweetalert.min.js"></script>
<?php include ('footer.php'); ?>



</body>
</html>