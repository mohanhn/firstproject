<?php

session_start();
require_once("assets/include/membersite_config.php");




if(isset($_POST['saveall'])=="save")
{
	$save=$_POST['saveall'];
	
	if(isset($_POST['first_name']))
				{
					$first_name=$_POST['first_name'];
					
				}else
				{
					$first_name='';
				}
				
				
				
	if(isset($_POST['last_name']))
				{
					$last_name=$_POST['last_name'];
					
				}else
				{
					$last_name='';
				}
				
				
	if(isset($_POST['ssn_occ']))
				{
					$ssn_occ=$_POST['ssn_occ'];
					
				}else
				{
					$ssn_occ='';
				
				}			
	
	if(isset($_POST['dob_occ']))
				{
					$dob_occ=$_POST['dob_occ'];
					
				}else
				{
					$dob_occ='';
				
				}
     
	 
	 if(isset($_POST['email_occ']))
				{
					$email_occ=$_POST['email_occ'];
					
				}else
				{
					$email_occ='';
				
				} 
				
				
	 if(isset($_POST['occupant_type']))
				{
					$occupant_type=$_POST['occupant_type'];
					
				}else
				{
					$occupant_type='';
				
				}
				
				
				
				$insertoccupant1=$fgmembersite->insertintotable($first_name,$last_name,$ssn_occ,$dob_occ,$email_occ,$occupant_type);
			echo $insertoccupant1;
				
		
				
				
}


	if(isset($_POST['update']))
	{
		$save=$_POST['update'];
		
		if(isset($_POST['slno']))
				{
					$slno=$_POST['slno'];
					
				}else
				{
					$slno='';
				}
				
				
				
	if(isset($_POST['fname']))
				{
					$fname=$_POST['fname'];
					
				}else
				{
					$fname='';
				}
				
				
	if(isset($_POST['lname']))
				{
					$lname=$_POST['lname'];
					
				}else
				{
					$lname='';
				
				}			
	
	if(isset($_POST['ssn']))
				{
					$ssn=$_POST['ssn'];
					
				}else
				{
					$ssn='';
				
				}
     
	 
	 if(isset($_POST['dob']))
				{
					$dob=$_POST['dob'];
					
				}else
				{
					$dob='';
				
				} 
				
				
	 if(isset($_POST['email']))
				{
					$email=$_POST['email'];
					
				}else
				{
					$email='';
				
				}	 
				
				
	if(isset($_POST['occtype']))
				{
					$occtype=$_POST['occtype'];
					
				}else
				{
					$occtype='';
				
				}
				
				
		$updateoccupant1=$fgmembersite->updateintotable($slno,$fname,$lname,$ssn,$dob,$email,$occtype);
			echo $updateoccupant1;
		
	}


if(isset($_POST['col_id']))
	{
		$sl_no=$_POST['col_id'];
		error_log($sl_no);
		$deleteoccupant=$fgmembersite->deleteoccupant($sl_no);
			echo $deleteoccupant;
	}




  
 
?>