<?php
  session_start();
  require_once("assets/include/membersite_config.php");
  
  if(isset($_POST['package_select']))
	  
	  {
		  error_log("hi");
		  $selectedpackage=$_POST['package_select'];
		  $selectedpackage1=trim($selectedpackage);
		  $result=$fgmembersite->selectedpackage($selectedpackage1);
		  error_log(print_r($result,true));
		  echo
		  "<table class='table table-striped'>
							
							<tr>
							<th>Sl no</th>
							<th>category</th>
							<th>sub-category</th>
							<th>vehicles</th>
							<th>email id</th>
							<th>mobile number</th>
							<th>comment</th>
							</tr>";
							for($i=0; $i<count($result); $i++)
							{
								
								echo
								"<tr>
								<td>{$result[$i]['slno']}</td>
								<td>{$result[$i]['category']}</td>
								<td>{$result[$i]['subcategory']}</td>
								<td>{$result[$i]['vehicles']}</td>
								<td>{$result[$i]['Email']}</td>
								<td>{$result[$i]['mobilenumber']}</td>
								<td>{$result[$i]['comment']}</td>
								</tr>";
							}
							
							echo "</table>";
	  }
	  
	  
	  
	  
	  
	  
	  
	  
	  
	  /* all details form information storing */
		if(isset($_POST['saveall'])=="save") //checking saveall hidden fiels value is save or not
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