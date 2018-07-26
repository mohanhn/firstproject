<?php
session_start();
require_once("assets/include/membersite_config.php");

$result=$fgmembersite->addoccupant();
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="description" content="A fully featured admin theme which can be used build CRM ,CMS ,etc">
<meta name="author" content="pixel Effects">
<link rel="shortcut icon" href="assets/images/favicon.ico">
<link href="assets/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
<link href="assets/css/core.css" rel="stylesheet" type="text/css" />
<!--<link href="assets/css/components.css" rel="stylesheet" type="text/css" /> -->
<link href="assets/css/icons.css" rel="stylesheet" type="text/css" />

<link href="assets/css/menu.css" rel="stylesheet" type="text/css" />
<link href="assets/css/responsive.css" rel="stylesheet" type="text/css" />
<script src="assets/js/modernizr.min.js"></script>
<link rel="stylesheet" type="text/css" href="assets/sweetalert/sweetalert.css">
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs/jq-2.2.4/dt-1.10.15/af-2.2.0/b-1.3.1/b-print-1.3.1/fc-3.2.2/fh-3.1.2/datatables.min.css"/>
<link href="assets/css/leasingpro.css" rel="stylesheet" type="text/css" />
<style>
.details_box
{
	margin-top: 200px;
    padding-top: 24px;
    padding-bottom: 96px;
    margin-right: 15px;
    margin-left: 15px;
}
.modal.fade:not(.in).left .modal-dialog 
{
		webkit-transform: translate3d(-25%, 0, 0);
		transform: translate3d(-25%, -25%, 0);
}

