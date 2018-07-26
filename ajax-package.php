<?php
	session_start();
	require_once("assets/include/membersite_config.php");
	
	
	
if(isset($_POST['member_select']))
{
	
	$selectedmember=$_POST['member_select'];
	error_log($selectedmember);
	$selectedmember1=trim($selectedmember);
	$result=$fgmembersite->selectmembers($selectedmember1);
	error_log(print_r($result,true));
	echo 
	
		"<table class='table table-striped'>
			<tr>
				<th>Id</th>
				<th>Member Id</th>
				<th>Member Number</th>
				<th>Member Name</th>
				<th>Master Member Id</th>
				<th>Master Member Number</th>
			</tr>";
			for($i=0; $i<count($result); $i++)
			{
				echo 
						"<tr>
								<td>{$result[$i]['apprpid']}</td>
								<td>{$result[$i]['apprpdefaultreq']}</td>
								<td>{$result[$i]['apprpedit']}</td>
								<td>{$result[$i]['apprpfeedid']}</td>
								<td>{$result[$i]['apprpfieldname']}</td>
								<td>{$result[$i]['apprpfieldtype']}</td>
								<td>{$result[$i]['apprphide']}</td>
								<td>{$result[$i]['apprpid']}</td>
								<td>{$result[$i]['apprplastmodified']}</td>
								<td>{$result[$i]['apprpmodifiedby']}</td>
								<td>{$result[$i]['apprpoptlabel']}</td>
								<td>{$result[$i]['apprpreq']}</td>
								<td>{$result[$i]['apprpselect']}</td>
								<td>{$result[$i]['apprptempid']}</td>
							
						
						</tr>";
			}				
		echo "</table>";
		
		
		
		
}

/* all details form information storing */
		if(isset($_POST['saveall'])=="save") //checking saveall hidden field value is save or not
		{
			$save=$_POST['saveall'];
			
			if(isset($_POST['main_cat']))
				{
					$main_cat=$_POST['main_cat'];
					
				}else
				{
					$main_cat='';
				}
			error_log($main_cat);
			
			
			if(isset($_POST['sub_cat']))
				{
					$sub_cat=$_POST['sub_cat'];
					
				}else
				{
					$sub_cat='';
				}
			error_log($sub_cat);
			
			
			if(isset($_POST['vehicle']))
				{
					$vehicle=$_POST['vehicle'];
					
				}else
				{
					$vehicle='';
				}
			error_log($vehicle);
			
			
			
			if(isset($_POST['email']))
				{
					$email=$_POST['email'];
					
				}else
				{
					$email='';
				}
			error_log($email);
			
			
			
			if(isset($_POST['pwd']))
				{
					$pwd=$_POST['pwd'];
					
				}else
				{
					$pwd='';
				}
			error_log($pwd);
			
			
			
			
			
			if(isset($_POST['mnumber']))
				{
					$mnumber=$_POST['mnumber'];
					
				}else
				{
					$mnumber='';
				}
				error_log($mnumber);
			
			
			
		
		
			
			if(isset($_POST['comment']))
				{
					$comment=$_POST['comment'];
					
				}else
				{
					$comment='';
				}
			error_log($comment);
		
			$insert=$fgmembersite->inserttotable($main_cat,$sub_cat,$vehicle,$email,$pwd,$mnumber,$comment);
			echo $insert;
		
		}


?>