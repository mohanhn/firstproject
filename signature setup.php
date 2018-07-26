<?php
	session_start();
	require_once("assets/include/membersite_config.php");


?>

<!DOCTYPE html>

<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="description" content="A fully featured admin theme which can be used to build CRM, CMS, etc.">
<meta name="author" content="Pixel Effects">
<!-- App Favicon -->
<link rel="shortcut icon" href="assets/images/favicon.ico">
<!-- App title -->
<title>LeasingPro&trade;</title>
<!-- App CSS -->
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
</style>
</head>

<body class="fixed-left">

<div class="wrapper">
	<div class="topbar">
		<?php include('header.php'); ?>
	</div>
		<div class="content-page-nonnav">
			<div class="content">
				<div class="container" style="margin-top: 160px;margin-bottom: 20px;position:relative;">
					<div class="row">
						<div class="col-xs-12">
							<div class="card-box">
								<div class="row">
									<div class="col-xs-12">
										<h3 class="page_title">Electronic Signature Setup</h3>
									</div>
								</div>
								<div class="row member_id_block">
									<div class="col-xs-12">
										 <div style="text-align:center;">
											 <span class="member_label">Membership ID: </span>
											 <span class="member_id">DSR</span>
										 </div>
									</div>
								</div>
									<form class="form-inline template-form" id="select_member_list">
										<div class="form-group">
											<label class="sr-only" for="select_template">Select Package to Edit</label>
											<div class="input-group option_width">
												<div class="input-group-addon" ><i class="fa fa-users" aria-hidden="true"></i></div>
												<select class="form-control" id="select_member_name">
													  <option selected="" disabled="">Select Member</option>  
													  <option>Member2</option>  
													  <option>Member3</option>  
													  <option>Member4</option>  
													  <option>Member5</option>  
													  <option>Member6</option>  
													  <option>Member7</option>  
													 
												</select>			
											</div>
										</div> 
									</form>	
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

</div>

<?php include('footer.php'); ?>
</body>
</html>