</style>
</head>
<body class="fixed-left">
	<div class="topbar">
        <div class="navbar navbar-default" role="navigation">
            <div class="container">
                <ul class="nav navbar-nav navbar-left">
                    <li>
                        <h4 class="page-titlebigog" style="padding-bottom:15px;"><img src="assets/images/tsp-logo-transparent.png" class="img-responsive"></h4>
                    </li>
                </ul>
                <a href="index.php" style="font-size:15px;float:right;margin-top:25px;margin-right:40px;font-size:17px;">Back To Dashboard</a>
            </div>
        </div>
    </div>
			<div class="content-page-nonnav">
				<div class="content">
					<div class="container">
						<div class="row">
							 <div class="card-box">
											<div class="row">
												<div class="col-xs-12 show_data" style="margin-top:130px;">
													<?php
														 echo
															  "<table class='table table-striped'>
																				
																				<tr>
																				<th>Sl no</th>
																				<th>First Name</th>
																				<th>Last Name</th>
																				<th>SSN</th>
																				<th>DOB</th>
																				<th>Email</th>
																				<th>Occupant type</th>
																				<th>Action</th>
																				</tr>";
																				for($i=0; $i<count($result); $i++)
																				{
																					
																					echo
																					"<form><tr>
																					<input type='hidden' name='update' id='update' value=''>
																					<input type='hidden' name='slno' id='slno' value='{$result[$i]['id']}'>
																					<td>{$i}</td>
																					<td><input type='text' class='form-control' name='fname' value='{$result[$i]['firstname']}' disabled></td>
																					<td><input type='text' class='form-control' name='lname' value='{$result[$i]['lastname']}' disabled></td>
																					<td><input type='text' class='form-control' name='ssn' value='{$result[$i]['ssn']}' disabled></td>
																					<td><input type='text' class='form-control' name='dob' value='{$result[$i]['dob']}' disabled></td>
																					<td><input type='text' class='form-control' name='email' value='{$result[$i]['email']}' disabled></td>
																					<td><input type='text' class='form-control' name='occtype' value='{$result[$i]['occupanttype']}' disabled></td>
																					<td class='edit_button'><span class='glyphicon glyphicon-pencil' style='cursor:pointer; color:blue;' id='edit_row'> <i class='fa fa-trash-o del_button' aria-hidden='true' style='cursor:pointer; color:red;'  id='{$result[$i]['id']}'></i></span</td>
																					<td class='save_button' style='display:none;'><i class='fa fa-floppy-o save_button1' aria-hidden='true' style='cursor:pointer; color:green;'  id='{$result[$i]['id']}'></i></span</td>
																					
																					
																					</tr>
																					</form>";
																				}
																				
															echo "</table>";
													 ?>
												</div>
											</div>
									   </div>
									   <!--modal-->
									   
									   <div class="modal fade left" id="myModal" role="dialog">
											<div class="modal-dialog modal-lg">
											  <div class="modal-content">
												<div class="modal-header">
												  <button type="button" class="close" data-dismiss="modal">&times;</button>
												  <h4 class="modal-title">OCCUPANT DETAILS</h4>
												</div>
												<form class="form-group" id="form_details">
													<input type="hidden" name="saveall" id="saveall" value="">
												<div class="modal-body col-xs-12">
													 <div class="col-xs-3">
														<label>First Name:</label>
													 </div>
													 <div class="col-xs-5">
														<input type="text" placeholder="First name" id="first_name" name="first_name" value="" class="form-control">
														<span class="fname_span" style="color:red; display:none;" >Mandatory field<span>
													 </div>
												</div>
												<div class="modal-body col-xs-12">
													 <div class="col-xs-3">
														<label>Last Name:</label>
													 </div>
													 <div class="col-xs-5">
														 <input type="text" placeholder="Last name" id="last_name" name="last_name" value="" class="form-control">
													     <span class="lname_span" style="color:red; display:none;" >Mandatory field<span>
													 </div>
												</div>
												<div class="modal-body col-xs-12">
													 <div class="col-xs-3">
														<label>SSN:</label>
													 </div>
													 <div class="col-xs-5">
														 <input type="text" placeholder="SSN" id="ssn_occ" name="ssn_occ" value="" class="form-control">
														 <span class="ssn_span" style="color:red; display:none;" >Mandatory field<span>
													 </div>
												</div>
												<div class="modal-body col-xs-12">
													 <div class="col-xs-3">
														<label>DOB:</label>
													 </div>
													 <div class="col-xs-5">
														 <input type="text" placeholder="DOB" id="dob_occ" name="dob_occ" value="" class="form-control">
														 <span class="dob_span" style="color:red; display:none;" >Mandatory field<span>
													 </div>
												</div>
												<div class="modal-body col-xs-12">
													 <div class="col-xs-3">
														<label>Email ID:</label>
													 </div>
													 <div class="col-xs-5">
														<input type="text" placeholder="E-Mail" id="email_occ" name="email_occ" value="" class="form-control">
													  <span class="email_span" style="color:red; display:none;" >Mandatory field<span>
													 </div>
												</div>
												<div class="modal-body col-xs-12">
													 <div class="col-xs-3">
														<label>Occupant type:</label>
													 </div>
													 <div class="col-xs-5">
														<input type="text" placeholder="Occupant Type" id="occupant_type" name="occupant_type" value="" class="form-control">
														<span class="occtype_span" style="color:red; display:none;" >Mandatory field<span>
													 </div>
												</div>
												<div class="modal-footer">
													<button type="button" class="btn btn-primary"   id="save_occ"> Save </button> 
													<button type="button" class="btn btn-primary"   id="cancel_occ"> Cancel</button>
												</div>
												</form>
											  </div>
											</div>
										  </div>
										</div>
											<div class="row">
												<div class="col-xs-12"style="padding-top:130px;">
														<button type="button" class="btn btn-primary"  id="Back_occ">Back</button>
														<button type="button" class="btn btn-primary"   id="add_occ" data-toggle="modal" data-target="#myModal"> Add Occupant </button> 
														<button type="button" class="btn btn-primary"   id="continue_occ"> Continue</button> 
												</div>
											</div>
					</div>
				</div>
			</div>

		
<script src="assets/js/jquery.min.js"></script>
<script src="assets/js/bootstrap.min.js"></script>      
<script type='text/javascript' src="assets/js/add_occupant.js"></script>
<script src="assets/sweetalert/sweetalert.min.js"></script>
<?php include ('footer.php'); ?>
</body>
</html>