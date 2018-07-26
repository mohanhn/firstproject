<?PHP
/*
    The Leasing Pros LLC
    Developer: Manoj Choudhary 
    Date: April 13, 2017

*/
//require_once("class.phpmailer.php");
//require_once("formvalidator.php");

class FGMembersite
{
    var $admin_email;
    var $from_address;
    var $username;
    var $pwd;
    var $database;
    var $tablename;
    var $connection;
    var $rand_key;
	var $template;
	var $loopTablesCat = array(0 => 'APPLIANCES', 1 => 'UNITADDRES', 2 => 'LEASETERMS', 3 => 'FINANCIALS', 4 => 'LIMITS', 5 => 'RESIDENT', 6 => 'ECONTACT', 7 => 'PETS', 8 => 'VEHICLEPAR', 9 => 'STORAGE', 10 => 'PROPERTY', 11 => 'UTILITIES', 12 => 'MISC');
	var $loopTablesNames = array(0 => 'applianceinforp', 1 => 'unitaddressrp', 2 => 'leasetermrp', 3 => 'financialinforp', 4 => 'limitinforp', 5 => 'residentinforp', 6 => 'econtactinforp', 7 => 'petinforp', 8 => 'vehicleparkrp', 9 => 'storageinforp', 10 => 'propertyinforp', 11 => 'utilityinforp', 12 => 'miscinforp');
	var $loopTablesprefix = array(0 => 'apprp', 1 => 'unitrp', 2 => 'lstrmrp', 3 => 'finrp', 4 => 'limitrp', 5 => 'resrp',6 => 'econtactrp', 7 => 'petrp', 8 => 'vehrp', 9 => 'storerp', 10 => 'proprp', 11 => 'utilityrp', 12 => 'miscrp');
					
    
	/* FTP REMOTE SERVER SETUP */
	var $ftphost = 'mybangalorerealtors.com';
	var $ftpusr = 'manojc@mybangalorerealtors.com';
	var $ftppass = 'ZB;qIVIA0^+T';	
	var $ftpbaseurl = "mybangalorerealtors.com/mybangalorerealtors.com/manojc/";
	

    var $error_message;
    
    //-----Initialization -------
	function __construct()
    {
        return;
    }

    function FGMembersite()
    {
        $this->sitename = 'www.leasingpro.com';
        $this->rand_key = 'vFabEWpUwrRIQWk';
    }

    function InitDB($host,$uname,$pwd,$database,$tablename)
    {
        $this->db_host   = $host;
        $this->username  = $uname;
        $this->pwd       = $pwd;
        $this->database  = $database;
        $this->tablename = $tablename;
        
    }
    function SetAdminEmail($email)
    {
        $this->admin_email = $email;
    }
    
    function SetWebsiteName($sitename)
    {
        $this->sitename = $sitename;
    }
    
    function SetRandomKey($key)
    {
        $this->rand_key = $key;
    }
    
    //-------Main Operations ----------------------
    
    // prequal DB login 
    function DBLogin()
    {

        //$this->connection = mysql_connect($this->db_host,$this->username,$this->pwd);
        $this->connection = mysqli_connect($this->db_host,$this->username,$this->pwd,$this->database);
        if(!$this->connection)
        {   
            $this->HandleDBError("Database Login failed! Please make sure that the DB login credentials provided are correct");
            return false;
        }
        
        return true;
    }
	 
 




	function ftpConnect()
    {
		$conn_id = ftp_connect($this->ftphost, 21);
		if($conn_id){
			if(ftp_login($conn_id, $this->ftpusr, $this->ftppass)){
				return $conn_id;
			}
			else{
				$this->HandleDBError("FTP Login failed! Please make sure that the FTP login credentials provided are correct");
				return false;
			}
		}
		else
		{
			$this->HandleDBError("FTP Connection failed! Please make sure that the FTP  credentials provided are correct");
			return false;
		}	
	}

	/*
	=======================================================
	      function  to display the default repo
    =======================================================
	*/
	
	function defaultrepo()
	{
		if(!$this->DBLogin())
        {
            $this->HandleError("Database login failed!");
            return false;
        }
		$qry = "select * from defaultrp";
		$result = mysqli_query($this->connection,$qry);
		if(!$result || mysqli_num_rows($result) <= 0)
		{
			return false;
		}
		$ar= [];
		while($row = mysqli_fetch_assoc($result))
		{
			array_push($ar, $row);  
		}
		mysqli_close($this->connection);
		return $ar;
	}
	
	/*
	=======================================================
	      function to insert new template name
    =======================================================
	*/
	 
	function addNewTemplate($temprpname, $temprpmemberid, $mastermemberno)
	{	$recordCount = 0;
		if(!$this->DBLogin())
			{	
				$this->HandleError("Database login failed!");
				return false;
			}
			$user = $_SESSION['user']; 
			$insertTemplateName = "INSERT INTO templaterp(temprpmemberid,tempmemberno, temprpname, temprpcreatedby) VALUES ('".$temprpmemberid."','".$mastermemberno."', '".$temprpname."', '".$user."')";
			// Insert sanitize string in record
			$check_sql = "SELECT * FROM templaterp WHERE temprpname = '".$temprpname."' AND temprpmemberid='".$temprpmemberid."';"; 
			$check_sql_query=mysqli_query($this->connection,$check_sql);
			//error_log(print_r($check_sql_query,true));
			/* if template name exists return false */
			if (mysqli_num_rows($check_sql_query) > 0) 
			{
				mysqli_close($this->connection);	
				$exists="exists";
				return $exists;

			} 
			/* if template name don't exists  return true */
			else if(mysqli_num_rows($check_sql_query)==0)
			{	 
				$insert_row = mysqli_query($this->connection,$insertTemplateName);
				if($insert_row)
				{	
					//Record was successfully inserted, respond result back to index page
					$last_id = mysqli_insert_id($this->connection); //Get ID of last inserted row from MySQL
					//mysqli_close($this->connection);
					
					/* 
					duplicateRepo() - Used to provide default values in all repositories tables
					1st argument - default category
					2nd argument - id of the last record added in template repository
					3nd argument - Target Table name
					4nd argument - Prefix  - the table attributes
					*/
					for($counter=0;$counter<(count($this->loopTablesCat));$counter++)
					{
						$categoryName = $this->loopTablesCat[$counter];
						$tableName = $this->loopTablesNames[$counter];
						$tablePrefix = $this->loopTablesprefix[$counter];
						$insert = $this->duplicateRepo($categoryName,$last_id,$tableName, $tablePrefix);
					}	
					return($last_id);
				}
				else
				{
					//header('HTTP/1.1 500 '.mysql_error()); //display sql errors.. must not output sql errors in live mode.
					header('HTTP/1.1 500 Looks like mysql error, could not insert record!');
					mysqli_close($this->connection);
					return("error");
					 
				}
			}
			/* unknown result, return false  */
			else 
			{
			  mysqli_close($this->connection);
			   return("unknown");
			 
			}
		
		
	}
	
	/*
	=======================================================
	       Copy default repository to new tables
    =======================================================
	*/
	 
	function duplicateRepo($defrpcategory, $templateId, $tagetTable, $pre)
	{	/* Create Table if not exist*/ 
		$ceateTable = "CREATE TABLE IF NOT EXISTS {$tagetTable} (
					  {$pre}id bigint(11) NOT NULL AUTO_INCREMENT,
					  {$pre}tempid bigint(11) NOT NULL,
					  {$pre}feedid bigint(11) NOT NULL,
					  {$pre}subcategory varchar(20) NOT NULL DEFAULT '',
					  {$pre}fieldid varchar(20) NOT NULL DEFAULT '',
					  {$pre}dropdown varchar(20) NOT NULL DEFAULT '',
					  {$pre}select tinyint(1) DEFAULT '0',
					  {$pre}fieldname varchar(100) NOT NULL,
					  {$pre}optlabel varchar(100) DEFAULT '',
					  {$pre}fieldtype varchar(20) NOT NULL,
					  {$pre}defaultreq tinyint(1) DEFAULT '0',
					  {$pre}req tinyint(1) DEFAULT '0',
					  {$pre}hide tinyint(1) DEFAULT '0',
					  {$pre}edit tinyint(1) DEFAULT '0',
					  {$pre}created timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
					  {$pre}lastmodified timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
					  {$pre}modifiedby varchar(30) NOT NULL DEFAULT '',
					  {$pre}order bigint(11) NOT NULL DEFAULT '0',
					  PRIMARY KEY ({$pre}id)
					) ENGINE=InnoDB DEFAULT CHARSET=latin1;";
		$create = mysqli_query($this->connection,$ceateTable);
		 
		$select = "SELECT * from defaultrp WHERE defrpcategory = '{$defrpcategory}'";
		$result = mysqli_query($this->connection,$select);
		if(!$result || mysqli_num_rows($result) <= 0)
		{
			return false;
		}
		else
		{	
			$values ="";
			while($row = mysqli_fetch_assoc($result))
			{	
				$values .="({$templateId},{$row['defrpfeedid']},'{$row['defrpsubcategory']}','{$row['defrpfieldid']}','{$row['defrpdropdown']}',{$row['defrpselect']},'{$row['defrpfieldname']}','{$row['defrpoptlabel']}','{$row['defrpfieldtype']}',{$row['defrpdefaultreq']},{$row['defrpreq']},{$row['defrphide']},{$row['defrpedit']},{$row['defrporder']}),";
				
				//array_push($ar, $row);  
			}
			$final_values = "VALUES ".rtrim($values,",").";";
			$insert = "INSERT INTO {$tagetTable}({$pre}tempid, {$pre}feedid, {$pre}subcategory, {$pre}fieldid, {$pre}dropdown,{$pre}select, {$pre}fieldname, {$pre}optlabel, {$pre}fieldtype, {$pre}defaultreq, {$pre}req, {$pre}hide, {$pre}edit, {$pre}order) {$final_values}";
			 
			$insertResult = mysqli_query($this->connection,$insert);
			return true;
		}
	} 
	
	/*
	=======================================================
	      Function to display selected template data
    =======================================================
	*/	
	
	function showSelectedTemplate($temprpuid,$temprpmemberid)
	{	$this->template = $_SESSION['template'];
		return true;
		 
		 
	}
	
	
	/*
	=======================================================
	      function  to retrieve all Templlate List for Selected Master
    =======================================================
	*/	
	function listTemplates($templateID,$mastermemberid,$mastermemberno)
	{
		if(!$this->DBLogin())
        {
            $this->HandleError("Database login failed!");
            return false;
        }
		
		$qry ="select * from templaterp where 	temprpmemberid = '{$mastermemberid}' AND tempmemberno='{$mastermemberno}' AND temprpuid <>  {$templateID}";
		 
		$templateNames = mysqli_query($this->connection, $qry);
		if(!$templateNames || mysqli_num_rows($templateNames) <= 0)
		{
			mysqli_close($this->connection);
		    return "NoRecordError";
		}
		else
		{
			$templateList= [];
			 
			while($row = mysqli_fetch_assoc($templateNames))
			{
				array_push($templateList, $row);  
				
			}
			mysqli_close($this->connection);
			return $templateList;
		}	
		
	}
	
	/*
	=======================================================
	      function  to retrieve all DropDowns List items
    =======================================================
	*/	
	function getDropDownData()
	{
		if(!$this->DBLogin())
        {
            $this->HandleError("Database login failed!");
            return false;
        }
		
		$qry ="select * from dropdownrp ORDER By dropdownrpvalue ASC";
		$dropdownrpname  = "select DISTINCT dropdownrpname from dropdownrp";
		$distinctname = mysqli_query($this->connection, $dropdownrpname);
		if(!$distinctname || mysqli_num_rows($distinctname) <= 0)
		{
			mysqli_close($this->connection);
		    return "NoRecordError";
		}
		else
		{
			$name= [];
			$dropdownList = [];
			while($row = mysqli_fetch_assoc($distinctname))
			{
				array_push($name, $row['dropdownrpname']);  
				
			}
			 
			$selectAll  = mysqli_query($this->connection, $qry);
			 
			while($row = mysqli_fetch_assoc($selectAll))
			{
				for($i=0; $i<count($name); $i++)
				{
					if($row['dropdownrpname']==$name[$i])
					{
							$dropdownList[$name[$i]][] = $row;
							 
					}	
					  
				}	
				
			}
			mysqli_close($this->connection);
			return $dropdownList;
		}	
		
	}
	
	
	/*
	=======================================================
	      Function to select data from tables
    =======================================================
	*/	
	function displayAllTables($mastermemberid, $mastermemberno,$templateId)
	{		
		if(!$this->DBLogin())
		{	
			$this->HandleError("Database login failed!");
			return false;
		}
	    
		
		if(isset($templateId) && ($templateId!=''))
		{ 
			$membertemplate = $templateId;
			$checkMember = "select * from templaterp where 	temprpuid={$membertemplate} AND 	temprpmemberid='{$mastermemberid}' AND tempmemberno='{$mastermemberno}'";
			 
			$checkMemberResult = mysqli_query($this->connection,$checkMember);
			if(!$checkMemberResult || mysqli_num_rows($checkMemberResult) <= 0)
			{
			  error_log("Error member or template details are invalid");	
			  mysqli_close($this->connection);
			  return false;
			}
			else
			{	
				$qry ="";
				for($counter = 0; $counter<(count($this->loopTablesCat));$counter++)
				{
					$categoryName = $this->loopTablesCat[$counter];
					$tableName = $this->loopTablesNames[$counter];
					$tablePrefix = $this->loopTablesprefix[$counter];
					$tableExists = "DESCRIBE {$tableName}"; 
					 
					if(mysqli_query($this->connection,$tableExists)) {
						$qry  = $qry."  SELECT '{$categoryName}'  table_name, A{$counter}.*
								    FROM {$tableName} as A{$counter} WHERE {$tablePrefix}tempid = ".$membertemplate." AND {$tablePrefix}subcategory <> 'system'
									UNION";
					}				
				}	
				
				$qry = rtrim($qry," UNION ")." ORDER BY 	apprpfeedid ASC;";
				 
				$result = mysqli_query($this->connection, $qry);
				
					if(!$result || mysqli_num_rows($result) <= 0)
					{
					  error_log("Error in feteching the records from the repositories or no Matched Records found");	
					  mysqli_close($this->connection);
					  return false;
					}
					else
					{
						$ar2= [];
						while($row = mysqli_fetch_assoc($result))
						{
							array_push($ar2, $row);  
						}
						mysqli_close($this->connection);
						$resultset = [];
						 
						foreach($ar2 as $temp_row) 
						{
							if(!empty($temp_row['table_name']))
							{
							   $tableName = $temp_row['table_name'];
							   $resultset[$tableName][] =  $temp_row; 
							}
							else
							{
								
							}
						}
					 	 
						return $resultset;
					}
			}	
		}
		
		
	}
	
	/*
	=======================================================
	      Function to Update Repository SETUP data tables
    =======================================================
	*/	
	
	function updateTable($table,$defaultcategory,$pre,$row,$mastermemberid, $mastermemberno){
		 
		if(!$this->DBLogin())
		{	
			$this->HandleError("Database login failed!");
			return false;
		}
		
		/*-----------------------------------------------
			Select the Unique Members the defaultdata table   
		------------------------------------------------*/
		$unique_members  = "SELECT DISTINCT defaultmemberno, defaultmemberid, defaultmembername, defaultpropertyid FROM `defaultdata` WHERE defaulttempid ={$_SESSION['template']} AND defaultmembermasterid = '{$mastermemberid}' AND defaultmembermasterno = '{$mastermemberno}'"; 
		 
		$unique_members_result = mysqli_query($this->connection,$unique_members);
		$mem = [];
		if(!$unique_members_result || mysqli_num_rows($unique_members_result) <= 0)
		{
			//nothing to do here	
		}
		else
		{		
			while($selectedRow = mysqli_fetch_assoc($unique_members_result))
			{
				array_push($mem, $selectedRow);  
			}
		}
		/*-----------------------------------------------
			Select the fields from the defaultdata table   
		------------------------------------------------*/
		$select  = "SELECT  DISTINCT defaultfeedid FROM `defaultdata` WHERE defaulttempid ={$_SESSION['template']} AND defaultmembermasterid = '{$mastermemberid}' AND defaultmembermasterno = '{$mastermemberno}'"; 
		 
		$check_result = mysqli_query($this->connection,$select);
		$def = [];
		if(!$check_result || mysqli_num_rows($check_result) <= 0)
		{
			//nothing to do here	
		}
		else
		{		
			while($selectedRow = mysqli_fetch_assoc($check_result))
			{
				array_push($def, $selectedRow);  
			}
		}
		 
		/*-----------------------------------------------
			SET THE CATEGORY for  defaultdata table   
		------------------------------------------------*/
		/* $defaultcategory = $this->table_category($table);
		// identify the table column  prefix
		$pre =  $this->table_prefix($table); */
		
		// Feed Id Array 
		$feeds = $_POST['feedid'];
		$update_status = 1;
		$user = $_SESSION['user'];
		// Looping through the available rows
		for ($j = 0; $j < count($row); ++$j) 
		{		 			
				$rowId = $row[$j]; 				 //Row Number 
				$feed_id = $feeds[$j];			 //Array index to get feedid
				 
				 
				$column_loop = $_POST[$feed_id]; //feed id value
				$ar = [];
					/*
					$ar[0].....select
					$ar[1].....optlabel
					$ar[2].....defaultreq
					$ar[3].....req
					$ar[4].....hide
					$ar[5].....edit
					
					$ar[6].....label
					$ar[7].....filed type
					
					$ar[8].....subcategory type
					$ar[9].....fieldid type
					$ar[10].....order type
					*/
				for ($i=0; $i <= 5; $i++) 
				{	 
					if(isset($column_loop[$i])){
						// conditioon check to identify if it is a check box or an text input filed
						if($i==1)
						{
						  $ar[$i]=$column_loop[$i];
						}
						  else
						{
						  $ar[$i]=1;
						}  
					}
					else{
						
						if($i==1)
						{ $cc = "";
						  $ar[$i]=$cc;
						} 
						else{
							$ar[$i]=0;
						}
						 
					}
				}
				$ar[6] = $column_loop[6];
				$ar[7] = $column_loop[7];		
				$ar[8] = $column_loop[8];		
				$ar[9] = $column_loop[9];		
				$ar[10] = $column_loop[10];		
				$ar[11]	= $column_loop[11];	
				 
				$query = "UPDATE {$table} SET {$pre}select={$ar[0]},{$pre}optlabel='{$ar[1]}',{$pre}defaultreq={$ar[2]},{$pre}req={$ar[3]},{$pre}hide={$ar[4]},{$pre}edit={$ar[5]}, {$pre}modifiedby='{$user}'  WHERE {$pre}id={$rowId} AND {$pre}feedid={$feed_id} AND {$pre}tempid={$_SESSION['template']}";
				 
				$result = mysqli_query($this->connection,$query);
				 
				
				 
				if(!$result)
				{	 
					$update_status = 0;
					error_log("DATA NOT UPDATED TO TABLES");
				}
				else
				{	/* Updating the defaultdata */
					if(!empty($def))
					{
						 	
							/* Case 1: Defaultdata already has a record 
									   SubCase 1: default table, select is unchacked  ---- delete from defaultdata
									   SubCase 2: default table, select is checked but defaultRequired is unchecked ---- delete from defaultdata
									   SubCase 3: default table,  select is checked and defaultRequired is checked	---- update optionalLable filed in defaultdata	
									   
							   Case 2: Defaultdata has No record 
									   SubCase 1: default table, select is unchacked  ---- do nothing
									   SubCase 2: default table, select is checked but defaultRequired is unchecked ---- do nothing
									   SubCase 3: default table,  select is checked and defaultRequired is checked	---- Insert the new row in defaultdata			   
							*/	
							
							
							/* Case 1: Defaultdata already has a record  */
							$set_check = 0;
							foreach($def as $check)
							{
								if($check['defaultfeedid']==$feed_id)
								{
									$set_check = 1;
								}
							} 
							 
							if ($set_check == 1) 
							{	/* Case 1 --> SubCase 1    
								   Case 1 --> SubCase 2
								 */ 
								if(($ar[0]==0) || ($ar[2]==0))
								{
									$delete_query = "DELETE FROM defaultdata WHERE defaultfeedid ={$feed_id} AND defaulttempid={$_SESSION['template']} AND defaultmembermasterid = '{$mastermemberid}' AND defaultmembermasterno = '{$mastermemberno}'"; 
								 
									$result = mysqli_query($this->connection,$delete_query);
									if (mysql_affected_rows() > 0) 
									{
										// Deleted successfull
									}
									else 
									{
										error_log('unable to delete from defaultdata');
									}
									
								}
								/* Case 1 --> SubCase 3 */
								else if(($ar[0]==1) && ($ar[2]==1))
								{	
									if(!empty($mem))
									{
									   foreach($mem as $rowx)
										{
											$update_optional_label = "UPDATE  defaultdata SET defaultoptlabel='{$ar[1]}' WHERE defaultfeedid ={$feed_id} AND defaulttempid={$_SESSION['template']} AND defaultmembermasterid = '{$mastermemberid}' AND defaultmembermasterno = '{$mastermemberno}' AND defaultmemberid='{$rowx['defaultmemberid']}' AND defaultmemberno='{$rowx['defaultmemberno']}' AND defaultpropertyid={$rowx['defaultpropertyid']}"; 
											 
											$update_result = mysqli_query($this->connection,$update_optional_label);
											if (mysqli_affected_rows($this->connection) <= 0) 
											{
												error_log("Upation Error, failed to update the defaultoptlabel to defaultdata 2");
											}
										}
									}		
									
									
								}  
								
							}
							
							/* Case 2 --> SubCase 3 */
							elseif(($set_check == 0) && ($ar[0]==1) && ($ar[2]==1)) 
							{		/* else if(($key['defaultfeedid']  != $feed_id) && ($ar[0]==1) && ($ar[2]==1)) */
									if(!empty($mem))
									{
									   foreach($mem as $rowx)
										{
											$order = 0;
											if(isset($ar[10]))
											{	if($ar[10]!='')
												{
													$order = $ar[10];
												}
												else
												{
													$order = 0;
												}	
												
											}
											
											$values = "('{$rowx['defaultmemberid']}','{$rowx['defaultmemberno']}','{$rowx['defaultmembername']}','{$mastermemberid}','{$mastermemberno}','{$defaultcategory}',{$_SESSION['template']},{$feed_id},'{$ar[8]}','{$ar[9]}','{$ar[11]}','{$ar[7]}','{$ar[6]}','{$ar[1]}','','{$ar[2]}',{$rowx['defaultpropertyid']},'{$_SESSION['user']}',{$order})";
											$values = rtrim($values," , ").";";
											$insert  = "INSERT INTO defaultdata (defaultmemberid,defaultmemberno,defaultmembername,defaultmembermasterid,defaultmembermasterno,defaultcategory,defaulttempid,defaultfeedid,defaultsubcategory,defaultfieldid,defaultdropdown,defaultfieldtype,defaultfieldname,defaultoptlabel,defaultvalue,defaultedit,defaultpropertyid,defaultmodifiedby,defaultorder) VALUES {$values}";				
											 
											 
											$insert_result = mysqli_query($this->connection,$insert);
											if (mysqli_affected_rows($this->connection) <= 0) 
											{
												error_log("Upation Error, failed to update the defaultoptlabel to defaultdata");
											} 
										}		
									}
									
							}
							else
							{
								error_log("Unknown Error");
							}
						 
					}		
				}
				 
		}
		mysqli_close($this->connection);
		if($update_status == 1)
		{ return("upateSuccess");
			
		
		}
		else
		{	error_log("DATA NOT UPDATED TO TABLES Please Check updateTable() function called from Ajax file");
			return("upateError");
		}
	}
	
	/*
	=======================================================
	      Function to copy  template values
    =======================================================
	*/	
	function copySelectedTemplate($copy_template_id,$newTemplateId,$mastermemberid,$mastermemberno)
	{
		if(!$this->DBLogin())
		{	
			$this->HandleError("Database login failed!");
			return false;
		}
		
		$check ="select * from templaterp where 	temprpmemberid = '{$mastermemberid}' AND tempmemberno='{$mastermemberno}' AND temprpuid={$copy_template_id}";
		$checkTemplate = mysqli_query($this->connection,$check); 
		if(!$checkTemplate || mysqli_num_rows($checkTemplate) <= 0)
		{
			mysqli_close($this->connection);
			return false;
		}
		
		else if(mysqli_num_rows($checkTemplate) == 1)
		{
			mysqli_close($this->connection);
			 
			$copyfromTempRecords = $this->displayAllTables($mastermemberid, $mastermemberno,$copy_template_id);
			
			
			if(!empty($copyfromTempRecords))
			{	
				if(!$this->DBLogin())
				{	
					$this->HandleError("Database login failed!");
					return false;
				} 

				
				for($tableCount = 0; $tableCount < count($this->loopTablesCat); $tableCount++) 
				{
					$currentCategory  = $this->loopTablesCat[$tableCount];
					$targetTable = $this->loopTablesNames[$tableCount];
					$pre = $this->loopTablesprefix[$tableCount];
					if(!empty($copyfromTempRecords[$currentCategory]))
					{	
						
						foreach($copyfromTempRecords[$currentCategory] as $row)
						{
							$update = "";
							$update  =" update  {$targetTable} set {$pre}select={$row['apprpselect']}, {$pre}optlabel='{$row['apprpoptlabel']}',{$pre}defaultreq={$row['apprpdefaultreq']},{$pre}req={$row['apprpreq']},{$pre}hide={$row['apprphide']},{$pre}edit={$row['apprpedit']},{$pre}order={$row['apprporder']} where {$pre}tempid={$newTemplateId} AND {$pre}feedid={$row['apprpfeedid']}";

						    $insert_result = mysqli_query($this->connection,$update);			
						}

						
						
					}
				}

				// $this->copyAllTables($update);
				//$insert_result = mysqli_query($this->connection,$update);
				mysqli_close($this->connection);
				return "templateCopySuccess";
			}
			/* if empty $copyfromTempRecords */ 
			else
			{
				mysqli_close($this->connection);
				return "templateCopyError";
				
			}
		}
	}
	



	function copyAllTables($update){
		if(!$this->DBLogin())
		{	
			$this->HandleError("Database login failed!");
			return false;
		}
		

		mysqli_close($this->connection);
	    return true;
	}
	/*
	=======================================================
	      Function to verify delete templates request and Delete template 
    =======================================================
	*/	
	
	function deleteSelectedTemplate($delete_template_id,$temprpmemberid)
	{	
		if(!$this->DBLogin())
		{	
			$this->HandleError("Database login failed!");
			return false;
		}
		 
		// Verify delte request is genuine or not
		
		$check_template_member = "SELECT temprpuid from templaterp WHERE 	temprpuid ={$delete_template_id} AND temprpmemberid='{$temprpmemberid}'";  
		$result = mysqli_query($this->connection,$check_template_member);
		 
		if(!$result || mysqli_num_rows($result) <= 0)
		{
			mysqli_close($this->connection);
			return false;
		}
		else{
				$delete_query = "DELETE FROM templaterp WHERE temprpuid ={$delete_template_id} AND temprpmemberid='{$temprpmemberid}'"; 
				$result = mysqli_query($this->connection,$delete_query);
				 
				// Delete data from other tables
				 
				$delete = $this->deleteTemplate($delete_template_id,"propertyinforp", "proprp");
				$delete = $this->deleteTemplate($delete_template_id,"applianceinforp", "apprp"); 
				$delete = $this->deleteTemplate($delete_template_id,"financialinforp", "finrp");
				$delete = $this->deleteTemplate($delete_template_id,"leasetermrp", "lstrmrp");
				$delete = $this->deleteTemplate($delete_template_id,"limitinforp", "limitrp");
				$delete = $this->deleteTemplate($delete_template_id,"miscinforp", "miscrp");
				$delete = $this->deleteTemplate($delete_template_id,"petinforp", "petrp");
				$delete = $this->deleteTemplate($delete_template_id,"residentinforp", "resrp");
				$delete = $this->deleteTemplate($delete_template_id,"econtactinforp", "econtactrp");
				$delete = $this->deleteTemplate($delete_template_id,"unitaddressrp", "unitrp");
				$delete = $this->deleteTemplate($delete_template_id,"vehicleparkrp", "vehrp");
				$delete = $this->deleteTemplate($delete_template_id,"utilityinforp", "utilityrp");	
				$delete = $this->deleteTemplate($delete_template_id,"defaultdata", "default");	
				$delete = $this->deleteTemplate($delete_template_id,"assigntemplaterp", "assign");	
				$delete = $this->deleteTemplate($delete_template_id,"calulationsetuprp", "calrp");	
				
				mysqli_close($this->connection);
				return("tempDeleted");
			 
			
		}
		
	}
	
	/*
	=======================================================
	      Function delete templates -- called from  deleteSelectedTemplate(); 
    =======================================================
	*/
	function deleteTemplate($delete_template_id,$table_name, $pre)
	{
		if(!$this->DBLogin())
		{	
			$this->HandleError("Database login failed!");
			return false;
		}
		if($table_name == 'assigntemplaterp')
		{
			$delete_query = "DELETE FROM {$table_name} WHERE assignmemebertemp ={$delete_template_id}"; 
		}
		
		else
		{		
			$delete_query = "DELETE FROM {$table_name} WHERE {$pre}tempid ={$delete_template_id}"; 
		}	
		
		$result = mysqli_query($this->connection,$delete_query); 
		 
		mysqli_close($this->connection);
		unset($this->connection);
		if($result)
		{
			return true;
		}
		else
		{
			return false;
		}
	}	
	
	/*
	=======================================================
	      Function to identify table prefix
    =======================================================
	*/					 
	function table_prefix($table)
	{
		$pre ='';
		if($table == "propertyinforp")
			$pre="proprp";
		
		elseif($table == "applianceinforp")
			$pre="apprp";
		elseif($table == "financialinforp")
			$pre="finrp";
		elseif($table == "leasetermrp")
			$pre="lstrmrp";
		elseif($table == "limitinforp")
			$pre="limitrp";
		elseif($table == "miscinforp")
			$pre="miscrp";
		elseif($table == "petinforp")
			$pre="petrp";
		elseif($table == "residentinforp")
			$pre="resrp";
		elseif($table == "unitaddressrp")
			$pre="unitrp";
		elseif($table == "vehicleparkrp")
			$pre="vehrp";
		elseif($table == "storageinforp")
			$pre="storerp";		
		elseif($table == "utilityinforp")
			$pre="utilityrp";	
		else
			$pre="UNKNOWN_ERROR";	
		return($pre);
	}
	 
	/* function to display  template names */
	function templateNames($mastermemberid,$mastermemberno)
	{	if(!$this->DBLogin())
        {	
            $this->HandleError("Database login failed!");
            return false;
        }
		
		$qry = "select * from templaterp where temprpmemberid='".$mastermemberid."' AND 	tempmemberno ='".$mastermemberno."' ORDER BY temprpname * 1, temprpname ASC";
		$result = mysqli_query($this->connection,$qry);
		$ar = [];
		//$row = mysqli_fetch_assoc($result);
		
		$ar= [];
		while($row = mysqli_fetch_assoc($result))
		{
			array_push($ar, $row);  
		}
		mysqli_close($this->connection);
		return $ar;
	}
	
	/* SET THE CATEGORY for  defaultdata table */
	function table_category($table)
	{
		$table_category ='';
		
		if($table == "propertyinforp")
			$table_category="PROPERTY";
		elseif($table == "applianceinforp")
			$table_category="APPLIANCES";
		elseif($table == "financialinforp")
			$table_category="FINANCIALS";
		elseif($table == "leasetermrp")
			$table_category="LEASETERMS";
		elseif($table == "limitinforp")
			$table_category="LIMITS";
		elseif($table == "miscinforp")
			$table_category="MISC";
		elseif($table == "petinforp")
			$table_category="PETS";
		elseif($table == "residentinforp")
			$table_category="RESIDENT";
		elseif($table == "unitaddressrp")
			$table_category="UNITADDRES";
		elseif($table == "vehicleparkrp")
			$table_category="VEHICLEPAR";
		elseif($table == "storageinforp")
			$table_category="STORAGE";
		elseif($table == "utilityinforp")
			$table_category="UTILITIES";	
		else
			$table_category="UNKNOWN_ERROR";	
		return($table_category);
	}
		 
		 
		
		
		
	/*
	=======================================================
	      Function to retrive data from 
	Purpose:- Select the memebers assigned under a specific
	MasterMemberId & MasterMemberNo.	
    =======================================================
	*/	
	function memeberList($mastermemberid,$mastermemberno)
	{
		if(!$this->DBLogin())
        {	
            $this->HandleError("Database login failed!");
            return false;
        }
	 
		$qry = 'Select * from members where membermasterid = "' .$mastermemberid.'" and membermasterno = "' .$mastermemberno. '" ;';
		 
		$result = mysqli_query($this->connection,$qry);
		$ar = [];
		while($row = mysqli_fetch_assoc($result))
		{
			array_push($ar, $row);  
		}
		mysqli_close($this->connection);
		return $ar;
	}
	
	function updateTemplateAssign($mastermemberid,$mastermemberno,$memberid,$memberno,$membername,$membertemplate,$assignactive,$memberproperty)
	{
		if(!$this->DBLogin())
        {	
            $this->HandleError("Database login failed!");
            return false;
        }
		 
		 
		$check ="SELECT * FROM assigntemplaterp WHERE assignmastermemberid='".$mastermemberid."' AND assignmastermemberno='".$mastermemberno."' AND assignmemberid ='".$memberid."' AND assignmemberno='".$memberno."' AND  assignmemebertemp =".$membertemplate." AND assignactive='".$assignactive."' AND  assignmemberproperty=".$memberproperty."";
		$check2 = 'Select * from members where membermasterid = "' .$mastermemberid.'" and membermasterno = "' .$mastermemberno. '"and 	memberid="'.$memberid.'" and memberno="'.$memberno.'"';
		//$check3 = 'Select * from templaterp where membermasterid = "' .$mastermemberid.'" and membermasterno = "' .$mastermemberno. '"and 	memberid="'.$memberid.'" and memberno="'.$memberno.'"';
		$qry = "INSERT INTO assigntemplaterp(assignmastermemberid,assignmastermemberno,assignmemberid,assignmemberno,assignmemebertemp,assignactive,assignmemberproperty,assigncreatedby) 
				VALUES('".$mastermemberid."','".$mastermemberno."','".$memberid."','".$memberno."',".$membertemplate.",'".$assignactive."',".$memberproperty.",'".$_SESSION['user']."' )";
		
		 
		$check_result = mysqli_query($this->connection, $check);
		$check_result = mysqli_query($this->connection, $check);
		$check_result2 = mysqli_query($this->connection, $check2);
		
		if(!$check_result || mysqli_num_rows($check_result) <= 0)
		{	
			if(!$check_result2 || mysqli_num_rows($check_result2) <= 0)
			{	
				mysqli_close($this->connection);
				error_log("sql injection attack prevention");
				$arr = array ('response'=>'updateError');
				return $arr;
			}
			else
			{
				$check ="SELECT * FROM assigntemplaterp WHERE assignmastermemberid='".$mastermemberid."' AND assignmastermemberno='".$mastermemberno."' AND assignmemberid ='".$memberid."' AND assignmemberno='".$memberno."' AND  assignmemebertemp =".$membertemplate." AND assignactive='".$assignactive."'";
				
				if (mysqli_query($this->connection, $qry)) 
				{
					$lastid = mysqli_insert_id($this->connection);
					mysqli_close($this->connection);	
					/* Get the template fields for the selcted template from the template repositories tables */
					//$template_result_set = $this->initial_default_setup($lastid, $membertemplate);	
					 
					$resultset =[];
					$resultset2 =[];
					/*foreach($template_result_set as $temp_row)
					{
						if($temp_row['table_name'] == "APPLIANCES")
						{
						   $resultset['APPLIANCES'][] =  $temp_row; 
						}
						 elseif($temp_row['table_name'] == "FINANCIALS")
						{
							$resultset['FINANCIALS'][] =$temp_row;
							error_log("IAM IN 2 ");
						}
						 
						elseif($temp_row['table_name'] == "LEASETERMS")
						{
							$resultset['LEASETERMS'][] =$temp_row;
						}
						elseif($temp_row['table_name'] == "LIMITS")
						{
							$resultset['LIMITS'][] =$temp_row;
						}
						elseif($temp_row['table_name'] == "MISC")
						{
							$resultset['MISC'][] =$temp_row;
						}
						elseif($temp_row['table_name'] == "PETS")
						{
							$resultset['PETS'][] =$temp_row;
						}
						elseif($temp_row['table_name'] == "PROPERTY")
						{
							$resultset['PROPERTY'][] =$temp_row;
						}
						elseif($temp_row['table_name'] == "RESIDENT")
						{
							$resultset['RESIDENT'][] = $temp_row;
						}
						elseif($temp_row['table_name'] == "UNITADDRES")
						{
							$resultset['UNITADDRES'][] = $temp_row;
						}
						elseif($temp_row['table_name'] == "UTILITIES")
						{
							$resultset['UTILITIES'][] = $temp_row;
						}
						elseif($temp_row['table_name'] == "VEHICLEPAR")
						{
							$resultset['VEHICLEPAR'][] = $temp_row;
						} 	
						else
						{
							
						}
					}
					*/
					
					
					$arr = array ('response'=>'updated','lastId'=>$lastid);
					return $arr ;
				}
			   else
			   {
					mysqli_close($this->connection);
					$arr = array ('response'=>'updateError');
					return $arr;
			   }
			}			   
		}
		else if(mysqli_num_rows($check_result) <= 0){
			
			/* Update table records query will be executed here */
			/* Update table records query will be executed here */
		}
		else
		{
			error_log("Problem with the updateTemplateAssign()");
			$arr = array ('response'=>'exists');
			mysqli_close($this->connection);
			return $arr;
		}
	}
	/*
	=======================================================
	      Function to retrive data from  Assigntemplaterp Table
	Purpose:- Select the all rows assigned under a specific
				MasterMemberId & MasterMemberNo.	
    =======================================================
	*/	
	function get_selected_template($mastermemberid,$mastermemberno,$template_level)
	{
		
		if(!$this->DBLogin())
        {	
            $this->HandleError("Database login failed!");
            return false;
        }
		$qry ='';
		if($template_level == "property_level")
		{
		   $qry ="SELECT *, date(assignlastmodified) as assignlastmodifieddate  FROM assigntemplaterp WHERE assignmastermemberid='".$mastermemberid."' AND assignmastermemberno='".$mastermemberno."' AND assignmemberproperty!=-1 AND assignactive='Y'";	
		}
		else if($template_level == "member_level")
		{
			$qry ="SELECT *, date(assignlastmodified) as assignlastmodifieddate FROM assigntemplaterp WHERE assignmastermemberid='".$mastermemberid."' AND assignmastermemberno='".$mastermemberno."' AND assignmemberproperty=-1 AND assignactive='Y'";
		}
		else{
		  $qry ='';
		}
		//$qry ="SELECT * FROM assigntemplaterp WHERE assignmastermemberid='".$mastermemberid."' AND assignmastermemberno='".$mastermemberno."' AND assignmemberproperty=-1 AND assignactive='Y'";
		$result = mysqli_query($this->connection, $qry);
	
		$ar= [];
		while($row = mysqli_fetch_assoc($result))
		{
			array_push($ar, $row);  
		}
		mysqli_close($this->connection);
		return $ar;
	}
	
	
	
	/*
	=======================================================
	      Function to retrive data from  template tables
			Purpose:- First time default set up	
    =======================================================
	*/	
	
	
	
	function initial_default_setup($memberid,$memberno,$membername,$membertemplate,$mastermemberid,$mastermemberno,$current_property,$selected_property)
	{
		
  		if(!$this->DBLogin())
        {	
            $this->HandleError("Database login failed!");
            return false;
        }
		
		$check = "SELECT * FROM defaultdata WHERE defaultmemberid = '{$memberid}' AND defaultmemberno ='{$memberno}' AND defaultmembermasterid = '{$mastermemberid}' AND  defaultmembermasterno ='{$mastermemberno}' AND defaultpropertyid={$selected_property}";
		$checkResult =  mysqli_query($this->connection, $check);
		/* If defaultData Contains no entries for this member, insert all the records */
		if(!$checkResult || mysqli_num_rows($checkResult) <= 0)
		{	
				$qry ="";
				for($counter = 0; $counter<(count($this->loopTablesCat));$counter++)
				{
					$categoryName = $this->loopTablesCat[$counter];
					$tableName = $this->loopTablesNames[$counter];
					$tablePrefix = $this->loopTablesprefix[$counter];
					
					$qry  = $qry."  SELECT '{$categoryName}'  table_name, A1.*
								    FROM {$tableName} as A1 WHERE {$tablePrefix}tempid = ".$membertemplate." AND {$tablePrefix}select = 1 AND  {$tablePrefix}defaultreq = 1
									UNION";
				}	
				
				$qry = rtrim($qry," UNION ")." ORDER BY 	apprpfeedid ASC;";
				 
				$result = mysqli_query($this->connection, $qry);
				
					if(!$result || mysqli_num_rows($result) <= 0)
					{
					  error_log("Error in feteching the records from the repositories or no Matched Records found");	
					  mysqli_close($this->connection);
					  return false;
					}
					else
					{
						$ar2= [];
						while($row = mysqli_fetch_assoc($result))
						{
							array_push($ar2, $row);  
						}
						mysqli_close($this->connection);
						$resultset = [];
						 
						foreach($ar2 as $temp_row) 
						{
							if(!empty($temp_row['table_name']))
							{
							   $tableName = $temp_row['table_name'];
							   $resultset[$tableName][] =  $temp_row; 
							}
							else
							{
								
							}
						}
						 
						return $resultset;
					}
	
		}
		else
		{			/* 
					 'defaultcategory'  table_name,
					 'defaultfeedid'	apprpfeedid,
					 'defaulttempid'    apprptempid, 
					 'defaultedit'		apprpselect, 
					 'defaultfieldname' apprpfieldname, 
					 'defaultoptlabel'  apprpoptlabel, 
					 'defaultfieldtype' apprpfieldtype, 
					 'defaultcreated' apprpcreated, 
					 'defaultmodifiedby' apprpmodifiedby, 
				   */
					
					$qry ="SELECT  defaultcategory  as  'table_name', defaultfeedid	   as  'apprpfeedid', defaultfieldid	   as  'apprpfieldid', defaultdropdown	as  'apprpdropdown', defaultsubcategory	as  'apprpsubcategory',  defaulttempid as  'apprptempid', defaultedit		   as  'apprpselect', defaultfieldname    as  'apprpfieldname', defaultoptlabel     as  'apprpoptlabel',   defaultfieldtype    as  'apprpfieldtype', defaultmodifiedby   as  'apprpmodifiedby', A.*  FROM defaultdata A WHERE  defaultmemberno='".$memberno."' AND defaultmemberid = '{$memberid}' AND  	defaultmembermasterno = '{$mastermemberno}' AND  defaultmembermasterid='".$mastermemberid."' 
					 AND defaulttempid = {$membertemplate} AND defaultpropertyid={$selected_property} ORDER BY 	apprpfeedid ASC";
					 
					$checkResult = mysqli_query($this->connection, $qry);
					$ar2= [];
					while($row = mysqli_fetch_assoc($checkResult))
					{
						array_push($ar2, $row);  
					}
					mysqli_close($this->connection);
					$resultset = [];
					 
					foreach($ar2 as $temp_row) 
					{
						if(!empty($temp_row['table_name']))
						{
						   $tableName = $temp_row['table_name'];
						   $resultset[$tableName][] =  $temp_row; 
						}
						else
						{
							
						}
					}
					
					return $resultset;
			}
			
			
			
			
	}
		/*
	=======================================================
	      Function to insert data to   defaultData Table 
	=======================================================
	*/	
	function updateDefaultData($values,$templateId, $memberid,$memberno,$membername,$mastermemberid,$mastermemberno,$property_id)
	{
		if(!$this->DBLogin())
        {	
            $this->HandleError("Database login failed!");
            return false;
        }
		
		
		 
		$check  = "SELECT * FROM defaultdata WHERE defaultmemberid = '{$memberid}' AND defaultmemberno ='{$memberno}' AND defaultmembername = '{$membername}' AND defaultmembermasterid = '{$mastermemberid}' AND  defaultmembermasterno ='{$mastermemberno}' AND 	defaultpropertyid={$property_id}";
		$checkResult =  mysqli_query($this->connection, $check);
		 
		/* If defaultData Contains no entries for this member, insert all the records */
		if(!$checkResult || mysqli_num_rows($checkResult) <= 0)
		{
			$qry  = "INSERT INTO defaultdata (defaultmemberid,defaultmemberno,defaultmembername,defaultmembermasterid,defaultmembermasterno,defaultcategory,defaulttempid,defaultfeedid,defaultsubcategory,defaultfieldid,defaultdropdown,defaultfieldtype,defaultfieldname,defaultoptlabel,defaultvalue,defaultedit,defaultpropertyid,defaultmodifiedby) VALUES {$values}";
			$result = mysqli_query($this->connection, $qry);
			if($result){
				
				return true;
			}
			else
			{
				return false;
			}
		}
		/* if defaultData Contains  entries for this member, delete  old records and update the new records */
		else
		{ 
			$del  = "DELETE   FROM defaultdata WHERE defaultmemberid = '{$memberid}' AND defaultmemberno ='{$memberno}' AND defaultmembername = '{$membername}' AND defaultmembermasterid = '{$mastermemberid}' AND  defaultmembermasterno ='{$mastermemberno}' AND 	defaultpropertyid={$property_id}";
			$delResult =  mysqli_query($this->connection, $del);
			if($delResult)
			{
				$qry  = "INSERT INTO defaultdata (defaultmemberid,defaultmemberno,defaultmembername,defaultmembermasterid,defaultmembermasterno,defaultcategory,defaulttempid,defaultfeedid,defaultsubcategory,defaultfieldid,defaultdropdown,defaultfieldtype,defaultfieldname,defaultoptlabel,defaultvalue,defaultedit,defaultpropertyid,defaultmodifiedby) VALUES {$values}";
				$result = mysqli_query($this->connection, $qry);
				if($result)
				{	mysqli_close($this->connection);
					return true;
				}
				else
				{
					error_log("Record Updation failed due to unknown error, please try again");
					mysqli_close($this->connection);
					return false;
				}
			}
			else
			{
				error_log("Unable to delete records from the targeted table, please try again");
				mysqli_close($this->connection);
				return false;
			}
		}
	}
	 
	
	/* Delete the Template Defaults for a selected member */
	
	
	function deleteDefaultData($templateId, $memberid,$memberno,$membername,$mastermemberid,$mastermemberno,$property_id)
	{		
		if(!$this->DBLogin())
		{
		$this->HandleError("Database login failed!");
		return false;
		}
		 $del  = "DELETE   FROM defaultdata WHERE defaulttempid ={$templateId} AND defaultmemberid = '{$memberid}' AND defaultmemberno ='{$memberno}'  AND defaultmembermasterid = '{$mastermemberid}' AND  defaultmembermasterno ='{$mastermemberno}' AND defaultpropertyid={$property_id}";
		 error_log($del);
		 
		$del_data_Result =  mysqli_query($this->connection, $del);
		if(mysqli_affected_rows($this->connection) > 0)
		{ 
			$del_temp  = "DELETE   FROM assigntemplaterp WHERE 	assignmemebertemp ={$templateId} AND assignmemberid = '{$memberid}' AND assignmemberno ='{$memberno}' AND  assignmastermemberid = '{$mastermemberid}' AND  assignmastermemberno ='{$mastermemberno}' AND  assignmemberproperty={$property_id}";
			 
			$del_temp_Result =  mysqli_query($this->connection, $del_temp);
			if(mysqli_affected_rows($this->connection) > 0)
			{	 
				mysqli_close($this->connection);
				return true;
			}
			else
			{
				error_log("unable to delete from template Assignment table");
				mysqli_close($this->connection);
				return false;
			}
		}
		 
		/* check if any thing exists in the default data table for given Member  */
		else{
			/* Records exsistance check in default Data table */
			$sel  = "Select *  FROM defaultdata WHERE defaulttempid ={$templateId} AND defaultmemberid = '{$memberid}' AND defaultmemberno ='{$memberno}' AND defaultmembermasterid = '{$mastermemberid}' AND  defaultmembermasterno ='{$mastermemberno}' AND defaultpropertyid={$property_id}";
			 
			$checkResult =  mysqli_query($this->connection, $sel);
			if(!$checkResult || mysqli_num_rows($checkResult) <= 0)
			{	/* if no records found in default data table for this memeber, delete the template assignment  from  assigned template  Table */
				$del_temp2  = "DELETE   FROM assigntemplaterp WHERE 	assignmemebertemp ={$templateId} AND assignmemberid = '{$memberid}' AND assignmemberno ='{$memberno}' AND  assignmastermemberid = '{$mastermemberid}' AND  assignmastermemberno ='{$mastermemberno}' AND 	assignmemberproperty={$property_id}";
				 
				$del_temp_Result =  mysqli_query($this->connection, $del_temp2);
				if(mysqli_affected_rows($this->connection) > 0)
				{	 
					mysqli_close($this->connection);
					return true;
				}
				else
				{
					error_log("unable to delete from template Assignment table");
					mysqli_close($this->connection);
					return false;
				}
			}
			else
			{	
				error_log("Unknown Error Occured, either there is nothing to delete from Assigned Template Table for selected member or passed data is invalid");
				mysqli_close($this->connection);
				return false;
			}
		}
	}
	
	
	
	// new function to get the defaultdata from the 
	
	
	
		/*
	=======================================================
	      Function to show members to copy data 
	=======================================================
	*/	
	
	function copyDefaultData($templateId, $memberid,$memberno,$membername,$mastermemberid,$mastermemberno,$current_propertyId)
	{
		if(!$this->DBLogin())
		{
		$this->HandleError("Database login failed!");
		return false;
		}
		/* if the copy request is for member level only */
		if($current_propertyId== -1)
		{
			$qry  = "select * from assigntemplaterp where assignmastermemberid ='{$mastermemberid}' AND  assignmastermemberno = '{$mastermemberno}' AND  assignmemebertemp = {$templateId}  AND assignactive='Y'  AND assignmemberno != '{$memberno}' AND assignmemberid='{$memberid}' AND assignmemberproperty={$current_propertyId}";
		}
		/* if the copy request is for Property level only */
		else
		{
			$qry  = "select * from assigntemplaterp where assignmastermemberid ='{$mastermemberid}' AND  assignmastermemberno = '{$mastermemberno}' AND  assignmemebertemp = {$templateId}  AND assignactive='Y'  AND assignmemberno= '{$memberno}' AND assignmemberid='{$memberid}'  AND assignmemberproperty!= -1  AND assignmemberproperty!={$current_propertyId}";
			 
		}
		$check_result =  mysqli_query($this->connection, $qry); 
		if(!$check_result || mysqli_num_rows($check_result) <= 0)
		{
			mysqli_close($this->connection);
			return false;
		}
		else
		{
			$ar2= [];
			while($row = mysqli_fetch_assoc($check_result))
			{
				array_push($ar2, $row);  
			}
			mysqli_close($this->connection);
			return $ar2;
		}
		
	}
	
	
	function copyGetPropertyList($templateId, $memberid,$memberno,$membername,$mastermemberid,$mastermemberno, $current_propertyId)
	{
		if(!$this->DBLogin())
		{
		$this->HandleError("Database login failed!");
		return false;
		}
		$qry  = "select * from property";
	 
		$check_result =  mysqli_query($this->connection, $qry); 
		if(!$check_result || mysqli_num_rows($check_result) <= 0)
		{
			mysqli_close($this->connection);
			return false;
		}
		else
		{
			$ar2= [];
			while($row = mysqli_fetch_assoc($check_result))
			{
				array_push($ar2, $row);  
			}
			mysqli_close($this->connection);
			return $ar2;
		}
		
	}
	
	
	/*
	=======================================================
	      Function to List the Members 
		  who has property Assigned
	=======================================================
	*/		
	
function GetpropertyMeberList($mastermemberid, $mastermemberno)
{
	if(!$this->DBLogin())
	{
		$this->HandleError("Database login failed!");
		return false;
	}
	$qry = "SELECT * FROM members WHERE membermasterid='{$mastermemberid}' AND membermasterno='{$mastermemberno}' AND idmembers IN (SELECT propertymemberfeedid FROM property)";
	//$qry = "SELECT * FROM members INNER JOIN property ON idmembers = propertymemberfeedid where membermasterid='{$mastermemberid}' AND membermasterno='{$mastermemberno}' AND  	memberno='{$_SESSION['selectedMemberNo']}' AND memberid='{$_SESSION['selectedMemberId']}'";		
	
	 
	$result =  mysqli_query($this->connection, $qry); 
	if(!$result || mysqli_num_rows($result) <= 0)
	{
		mysqli_close($this->connection);
		return false;
	}
	else
	{
		$ar2= [];
		while($row = mysqli_fetch_assoc($result))
		{
			array_push($ar2, $row);  
		}
		mysqli_close($this->connection);
		return $ar2;
	}

	
	}
	
	
	function memberProprtyList($mastermemberid,$mastermemberno)
	{
		 
		if(isset($_SESSION['selectedMemberId']) && isset($_SESSION['selectedMemberNo']) && (($_SESSION['selectedMemberId'])!=''))
		{
			 
			
			
			if(!$this->DBLogin())
			{
				$this->HandleError("Database login failed!");
				return false;
			}
			
			
			
			$qry = "SELECT * FROM members INNER JOIN property ON idmembers = propertymemberfeedid where membermasterid='{$mastermemberid}' AND membermasterno='{$mastermemberno}' AND  	memberno='{$_SESSION['selectedMemberNo']}' AND memberid='{$_SESSION['selectedMemberId']}'";		
			 
			$result =  mysqli_query($this->connection, $qry); 
			if(!$result || mysqli_num_rows($result) <= 0)
			{
				mysqli_close($this->connection);
				return false;
			}
			else
			{
				$ar2= [];
				while($row = mysqli_fetch_assoc($result))
				{
					array_push($ar2, $row);  
				}
				mysqli_close($this->connection);
				 
				return $ar2;
			}
		}
	
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	/*
	=======================================================
	=======================================================
	      Lease Input Landing Page
		  Author - Manoj
	=======================================================
	=======================================================
	*/	

	/*
	=======================================================
	     Function to get the Package Details For a 
					Selected Member
	=======================================================
	*/	
	function packageDetails($leaseInput_memberId,$leaseInput_memberNo,$leaseInput_masterMemberId,$leaseInput_masterMemberNo,$lease_type)
	{
		if(!$this->DBLogin())
		{
			$this->HandleError("Database login failed!");
			return false;
		}
		$qry ="SELECT * FROM packageassign  JOIN package ON packageassign.pkgassignmasterid = package.pkgid WHERE pkgassignmastermemberid='{$leaseInput_masterMemberId}' AND  pkgassignmastermemberno='{$leaseInput_masterMemberNo}' AND pkgassignmemberid='{$leaseInput_memberId}' AND pkgassignmemberno='{$leaseInput_memberNo}'  AND pkgtype='{$lease_type}' ORDER BY pkgid";
		 
		$result =  mysqli_query($this->connection, $qry); 
		if(!$result || mysqli_num_rows($result) <= 0)
		{
			mysqli_close($this->connection);
			return false;
		}
		else
		{
			$ar2= [];
			while($row = mysqli_fetch_assoc($result))
			{
				array_push($ar2, $row);  
			}
			mysqli_close($this->connection);
			 
			return $ar2;
		} 
  }
	
	/*
	=======================================================
	     Function to get the Package Document Details
	=======================================================
	*/	
function packageDocumentDetails($leaseInput_memberId,$leaseInput_memberNo,$leaseInput_masterMemberId,$leaseInput_masterMemberNo,$pkgassignmasterid)
{
	if(!$this->DBLogin())
	{
		$this->HandleError("Database login failed!");
		return false;
	}
	$qry ="SELECT * FROM packagedetails JOIN package ON packagedetails.pkgdetailmasterid = package.pkgid JOIN documents ON packagedetails.pkgdetaildocid = documents.iddocuments WHERE pkgdetailmasterid={$pkgassignmasterid} AND  pkgdetailmastermemberid='{$leaseInput_masterMemberId}' AND  pkgdetailmastermemberno='{$leaseInput_masterMemberNo}'";
	 
	$result =  mysqli_query($this->connection, $qry); 
	if(!$result || mysqli_num_rows($result) <= 0)
	{
		mysqli_close($this->connection);
		return false;
	}
	else
	{
		$ar2= [];
		while($row = mysqli_fetch_assoc($result))
		{
			array_push($ar2, $row);  
		}
		mysqli_close($this->connection);
		 
		return $ar2;
	} 	

	
	}	
	
	
	/*
	=======================================================
	     Function to Generate Or Update 
		 MaterLeaseID and LeaseDocuments
	=======================================================
	*/
	function generateMasterLeaseId($leaseInput_memberId,$leaseInput_memberNo,$leaseInput_masterMemberId,$leaseInput_masterMemberNo,$leaseInput_type,$pkgassignid,$pkgassignmasterid,
	$leaseInput_package_name,$leaseInput_document_name,$leaseInput_document_details,$leaseInput_documentlist,$user_name,$propertyRowId,$propertyRowMemberFeedId,$propertyRowCode, 
	$leaseInput_property_name,$leaseInput_inquiry_no,$leaseInput_co_qualifier_id)
	{
		if(!$this->DBLogin())
		{
			$this->HandleError("Database login failed!");
			return false;
		}
		/* Logic Flow 
			 
		*/	
			if(($propertyRowId!=-1) && ($propertyRowMemberFeedId!= -1))
			{
				/* Check if given member, property, Details are Correct */
				$checkProperty = "SELECT * FROM property WHERE propertyid={$propertyRowId} AND propertymemberfeedid={$propertyRowMemberFeedId} AND propertycode='{$propertyRowCode}'";	
				$resuyltCheckProperty =  mysqli_query($this->connection, $checkProperty);
				if(!$resuyltCheckProperty || mysqli_num_rows($resuyltCheckProperty) <= 0)
				{    
					mysqli_close($this->connection);
					unset($this->connection);
					return "noPropertyFound";
				}
			}	
		 
			
			$checkPackage = "SELECT * FROM  packageassign WHERE pkgassignid={$pkgassignid} AND pkgassignmasterid={$pkgassignmasterid} AND pkgassignmastermemberid='{$leaseInput_masterMemberId}' AND pkgassignmastermemberno='{$leaseInput_masterMemberNo}' AND pkgassignmemberid='{$leaseInput_memberId}' AND pkgassignmemberno='{$leaseInput_memberNo}'"; 
			$resuyltCheckPackage =  mysqli_query($this->connection, $checkPackage);
			
			/* if the Given Property and Package Details exist*/
			if(!$resuyltCheckPackage || mysqli_num_rows($resuyltCheckPackage) <= 0)
			{    
		        mysqli_close($this->connection);
				unset($this->connection);
				return "noPropertyFound";
			}
			else
			{
				/* Get the Assigned Template Details from 'assigntemplaterp' table */
				$getTemplate = "SELECT * FROM assigntemplaterp WHERE 	assignmemebertemp={$_SESSION['slectedTemplate']} AND assignmastermemberid='{$leaseInput_masterMemberId}' AND assignmastermemberno='{$leaseInput_masterMemberNo}' AND assignmemberid='{$leaseInput_memberId}' AND assignmemberno='{$leaseInput_memberNo}' AND  assignmemberproperty={$propertyRowId}";
				 error_log($getTemplate);
				
				$resultGetTemplate = mysqli_query($this->connection, $getTemplate);
				 
				
			    /* If no template assigned for selected Member, Exit */
				if(!$resultGetTemplate || mysqli_num_rows($resultGetTemplate) <= 0)
				{	
					mysqli_close($this->connection);
					unset($this->connection);
				    return "noTemplateAssigned";
				}
				else
				{	
					$masterleaseassigntemplaterpid ='';
					$masterleasetemplateid ='';
					$masterleasetemplatename ='';
					
					while($row = mysqli_fetch_assoc($resultGetTemplate))
					{
					  $masterleaseassigntemplaterpid = $row['assignid'];
					  $masterleasetemplateid =$row['assignmemebertemp'];
					  $masterleasetemplatename = 'ssr';
					}
					
					/* Verify - Calculation setup is  set */
					$checkCalSetup = "SELECT * FROM  calulationsetuprp WHERE 	calrpmastermemberid='{$leaseInput_masterMemberId}' AND 	calrpmastermemberno = '{$leaseInput_masterMemberNo}' AND 	calrptempid = {$masterleasetemplateid}"; 
					error_log($checkCalSetup);
					$resultCalSetup = mysqli_query($this->connection, $checkCalSetup);
					if(!$resultCalSetup || mysqli_num_rows($resultCalSetup) <= 0)
					{	
						mysqli_close($this->connection);
						unset($this->connection);
						return "noCalculationSetup_{$masterleasetemplateid}";
					}
					/* Verify - Defaults are set */
					 
					$check1 =  $this->defaultCheck("applianceinforp","apprp",$masterleasetemplateid,$propertyRowId,$leaseInput_memberId,$leaseInput_memberNo,$leaseInput_masterMemberId,$leaseInput_masterMemberNo);
					$check2 =  $this->defaultCheck("residentinforp","resrp",$masterleasetemplateid,$propertyRowId,$leaseInput_memberId,$leaseInput_memberNo,$leaseInput_masterMemberId,$leaseInput_masterMemberNo);
					$check3 =  $this->defaultCheck("financialinforp","finrp",$masterleasetemplateid,$propertyRowId,$leaseInput_memberId,$leaseInput_memberNo,$leaseInput_masterMemberId,$leaseInput_masterMemberNo);
					$check4 =  $this->defaultCheck("leasetermrp","lstrmrp",$masterleasetemplateid,$propertyRowId,$leaseInput_memberId,$leaseInput_memberNo,$leaseInput_masterMemberId,$leaseInput_masterMemberNo);
					$check5 =  $this->defaultCheck("limitinforp","limitrp",$masterleasetemplateid,$propertyRowId,$leaseInput_memberId,$leaseInput_memberNo,$leaseInput_masterMemberId,$leaseInput_masterMemberNo);
					$check6 =  $this->defaultCheck("miscinforp","miscrp",$masterleasetemplateid,$propertyRowId,$leaseInput_memberId,$leaseInput_memberNo,$leaseInput_masterMemberId,$leaseInput_masterMemberNo);
					$check7 =  $this->defaultCheck("petinforp","petrp",$masterleasetemplateid,$propertyRowId,$leaseInput_memberId,$leaseInput_memberNo,$leaseInput_masterMemberId,$leaseInput_masterMemberNo);
					$check8 =  $this->defaultCheck("propertyinforp","proprp",$masterleasetemplateid,$propertyRowId,$leaseInput_memberId,$leaseInput_memberNo,$leaseInput_masterMemberId,$leaseInput_masterMemberNo);
					$check9 =  $this->defaultCheck("unitaddressrp","unitrp",$masterleasetemplateid,$propertyRowId,$leaseInput_memberId,$leaseInput_memberNo,$leaseInput_masterMemberId,$leaseInput_masterMemberNo);
					$check10 = $this->defaultCheck("utilityinforp","utilityrp",$masterleasetemplateid,$propertyRowId,$leaseInput_memberId,$leaseInput_memberNo,$leaseInput_masterMemberId,$leaseInput_masterMemberNo);
					$check11 = $this->defaultCheck("vehicleparkrp","vehrp",$masterleasetemplateid,$propertyRowId,$leaseInput_memberId,$leaseInput_memberNo,$leaseInput_masterMemberId,$leaseInput_masterMemberNo);
					$check12 = $this->defaultCheck("storageinforp","storerp",$masterleasetemplateid,$propertyRowId,$leaseInput_memberId,$leaseInput_memberNo,$leaseInput_masterMemberId,$leaseInput_masterMemberNo);
					$check12 = $this->defaultCheck("econtactinforp","econtactrp",$masterleasetemplateid,$propertyRowId,$leaseInput_memberId,$leaseInput_memberNo,$leaseInput_masterMemberId,$leaseInput_masterMemberNo);
					
					
					
						
					if($check1 && $check2 && $check3 && $check4 && $check5 && $check6 && $check7 && $check8 && $check9 && $check10 && $check11)
					{
						//$default_status =1;
						
						/* Insert Data into masterleaseid Table */
						$values ="('{$leaseInput_masterMemberId}','{$leaseInput_masterMemberNo}','{$leaseInput_memberId}','{$leaseInput_memberNo}','{$leaseInput_type}',{$pkgassignid},{$pkgassignmasterid},'{$leaseInput_package_name}',{$propertyRowId},'{$propertyRowCode}','{$leaseInput_property_name}',{$leaseInput_inquiry_no},{$leaseInput_co_qualifier_id},{$masterleaseassigntemplaterpid},{$masterleasetemplateid},'{$masterleasetemplatename}','{$user_name}')";
						$qry  = "INSERT INTO masterleaseid (masterleasemastermemberid,masterleasemastermemberno,masterleasememberid,masterleasememberno,masterleasetype,masterleasepackageassignid,masterleasepackageid,masterleasepackagename,masterleasepropertyid,masterleasepropertycode,masterleasepropertyname,masterleaseinquiryno,masterleasecoqualifiedno,masterleaseassigntemplaterpid,masterleasetemplateid,masterleasetemplatename,masterleasecreatedby) VALUES {$values}"; 
						$result = mysqli_query($this->connection, $qry); 
						$lastInsertedRecordId = mysqli_insert_id ($this->connection); 
						$_SESSION['masterleaseid'] = $lastInsertedRecordId;
						$_SESSION['leaseMemberId'] = $leaseInput_memberId;
						$_SESSION['leaseMemberNo'] = $leaseInput_memberNo;
						
						/* Step 3 */
						if($result)
						{	
						
							/*  Logic Flow 
								Step 1: Check if any document is selected i.e '$leaseInput_documentlist' array is not empty, 
									  : If atleast one document is selected, Go to step 2
									  :	If  No document is slected, go to Step 
									  
								Step 2: Check if document names array($leaseInput_document_name) and Document Details Array($leaseInput_document_details)  have equal number of items	
										If Yes Go to step 3
										If no Go to step 
								Step 3: build the Insert Query
									  : GO to step 4
								Step 4: Delete all the Previous Records for the current reuest from LeaseDocuments table
									  : Insert the New records 	
										
							*/
							/* Step 1 */
							if(!empty($leaseInput_documentlist))
							{	
								$transaction_status = 0;
								/* Step 2 */
								$lcount_doc_name = count($leaseInput_document_name);
								$count_doc_details = count($leaseInput_document_details);
								$insert='';
								if($lcount_doc_name == $count_doc_details)
								{	
									/* Step 3 */
									for($i = 0; $i<$count_doc_details;$i++)
									{
										
										$leasedocmasterdoc = explode('_', $leaseInput_document_details[$i]);
										$leasedocpackagedetailsid = $leasedocmasterdoc[0];
										$leasedocmasterdocid =$leasedocmasterdoc[2];
										$leasedocdocname = $leaseInput_document_name[$i];
										if(isset($leaseInput_documentlist[$i]) && ($leaseInput_documentlist[$i]=='on'))
										{
											$leasedocselect = 1;
											$insert = $insert." ('{$leaseInput_masterMemberId}','{$leaseInput_masterMemberNo}','{$leaseInput_memberId}','{$leaseInput_memberNo}',{$lastInsertedRecordId},{$leasedocpackagedetailsid},{$pkgassignid},{$pkgassignmasterid},'{$leaseInput_package_name}',{$leasedocmasterdocid},'{$leasedocdocname}',{$leasedocselect},'{$_SESSION['user']}'),";
										}
									}
								 
									/* Insert Record to table */
									$final_values = "VALUES ".rtrim($insert,",").";";
									$qry  = "INSERT INTO leasedocuments(leasedocmastermemberid,leasedocmastermemberno,leasedocmemberid,leasedocmemberno,leasedocmasterleaseid,leasedocpackagedetailsid,leasedocassignpackageid,leasedocpackageid,leasedocpackagename,leasedocmasterdocid,leasedocdocname,leasedocselect,leasedoccreatedby)".$final_values;
									$leasedocInsertResult = mysqli_query($this->connection, $qry); 
									if($leasedocInsertResult)
									{	 
										//mysqli_close($this->connection);
										//return true;
									}			
								}
								
								else
								{
									
								}
							}	
							 
							// get all the defaultrp Data 
							$getDefaultRPArray = $this->getDefaultRPdata();	
							
							//$table = "residentinforp"; 
							//$tablePre = "resident";
							//$coulmPrefix = "resrp";
							//$tagetTable = "leaseresidentinforp"; 
							$user = $_SESSION['user'];
							
							/* 
							$table....................Repository Table name(template repositories),
							$coulmPrefix..............Repository Table Column Prefix(template repositories),
							$tagetTable...............Lease Table Name(Lease Data Table),
							$tablePre.................Lease Table Prefix(Lease Data Table),
							$leaseInput_memberId......,
							$leaseInput_memberNo......,
							$leaseInput_masterMemberId,
							$leaseInput_masterMemberNo,
							$propertyRowId............,
							$masterleasetemplateid....Template ID,
							$lastInsertedRecordId.....Last Generated Lease Id,
							$user......................Current User*/
							
							/* $this->generateLeaseData($table,$tablePre,$coulmPrefix,$tagetTable,$leaseInput_memberId,$leaseInput_memberNo,$leaseInput_masterMemberId,$leaseInput_masterMemberNo,$propertyRowId,$masterleasetemplateid,$lastInsertedRecordId,$user); */
							$result1 = $this->generateLeaseData("applianceinforp","appliance","apprp","leaseapplianceinforp",$leaseInput_memberId,$leaseInput_memberNo,$leaseInput_masterMemberId,$leaseInput_masterMemberNo,$propertyRowId,$masterleasetemplateid,$lastInsertedRecordId,$user,$getDefaultRPArray);
							$result2 = $this->generateLeaseData("residentinforp","resident","resrp","leaseresidentinforp",$leaseInput_memberId,$leaseInput_memberNo,$leaseInput_masterMemberId,$leaseInput_masterMemberNo,$propertyRowId,$masterleasetemplateid,$lastInsertedRecordId,$user,$getDefaultRPArray);
							$result3 = $this->generateLeaseData("financialinforp","financial","finrp","leasefinancialinforp",$leaseInput_memberId,$leaseInput_memberNo,$leaseInput_masterMemberId,$leaseInput_masterMemberNo,$propertyRowId,$masterleasetemplateid,$lastInsertedRecordId,$user,$getDefaultRPArray);
							$result4 = $this->generateLeaseData("leasetermrp","leaseterm","lstrmrp","leaseleasetermrp",$leaseInput_memberId,$leaseInput_memberNo,$leaseInput_masterMemberId,$leaseInput_masterMemberNo,$propertyRowId,$masterleasetemplateid,$lastInsertedRecordId,$user,$getDefaultRPArray);
							$result5 = $this->generateLeaseData("limitinforp","limit","limitrp","leaselimitinforp",$leaseInput_memberId,$leaseInput_memberNo,$leaseInput_masterMemberId,$leaseInput_masterMemberNo,$propertyRowId,$masterleasetemplateid,$lastInsertedRecordId,$user,$getDefaultRPArray);
							$result6 = $this->generateLeaseData("miscinforp","misc","miscrp","leasemiscinforp",$leaseInput_memberId,$leaseInput_memberNo,$leaseInput_masterMemberId,$leaseInput_masterMemberNo,$propertyRowId,$masterleasetemplateid,$lastInsertedRecordId,$user,$getDefaultRPArray);
							$result7 = $this->generateLeaseData("petinforp","pet","petrp","leasepetinforp",$leaseInput_memberId,$leaseInput_memberNo,$leaseInput_masterMemberId,$leaseInput_masterMemberNo,$propertyRowId,$masterleasetemplateid,$lastInsertedRecordId,$user,$getDefaultRPArray);
							$result8 = $this->generateLeaseData("propertyinforp","property","proprp","leasepropertyinforp",$leaseInput_memberId,$leaseInput_memberNo,$leaseInput_masterMemberId,$leaseInput_masterMemberNo,$propertyRowId,$masterleasetemplateid,$lastInsertedRecordId,$user,$getDefaultRPArray);
							$result9 = $this->generateLeaseData("unitaddressrp","unitaddress","unitrp","leaseunitaddressrp",$leaseInput_memberId,$leaseInput_memberNo,$leaseInput_masterMemberId,$leaseInput_masterMemberNo,$propertyRowId,$masterleasetemplateid,$lastInsertedRecordId,$user,$getDefaultRPArray);
							$result10 = $this->generateLeaseData("utilityinforp","utility","utilityrp","leaseutilityinforp",$leaseInput_memberId,$leaseInput_memberNo,$leaseInput_masterMemberId,$leaseInput_masterMemberNo,$propertyRowId,$masterleasetemplateid,$lastInsertedRecordId,$user,$getDefaultRPArray);
							$result11 = $this->generateLeaseData("vehicleparkrp","vehiclepark","vehrp","leasevehicleparkrp",$leaseInput_memberId,$leaseInput_memberNo,$leaseInput_masterMemberId,$leaseInput_masterMemberNo,$propertyRowId,$masterleasetemplateid,$lastInsertedRecordId,$user,$getDefaultRPArray);
							$result12 = $this->generateLeaseData("storageinforp","store","storerp","leasestorageinforp",$leaseInput_memberId,$leaseInput_memberNo,$leaseInput_masterMemberId,$leaseInput_masterMemberNo,$propertyRowId,$masterleasetemplateid,$lastInsertedRecordId,$user,$getDefaultRPArray);
							$result12 = $this->generateLeaseData("econtactinforp","econtact","econtactrp","leaseecontactinforp",$leaseInput_memberId,$leaseInput_memberNo,$leaseInput_masterMemberId,$leaseInput_masterMemberNo,$propertyRowId,$masterleasetemplateid,$lastInsertedRecordId,$user,$getDefaultRPArray);
							$result13 = $this->generateCalculationData("calulationsetuprp","calrp","leasecalrp","leasecalulationsetuprp",$leaseInput_memberId,$leaseInput_memberNo,$leaseInput_masterMemberId,$leaseInput_masterMemberNo,$propertyRowId,$masterleasetemplateid,$lastInsertedRecordId,$user);
							
							/* error_log($result1); 
							error_log($result2); 
							error_log($result3); 
							error_log($result4); 
							error_log($result5); 
							error_log($result6); 
							error_log($result7); 
							error_log($result8); 
							error_log($result9); 
							error_log($result10); 
							error_log($result11);  */
							 
							
							/* if Copying Template fields failed, Delete all the inserted records */
							if(($result1 && $result2 && $result3 && $result4 && $result5 && $result6 && $result7 && $result8 && $result9 && $result10 && $result11)==false)
							{   
								$del1 = $this->deleteLeaseData("leaseapplianceinforp","appliance",$lastInsertedRecordId);
								$del2 = $this->deleteLeaseData("leaseresidentinforp","resident",$lastInsertedRecordId);
								$del3 = $this->deleteLeaseData("leasefinancialinforp","financial",$lastInsertedRecordId);
								$del4 = $this->deleteLeaseData("leaseleasetermrp","leaseterm",$lastInsertedRecordId);
								$del5 = $this->deleteLeaseData("leaselimitinforp","limit",$lastInsertedRecordId);
								$del6 = $this->deleteLeaseData("leasemiscinforp","misc",$lastInsertedRecordId);
								$del7 = $this->deleteLeaseData("leasepetinforp","pet",$lastInsertedRecordId);
								$del8 = $this->deleteLeaseData("leasepropertyinforp","property",$lastInsertedRecordId);
								$del9 = $this->deleteLeaseData("leaseunitaddressrp","unitaddress",$lastInsertedRecordId);
								$del10 = $this->deleteLeaseData("leaseutilityinforp","utility",$lastInsertedRecordId);
								$del11 = $this->deleteLeaseData("leasevehicleparkrp","vehiclepark",$lastInsertedRecordId);
								$del11 = $this->deleteLeaseData("leasedocuments","doc",$lastInsertedRecordId);
								$del11 = $this->deleteLeaseData("leasestorageinforp","store",$lastInsertedRecordId);
								$del11 = $this->deleteLeaseData("leaseecontactinforp","econtac",$lastInsertedRecordId);
								$del11 = $this->deleteLeaseData("masterleaseid","",$lastInsertedRecordId);
								//mysqli_close($this->connection); 
								return "noLeaseGenerated";	
							}
							else
							{	
								//mysqli_close($this->connection); 
								return "leaseIdGenerateSuccess";	
							}
						}
						else
						{
							error_log("unknnownError");
							//mysqli_close($this->connection); 
							return "leaseUnknownError";
							
						}	
					}
					else
					{
						mysqli_close($this->connection); 
					    return "DefaultNotSet";	
					}	
					
				}
			}
	}
	
	/* function to check Defaults are set or Not */
	function defaultCheck($table,$coulmPrefix,$masterleasetemplateid,$propertyRowId,$leaseInput_memberId,$leaseInput_memberNo,$leaseInput_masterMemberId,$leaseInput_masterMemberNo)
	{
		 
		$valueNotSet = 1;
		if(!$this->DBLogin())
		{	
			$this->HandleError("Database login failed!");
			return false;
		}
		$qry = "Select * from {$table} where {$coulmPrefix}tempid ={$masterleasetemplateid} AND {$coulmPrefix}select=1 AND {$coulmPrefix}defaultreq=1";
		$required = mysqli_query($this->connection, $qry);  
		/* if no default selected*/
 
		if(mysqli_num_rows($required)==0)
		{	 
			return true;
		}
		else
		{
			$ar= [];
			while($row = mysqli_fetch_assoc($required))
			{
				array_push($ar, $row);  
			}
			 
			foreach($ar as $row)
			{
				
				$search = "select * from defaultdata where defaultfeedid={$row[$coulmPrefix.'feedid']} AND defaultmemberid='{$leaseInput_memberId}' AND defaultmemberno='{$leaseInput_memberNo}' AND defaultmembermasterid='{$leaseInput_masterMemberId}' AND defaultmembermasterno='{$leaseInput_masterMemberNo}' AND defaultpropertyid={$propertyRowId} AND defaulttempid={$masterleasetemplateid} AND defaultvalue!='' AND defaultvalue IS NOT NULL";
				$default = mysqli_query($this->connection, $search);   
				$count = mysqli_num_rows($default);
				
				if($count==0 || !$default)
				{
					 
					$valueNotSet =0;
					
				}
				else
				{	
					 
				}		
			}
			
			if($valueNotSet==0)
			{
				return 0; 
			}
			else
			{
				return 1;
			}
		}	
	}
	
	/* Lease Input Feilds Copy after Successfull grenration of Lease ID */
		
	/*  Function Argument Description
		$table....................Repository Table name(template repositories),
		$coulmPrefix..............Repository Table Column Prefix(template repositories),
		$tagetTable...............Lease Table Name(Lease Data Table),
		$tablePre.................Lease Table Prefix(Lease Data Table),
		$leaseInput_memberId......,
		$leaseInput_memberNo......,
		$leaseInput_masterMemberId,
		$leaseInput_masterMemberNo,
		$propertyRowId............,
		$masterleasetemplateid....Template ID,
		$lastInsertedRecordId.....Last Generated Lease Id,
		$user......................Current User
	*/
	function generateLeaseData(
				$table,
				$tablePre,
				$coulmPrefix,
				$tagetTable,
				$leaseInput_memberId,
				$leaseInput_memberNo,
				$leaseInput_masterMemberId,
				$leaseInput_masterMemberNo,
				$propertyRowId,
				$masterleasetemplateid,
				$lastInsertedRecordId,
				$user,
				$getDefaultRPArray
	)
	{
		 
		if(!$this->DBLogin())
		{	
			$this->HandleError("Database login failed!");
			return false;
		}
		$selectQry = "select * from {$table} LEFT JOIN defaultdata ON defaultfeedid = {$table}.{$coulmPrefix}feedid AND defaultdata.defaultmemberid='{$leaseInput_memberId}' AND defaultdata.defaultmemberno='{$leaseInput_memberNo}' AND defaultdata.defaultmembermasterid='{$leaseInput_masterMemberId}' AND defaultdata.defaultmembermasterno='{$leaseInput_masterMemberNo}'  AND defaultdata.defaultpropertyid={$propertyRowId} AND defaultdata.defaulttempid={$masterleasetemplateid}  where 	{$coulmPrefix}tempid ={$masterleasetemplateid} AND {$coulmPrefix}select=1";
		error_log($selectQry); 
		$selectQryResult = mysqli_query($this->connection, $selectQry);
		if(!empty($selectQryResult))
		{	
			if(mysqli_num_rows($selectQryResult)==0)
			{
				 
				mysqli_close($this->connection); 
				return true;
				
			}
			else
			{	
				 
				 
				$values ="";
				while($row = mysqli_fetch_assoc($selectQryResult))
				{	
					/* get XMLTag Names for each field */
					$currentFieldID = $row[$coulmPrefix.'fieldid'];
					$xmlmastertag = '';
					$xmlblocktag = '';
					$xmlchildtag = '';
					
					if(!empty($getDefaultRPArray[$currentFieldID]['xmlmastertag']))
					{
						$xmlmastertag = $getDefaultRPArray[$currentFieldID]['xmlmastertag'];
					}	
					if(!empty($getDefaultRPArray[$currentFieldID]['xmlblocktag']))
					{
						$xmlblocktag =  $getDefaultRPArray[$currentFieldID]['xmlblocktag'];
					}	
					if(!empty($getDefaultRPArray[$currentFieldID]['xmlchildtag']))
					{
						$xmlchildtag =  $getDefaultRPArray[$currentFieldID]['xmlchildtag'];
					}
				 
					//error_log(print_r($getDefaultRPArray[$currentFieldID]['xmlmastertag'],true));	
					//error_log(print_r($getDefaultRPArray[$currentFieldID]['xmlblocktag'],true));	
					//error_log(print_r($getDefaultRPArray[$currentFieldID]['xmlchildtag'],true));	
					 
					/* -------XMLTAGNAMES ENDS---------- */
					
					if($table!='econtactinforp')
					{	
						$values .="('{$leaseInput_masterMemberId}','{$leaseInput_masterMemberNo}','{$leaseInput_memberId}','{$leaseInput_memberNo}',{$lastInsertedRecordId},'GROUP_1',{$row[$coulmPrefix.'feedid']},'{$row[$coulmPrefix.'subcategory']}','{$row[$coulmPrefix.'fieldid']}', '{$row[$coulmPrefix.'dropdown']}','{$row[$coulmPrefix.'fieldtype']}','{$row[$coulmPrefix.'fieldname']}','{$row[$coulmPrefix.'optlabel']}','',{$row[$coulmPrefix.'defaultreq']},'{$row['defaultvalue']}',{$row[$coulmPrefix.'req']},{$row[$coulmPrefix.'hide']},{$row[$coulmPrefix.'edit']},'{$user}',{$row[$coulmPrefix.'order']},'{$xmlmastertag}','{$xmlblocktag}','{$xmlchildtag}'),";
					}
					else if($table=='econtactinforp')
					{	
						$values .="('{$leaseInput_masterMemberId}','{$leaseInput_masterMemberNo}','{$leaseInput_memberId}','{$leaseInput_memberNo}',{$lastInsertedRecordId},'GROUP_1',{$row[$coulmPrefix.'feedid']},'{$row[$coulmPrefix.'subcategory']}','{$row[$coulmPrefix.'fieldid']}', '{$row[$coulmPrefix.'dropdown']}','{$row[$coulmPrefix.'fieldtype']}','{$row[$coulmPrefix.'fieldname']}','{$row[$coulmPrefix.'optlabel']}','',{$row[$coulmPrefix.'defaultreq']},'{$row['defaultvalue']}',{$row[$coulmPrefix.'req']},{$row[$coulmPrefix.'hide']},{$row[$coulmPrefix.'edit']},'{$user}',{$row[$coulmPrefix.'order']},'GROUP_1','{$xmlmastertag}','{$xmlblocktag}','{$xmlchildtag}'),";
					}
				}
				$final_values = "VALUES ".rtrim($values,",").";";
				if($table!='econtactinforp')
				{	
					$insert = "INSERT INTO {$tagetTable}(lease{$tablePre}mastermemberid,lease{$tablePre}mastermemberno,lease{$tablePre}memberid,lease{$tablePre}memberno,lease{$tablePre}masterleaseid,lease{$tablePre}grouplabel,lease{$tablePre}feedid,lease{$tablePre}subcategory,lease{$tablePre}fieldid,lease{$tablePre}dropdown,lease{$tablePre}fieldtype,lease{$tablePre}fieldname,lease{$tablePre}optlabel,lease{$tablePre}value,lease{$tablePre}defaultreq,lease{$tablePre}defaultvalue,lease{$tablePre}req,lease{$tablePre}hide,lease{$tablePre}edit,lease{$tablePre}createdby,lease{$tablePre}order, lease{$tablePre}xmlmastertag, lease{$tablePre}xmlblocktag, lease{$tablePre}xmlchildtag) {$final_values}";
				}
				else
				{
					$insert = "INSERT INTO {$tagetTable}(lease{$tablePre}mastermemberid,lease{$tablePre}mastermemberno,lease{$tablePre}memberid,lease{$tablePre}memberno,lease{$tablePre}masterleaseid,lease{$tablePre}grouplabel,lease{$tablePre}feedid,lease{$tablePre}subcategory,lease{$tablePre}fieldid,lease{$tablePre}dropdown,lease{$tablePre}fieldtype,lease{$tablePre}fieldname,lease{$tablePre}optlabel,lease{$tablePre}value,lease{$tablePre}defaultreq,lease{$tablePre}defaultvalue,lease{$tablePre}req,lease{$tablePre}hide,lease{$tablePre}edit,lease{$tablePre}createdby,lease{$tablePre}order, lease{$tablePre}mastergrouplabel, lease{$tablePre}xmlmastertag, lease{$tablePre}xmlblocktag, lease{$tablePre}xmlchildtag) {$final_values}";
				}
				error_log($insert); 
				$insertResult = mysqli_query($this->connection,$insert);
				if($insertResult)
				{
					mysqli_close($this->connection);
					//unset($this->connection);				
					return true;
				}
				else
				{
					mysqli_close($this->connection);
					//unset($this->connection);				
					return false;
				}
			}
		}
		else
		{
			mysqli_close($this->connection);
			//unset($this->connection);				
			return true;
		}	
		
	}
	
	/* Function to  Generate Calculation Data . */
	function generateCalculationData(
			$table,
			$tablePre,
			$coulmPrefix,
			$tagetTable,
			$leaseInput_memberId,
			$leaseInput_memberNo,
			$leaseInput_masterMemberId,
			$leaseInput_masterMemberNo,
			$propertyRowId,
			$masterleasetemplateid,
			$lastInsertedRecordId,
			$user
	)
	{
		if(!$this->DBLogin())
		{	
			$this->HandleError("Database login failed!");
			return false;
		}
		 
		$selectQry = "select * from {$table}   where  {$tablePre}mastermemberid='{$leaseInput_masterMemberId}' AND {$tablePre}mastermemberno='{$leaseInput_masterMemberNo}' AND {$tablePre}tempid ={$masterleasetemplateid}";
		 
		$selectQryResult = mysqli_query($this->connection, $selectQry);
		error_log(print_r($selectQryResult,true));
		if(!empty($selectQryResult))
		{	
			 
			if(mysqli_num_rows($selectQryResult)==0)
			{
				 
				mysqli_close($this->connection); 
				return true;
				
			}
			else
			{	
				 
				 
				$values ="";
				while($row = mysqli_fetch_assoc($selectQryResult))
				{	
					$values .="({$lastInsertedRecordId},'{$leaseInput_masterMemberId}','{$leaseInput_masterMemberNo}','{$leaseInput_memberId}','{$leaseInput_memberNo}',{$row[$tablePre.'tempid']},'{$row[$tablePre.'grouplabel']}','{$row[$tablePre.'category']}', '{$row[$tablePre.'subcategory']}',{$row[$tablePre.'defaultrpfeedid']},'{$row[$tablePre.'fieldid']}','{$row[$tablePre.'fieldname']}','{$row[$tablePre.'fieldvalue']}','{$user}'),";
				}
				$final_values = "VALUES ".rtrim($values,",").";";
				 	
				$insert = "INSERT INTO leasecalulationsetuprp(leaseid,leasecalrpmastermemberid,leasecalrpmastermemberno,leasecalrpmemberid,leasecalrpmemberno,
							leasecalrptempid,leasecalrpgrouplabel,leasecalrpcategory,leasecalrpsubcategory,leasecalrpdefaultrpfeedid,
							leasecalrpfieldid,leasecalrpfieldname,leasecalrpfieldvalue,leasecalrpmodifiedby) {$final_values}";
				 
				error_log($insert);
				$insertResult = mysqli_query($this->connection,$insert);
				if($insertResult)
				{
					mysqli_close($this->connection);
					//unset($this->connection);				
					return true;
				}
				else
				{
					mysqli_close($this->connection);
					//unset($this->connection);				
					return false;
				}
			}
		}
		else
		{
			mysqli_close($this->connection);
			//unset($this->connection);				
			return true;
		}	
	
	}		
	
	
	/* Function to  Delete the Lease Data from all tables. */
	function deleteLeaseData($leasetable,$tablePre,$leaseId)
	{	 
		if(!$this->DBLogin())
		{
		  $this->HandleError("Database login failed!");
		  return false;
		}
		if($leasetable=='masterleaseid'){
				$del = "DELETE FROM {$leasetable} WHERE {$tablePre}masterleaseid={$leaseId}";
		}
		else
		{	
		$del = "DELETE FROM {$leasetable} WHERE lease{$tablePre}masterleaseid={$leaseId}";
		}
		 
		$delResult = mysqli_query($this->connection,$del);
		mysqli_close($this->connection);
		 
		return true;
	}
	
	
	/*
	=======================================================
	    Function to Get DEFAULTRP table Data
		--  this function Retun the indexed 
			array with 'defrpfieldid' as index--
	=======================================================
	*/
	function getDefaultRPdata(){
		if(!$this->DBLogin())
		{
		  $this->HandleError("Database login failed!");
		  return false;
		}
		
		$qry = "SELECT * FROM `defaultrp`";
		$defaultrpSelect = mysqli_query($this->connection,$qry);
		$resultset = [];
		if(!$defaultrpSelect || mysqli_num_rows($defaultrpSelect) <= 0)
		{
			 mysqli_close($this->connection);
		     return false;
		}
		else
		{		
			foreach($defaultrpSelect as $row) 
			{
				$defrpfieldid = $row['defrpfieldid'];
				$resultset[$defrpfieldid]['xmlmastertag'] = $row['defrpxmlmastertag'];
				$resultset[$defrpfieldid]['xmlblocktag'] = $row['defrpxmlblocktag'];
				$resultset[$defrpfieldid]['xmlchildtag'] = $row['defrpxmlchildtag'];
				
			}
			
			mysqli_close($this->connection);
		    return $resultset;
		}	
		 
		 
		
	}
	
	/*
	=======================================================
	    Function to select data from table Based on Masterlease ID
    =======================================================
	*/	
	
	function showLeasefields($masterleaseid,$mastermemberid,$mastermemberno,$leasememberid,$leasememberno)
	{   if($masterleaseid==$_SESSION['masterleaseid'])
		{	if(!$this->DBLogin())
			{	
				$this->HandleError("Database login failed!");
				return false;
			}
			 
			$query = "
				SELECT 'APPLIANCES'  table_name, A1.*
					FROM leaseapplianceinforp as A1 WHERE leaseappliancemasterleaseid ={$masterleaseid} AND leaseappliancemastermemberid = '{$mastermemberid}' AND  leaseappliancemastermemberno = '{$mastermemberno}' AND 	leaseappliancememberid='{$leasememberid}' AND leaseappliancememberno='{$leasememberno}' AND leaseappliancestatus=1
			    UNION 
				SELECT 'FINANCIALS'  table_name, A2.*
					FROM leasefinancialinforp as A2 WHERE leasefinancialmasterleaseid ={$masterleaseid} AND leasefinancialmastermemberid = '{$mastermemberid}' AND  leasefinancialmastermemberno = '{$mastermemberno}' AND 		leasefinancialmemberid='{$leasememberid}' AND 	leasefinancialmemberno='{$leasememberno}' AND 	leasefinancialstatus=1
			    UNION
				SELECT 'LEASETERMS'  table_name, A3.*
					FROM leaseleasetermrp as A3 WHERE 	leaseleasetermmasterleaseid ={$masterleaseid} AND 	leaseleasetermmastermemberid = '{$mastermemberid}' AND  	leaseleasetermmastermemberno = '{$mastermemberno}' AND 	leaseleasetermmemberid='{$leasememberid}' AND 	leaseleasetermmemberno='{$leasememberno}' AND 	leaseleasetermstatus=1
			    UNION
				SELECT 'LIMITS'  table_name, A4.*
					FROM leaselimitinforp as A4 WHERE 		leaselimitmasterleaseid ={$masterleaseid} AND 	leaselimitmastermemberid = '{$mastermemberid}' AND  	leaselimitmastermemberno = '{$mastermemberno}' AND 	leaselimitmemberid='{$leasememberid}' AND leaselimitmemberno='{$leasememberno}'  AND leaselimitstatus=1
			    UNION
				SELECT 'MISC'  table_name, A5.*
					FROM leasemiscinforp as A5 WHERE 	leasemiscmasterleaseid ={$masterleaseid} AND leasemiscmastermemberid = '{$mastermemberid}' AND  	leasemiscmastermemberno = '{$mastermemberno}' AND 		leasemiscmemberid='{$leasememberid}' AND 	leasemiscmemberno='{$leasememberno}'  AND 	leasemiscstatus=1
			    UNION
				SELECT 'PETS'  table_name, A6.*
					FROM leasepetinforp as A6 WHERE 	leasepetmasterleaseid ={$masterleaseid} AND leasepetmastermemberid = '{$mastermemberid}' AND  	leasepetmastermemberno = '{$mastermemberno}' AND 	leasepetmemberid='{$leasememberid}' AND 	leasepetmemberno='{$leasememberno}'  AND 	leasepetstatus=1
			    UNION
				SELECT 'PROPERTY'  table_name, A7.*
					FROM leasepropertyinforp as A7 WHERE 	leasepropertymasterleaseid ={$masterleaseid} AND leasepropertymastermemberid = '{$mastermemberid}' AND  leasepropertymastermemberno = '{$mastermemberno}' AND 	leasepropertymemberid='{$leasememberid}' AND leasepropertymemberno='{$leasememberno}'  AND leasepropertystatus=1
			    UNION
				SELECT 'RESIDENT'  table_name, A8.*
					FROM leaseresidentinforp as A8 WHERE leaseresidentmasterleaseid ={$masterleaseid} AND 	leaseresidentmastermemberid = '{$mastermemberid}' AND  	leaseresidentmastermemberno = '{$mastermemberno}' AND 	leaseresidentmemberid='{$leasememberid}' AND 	leaseresidentmemberno='{$leasememberno}'  AND 	leaseresidentstatus=1
			    UNION
				SELECT 'UNITADDRES'  table_name, A9.*
					FROM leaseunitaddressrp as A9 WHERE leaseunitaddressmasterleaseid ={$masterleaseid} AND 	leaseunitaddressmastermemberid = '{$mastermemberid}' AND  	leaseunitaddressmastermemberno = '{$mastermemberno}' AND 	leaseunitaddressmemberid='{$leasememberid}' AND leaseunitaddressmemberno='{$leasememberno}'  AND leaseunitaddressstatus=1
			    UNION
				SELECT 'UTILITIES'  table_name, A10.*
					FROM leaseutilityinforp as A10 WHERE 	leaseutilitymasterleaseid ={$masterleaseid} AND 	leaseutilitymastermemberid = '{$mastermemberid}' AND  	leaseutilitymastermemberno = '{$mastermemberno}' AND 		leaseutilitymemberid='{$leasememberid}' AND 	leaseutilitymemberno='{$leasememberno}'  AND 	leaseutilitystatus=1
			    UNION
				SELECT 'VEHICLEPAR'  table_name, A11.*
					FROM leasevehicleparkrp as A11 WHERE 	leasevehicleparkmasterleaseid ={$masterleaseid} AND leasevehicleparkmastermemberid = '{$mastermemberid}' AND  	leasevehicleparkmastermemberno = '{$mastermemberno}' AND 		leasevehicleparkmemberid='{$leasememberid}' AND leasevehicleparkmemberno='{$leasememberno}'  AND leasevehicleparkstatus=1
				UNION
			    SELECT 'STORAGE'  table_name, A12.*
					FROM leasestorageinforp as A12 WHERE 	leasestoremasterleaseid ={$masterleaseid} AND leasestoremastermemberid = '{$mastermemberid}' AND  	leasestoremastermemberno = '{$mastermemberno}' AND 		leasestorememberid='{$leasememberid}' AND leasestorememberno='{$leasememberno}'  AND leasestorestatus=1
			   
				ORDER BY 	leaseappliancegrouplabel,leaseapplianceorder,leaseappliancefeedid ASC"; 
			 
			 
			$result = mysqli_query($this->connection,$query);
			 
			if(!$result || mysqli_num_rows($result) <= 0)
				{
				  error_log("Error in feteching the records from the repositories or no Matched Records found");	
				  mysqli_close($this->connection);
				  return false;
				}
			else
			{
				$ar2= [];
				while($row = mysqli_fetch_assoc($result))
				{
					array_push($ar2, $row);  
				}
				mysqli_close($this->connection);
				$resultset = [];
				 
				foreach($ar2 as $temp_row) 
				{	
					if($temp_row['table_name'] == "APPLIANCES")
					{
					   $resultset['APPLIANCES'][] =  $temp_row; 
					}
					 elseif($temp_row['table_name'] == "FINANCIALS")
					{
						$resultset['FINANCIALS'][] =$temp_row;
						 
					}
					 
					elseif($temp_row['table_name'] == "LEASETERMS")
					{
						$resultset['LEASETERMS'][] =$temp_row;
					}
					elseif($temp_row['table_name'] == "LIMITS")
					{
						$resultset['LIMITS'][] =$temp_row;
					}
					elseif($temp_row['table_name'] == "MISC")
					{
						$resultset['MISC'][] =$temp_row;
					}
					elseif($temp_row['table_name'] == "PETS")
					{
						$resultset['PETS'][] =$temp_row;
					}
					elseif($temp_row['table_name'] == "PROPERTY")
					{
						$resultset['PROPERTY'][] =$temp_row;
					}
					elseif($temp_row['table_name'] == "RESIDENT")
					{
						$resultset['RESIDENT'][] = $temp_row;
					}
					elseif($temp_row['table_name'] == "UNITADDRES")
					{
						$resultset['UNITADDRES'][] = $temp_row;
					}
					elseif($temp_row['table_name'] == "UTILITIES")
					{
						$resultset['UTILITIES'][] = $temp_row;
					}
					elseif($temp_row['table_name'] == "VEHICLEPAR")
					{
						$resultset['VEHICLEPAR'][] = $temp_row;
					} 
					elseif($temp_row['table_name'] == "STORAGE")
					{
						$resultset['STORAGE'][] = $temp_row;
					} 
					elseif($temp_row['table_name'] == "ECONTACT")
					{
						$resultset['ECONTACT'][] = $temp_row;
					} 					
					else
					{
						
					}
				}
				return $resultset;
			}
		}	
		else
		{
			return false;
		}	
	}
	
	
	/*
	=======================================================
	    Function to select data from table Based on Masterlease ID
    =======================================================
	*/	
	
	function showLeaseCalfields($masterleaseid,$mastermemberid,$mastermemberno,$leasememberid,$leasememberno)
	{
		if(!$this->DBLogin())
		{
		  $this->HandleError("Database login failed!");
		  return false;
		}
		
		$qry = "select * from leasecalulationsetuprp where 	leaseid = {$masterleaseid} AND 	leasecalrpmastermemberid='{$mastermemberid}' AND leasecalrpmastermemberno = {$mastermemberno} AND  	leasecalrpmemberid='{$leasememberid}' AND 	leasecalrpmemberno={$leasememberno}";
		 
		$result = mysqli_query($this->connection,$qry);
		if(!$result || mysqli_num_rows($result) <= 0)
		{
		  error_log("Error in feteching the records from the repositories or no Matched Records found");	
		  mysqli_close($this->connection);
		  return false;
		}
		else
		{
			$ar2= array();
			while($row = mysqli_fetch_assoc($result))
			{
				$leasecalrpgrouplabel  = $row['leasecalrpgrouplabel'];
				$ar2['setup'][$leasecalrpgrouplabel][] =  $row;
			}
		 
			mysqli_close($this->connection);
			return $ar2;
		}	
		
	
	}
	
	/*
	=======================================================
	    Function to Select Emergency Contact Details
    =======================================================
	*/	
	
	function showEmergencyContact($masterleaseid,$mastermemberid,$mastermemberno,$leasememberid,$leasememberno)
	{
		if(!$this->DBLogin())
		{
		  $this->HandleError("Database login failed!");
		  return false;
		}
		
		$qry = "SELECT * FROM leaseecontactinforp  WHERE 	leaseecontactmasterleaseid ={$masterleaseid} AND leaseecontactmastermemberid = '{$mastermemberid}' AND  	leaseecontactmastermemberno = '{$mastermemberno}' AND 		leaseecontactmemberid='{$leasememberid}' AND leaseecontactmemberno='{$leasememberno}'  AND leaseecontactstatus=1";
		 
		$result = mysqli_query($this->connection,$qry);
		if(!$result || mysqli_num_rows($result) <= 0)
		{
		  error_log("Error in feteching the records from the repositories or no Matched Records found");	
		  mysqli_close($this->connection);
		  return false;
		}
		else
		{
			$ar2= [];
			while($row = mysqli_fetch_assoc($result))
			{
				array_push($ar2, $row);  
			}
			mysqli_close($this->connection);
			return $ar2;
		}	
	}
	
	/*
	=======================================================
	    Function to Select Emergency Contact Details Based On Master GroupLable
    =======================================================
	*/	
	
	function showAddedEmergencyContact($mastermemberid,$mastermemberno, $estatus, $etable,$uniqueLabel)
	{
		if(!$this->DBLogin())
		{
		  $this->HandleError("Database login failed!");
		  return false;
		}
		$masterleaseid=$_SESSION['masterleaseid'];
		$leaseMemberId=$_SESSION['leaseMemberId'];
		$leaseMemberNo=$_SESSION['leaseMemberNo'];
		$eqry = "SELECT * FROM leaseecontactinforp  WHERE 	leaseecontactmasterleaseid ={$masterleaseid} AND leaseecontactmastermemberid = '{$mastermemberid}' AND  	leaseecontactmastermemberno = '{$mastermemberno}' AND 		leaseecontactmemberid='{$_SESSION['leaseMemberId']}' AND leaseecontactmemberno='{$_SESSION['leaseMemberNo']}'  AND leaseecontactstatus=1 AND leaseecontactmastergrouplabel='{$uniqueLabel}'";
		 
		$eresult = mysqli_query($this->connection,$eqry);
		 
		if(!$eresult || mysqli_num_rows($eresult) <= 0)
		{
		  error_log("Error in feteching the records from the repositories or no Matched Records found");	
		  mysqli_close($this->connection);
		  return false;
		}
		else
		{
			$ar2= [];
			while($row = mysqli_fetch_assoc($eresult))
			{
				array_push($ar2, $row);  
			}
			mysqli_close($this->connection);
			return $ar2;
		}	
		
	}
	
	/*
	=======================================================
	    Function to Update Lease Data To Lease Tables
    =======================================================
	*/	
	function updateLeaseRecordTable($mastermemberid,$mastermemberno,$table,$inputIds,$inputValues,$inputAllowed,$grouplabel, $contactInputValues,$contactInputIds,$contactGrouplabel,$accumulatedFiled)
	{
		$masterleaseid = $_SESSION['masterleaseid'];
		$leaseMemberId=$_SESSION['leaseMemberId'];
		$leaseMemberNo=$_SESSION['leaseMemberNo'];
		
		if(!$this->DBLogin())
		{
		  $this->HandleError("Database login failed!");
		  return false;
		}
		$tableName= '';
		$tableColumnPrefix = '';
		if($table=='APPLIANCES')
		{
			$tableName = 'leaseapplianceinforp';
			$tableColumnPrefix = 'leaseappliance';
			$leasestatus=1;
		}
		else if($table=='FINANCIALS')
		{
			$tableName = 'leasefinancialinforp';
			$tableColumnPrefix = 'leasefinancial';
			$leasestatus=1;
			
			if($accumulatedFiled!='')
			{	
				$this->updateFinAccumulatedFields($masterleaseid,$mastermemberid,$mastermemberno,$leaseMemberId,$leaseMemberNo,$tableName,$tableColumnPrefix,$inputIds,$inputValues,$leasestatus,$grouplabel,$accumulatedFiled);
				mysqli_close($this->connection);
				return $result;
			}	
		}
		else if($table=='LEASETERMS')
		{
			$tableName = 'leaseleasetermrp';
			$tableColumnPrefix = 'leaseleaseterm';
			$leasestatus=1;
		}
		else if($table=='LIMITS')
		{
			$tableName = 'leaselimitinforp';
			$tableColumnPrefix = 'leaselimit';
			$leasestatus=1;
		}
		else if($table=='MISC')
		{
			$tableName = 'leasemiscinforp';
			$tableColumnPrefix = 'leasemisc';
			$leasestatus=1;
		}
		else if($table=='PETS')
		{
			$tableName = 'leasepetinforp';
			$tableColumnPrefix = 'leasepet';
			$leasestatus=1;
			if(isset($inputAllowed))
			{
				if($inputAllowed=='no')
				{  
					$columnValue=0;	
				}
				else if($inputAllowed=='yes')
				{
					$columnValue=1;	
				}
				$columnName = 'masterleasepetscount';
				$result = $this->updateMasterLeaseRecord($tableName,$tableColumnPrefix,$columnValue,$columnName,$mastermemberid,$mastermemberno,$masterleaseid,$leaseMemberId,$leaseMemberNo,$inputIds,$inputValues,$grouplabel);
				
				mysqli_close($this->connection);
				return $result;				
			
			}
		}
		
		
		else if($table=='PROPERTY')
		{
			$tableName = 'leasepropertyinforp';
			$tableColumnPrefix = 'leaseproperty';
			$leasestatus=1;
		}
		else if($table=='RESIDENT')
		{
			$tableName = 'leaseresidentinforp';
			$tableColumnPrefix = 'leaseresident';
			$leasestatus=1;
			$result = $this->updateContactLeaseRecord($masterleaseid,$mastermemberid,$mastermemberno,$leaseMemberId,$leaseMemberNo,$tableName,$tableColumnPrefix,$inputIds,$inputValues,$leasestatus,$grouplabel, $contactInputValues,$contactInputIds,$contactGrouplabel);
			 
			mysqli_close($this->connection);
			return true ;
		}
		else if($table=='UNITADDRES')
		{
			$tableName = 'leaseunitaddressrp';
			$tableColumnPrefix = 'leaseunitaddress';
			$leasestatus=1;
		}
		else if($table=='UTILITIES')
		{
			$tableName = 'leaseutilityinforp';
			$tableColumnPrefix = 'leaseutility';
			$leasestatus=1;
		}
		else if($table=='VEHICLEPAR')
		{
			$tableName = 'leasevehicleparkrp';
			$tableColumnPrefix = 'leasevehiclepark';
			$leasestatus=1;
			if(isset($inputAllowed))
			{
				if($inputAllowed=='no')
				{  
					$columnValue=0;	
				}
				else if($inputAllowed=='yes')
				{
					$columnValue=1;	
				}
				$columnName = 'masterleasevehiclepark';
				$result = $this->updateMasterLeaseRecord($tableName,$tableColumnPrefix,$columnValue,$columnName,$mastermemberid,$mastermemberno,$masterleaseid,$leaseMemberId,$leaseMemberNo,$inputIds,$inputValues,$grouplabel);
				mysqli_close($this->connection);
				return $result;	
			
			}
		}
		else if($table=='STORAGE')
		{
			$tableName = 'leasestorageinforp';
			$tableColumnPrefix = 'leasestore';
			$leasestatus=1;
		}
		
		else
		{
			mysqli_close($this->connection);
			return false ;
		}
		$this->leaseRecordUpdate($masterleaseid,$mastermemberid,$mastermemberno,$leaseMemberId,$leaseMemberNo,$tableName,$tableColumnPrefix,$inputIds,$inputValues,$leasestatus,$grouplabel);
		mysqli_close($this->connection);
		return false ;
	}
	
	/*
	=======================================================
	    Function Update the Exisitng Lease Records 
    =======================================================
	*/	 
	function leaseRecordUpdate($masterleaseid,$mastermemberid,$mastermemberno,$leaseMemberId,$leaseMemberNo,$tableName,$tableColumnPrefix,$inputIds,$inputValues,$leasestatus,$grouplabel)
	{	
		
			
		    			
				for ($j = 0; $j < count($inputIds); $j++)
				{
					$query = "UPDATE {$tableName} SET {$tableColumnPrefix}value='{$inputValues[$j]}',{$tableColumnPrefix}status='{$leasestatus}' WHERE {$tableColumnPrefix}id={$inputIds[$j]}";
					
					mysqli_query($this->connection, $query);
				}
				return true;	
	}
	
	/*
	=======================================================
	    Function Update the Lease FINANCIALS Records
    =======================================================
	*/	 
	function updateFinAccumulatedFields($masterleaseid,$mastermemberid,$mastermemberno,$leaseMemberId,$leaseMemberNo,$tableName,$tableColumnPrefix,$inputIds,$inputValues,$leasestatus,$grouplabel,$accumulatedFiled)
	{	
		
			
		    			
				for ($j = 0; $j < count($inputIds); $j++)
				{
					$query = "UPDATE {$tableName} SET {$tableColumnPrefix}value='{$inputValues[$j]}',{$tableColumnPrefix}status='{$leasestatus}' WHERE {$tableColumnPrefix}id={$inputIds[$j]}";
					
					mysqli_query($this->connection, $query);
				}
				
				foreach($accumulatedFiled as  $accKey => $accValue)
				{
					$query = "UPDATE {$tableName} SET {$tableColumnPrefix}value='{$accValue}',{$tableColumnPrefix}status='{$leasestatus}' WHERE {$tableColumnPrefix}fieldid='{$accKey}' AND {$tableColumnPrefix}masterleaseid = {$masterleaseid}";
					
					error_log("vvvvvvvvv");
					error_log($query);
					mysqli_query($this->connection, $query);
				}	
				return true;	
	}
	
	
	/*
	=======================================================
	    Function Update the Exisitng Emergency Contact Lease Records 
    =======================================================
	*/	 
	
	function updateContactLeaseRecord($masterleaseid,$mastermemberid,$mastermemberno,$leaseMemberId,$leaseMemberNo,$tableName,$tableColumnPrefix,$inputIds,$inputValues,$leasestatus,$grouplabel,$contactInputValues,$contactInputIds,$contactGrouplabel)
	{	
		
			
		    			
				for ($j = 0; $j < count($inputIds); $j++)
				{
					$query = "UPDATE {$tableName} SET {$tableColumnPrefix}value='{$inputValues[$j]}',{$tableColumnPrefix}status='{$leasestatus}' WHERE {$tableColumnPrefix}id={$inputIds[$j]}";
					
					mysqli_query($this->connection, $query);
				}
				 
			
			if(!empty($contactInputIds))
			{	
				$tableName = 'leaseecontactinforp'; 
				$tableColumnPrefix = 'leaseecontact';
				$inputValues = $contactInputValues;
				$grouplabel = $contactGrouplabel;
				$inputIds = $contactInputIds;
				 
				
				 
				
				for($j =0; $j<count($contactInputIds);$j++)
				{
					$query = "UPDATE {$tableName} SET {$tableColumnPrefix}value='{$inputValues[$j]}',{$tableColumnPrefix}status='{$leasestatus}' WHERE {$tableColumnPrefix}id={$inputIds[$j]}";
					
					mysqli_query($this->connection, $query); 
					
				}	
				error_log(print_r($_POST,true));
			}	
		 return true;	
	}
	
	
	
	/*
	=======================================================
	    Function to Update MasterLease Table Records
    =======================================================
	*/	
	
	function updateMasterLeaseRecord($tableName,$tableColumnPrefix,$columnValue,$columnName,$mastermemberid,$mastermemberno,$masterleaseid,$leaseMemberId,$leaseMemberNo,$inputIds,$inputValues,$grouplabel)
	{	
		
		
		$qry = "UPDATE masterleaseid SET {$columnName}={$columnValue} WHERE 	masterleaseid={$masterleaseid} AND masterleasemastermemberid='{$mastermemberid}' AND 	masterleasemastermemberno='{$mastermemberno}' AND masterleasememberid='{$leaseMemberId}' AND 	masterleasememberno='{$leaseMemberNo}'";
		if(mysqli_query($this->connection, $qry))
		{	
			if($columnValue==0)
			{	 
				$leasestatus =1;
				$this->leaseRecordUpdate($masterleaseid,$mastermemberid,$mastermemberno,$leaseMemberId,$leaseMemberNo,$tableName,$tableColumnPrefix,$inputIds,$inputValues,$leasestatus,$grouplabel); 
			}
			else if($columnValue==1)
			{	 
				$leasestatus =1;
				$this->leaseRecordUpdate($masterleaseid,$mastermemberid,$mastermemberno,$leaseMemberId,$leaseMemberNo,$tableName,$tableColumnPrefix,$inputIds,$inputValues,$leasestatus,$grouplabel);
			}	
		}	
	}
	
	/*
	=======================================================
	    Function to ADD NEW PET, VECH AND RESIDENT
    =======================================================
	*/	
	
	function addNewLeaseRecord($mastermemberid,$mastermemberno,$table,$inputIds,$inputValues,$grouplabel,$feedId, $estatus, $etable, $einputValues, $einputIds, $egrouplabel, $efeedId)
	{
		if(!$this->DBLogin())
		{
		  $this->HandleError("Database login failed!");
		  return false;
		}
		
		$masterleaseid = $_SESSION['masterleaseid'];
		$leaseMemberId=$_SESSION['leaseMemberId'];
		$leaseMemberNo=$_SESSION['leaseMemberNo'];
		
		if($table=='PETS')
		{
			$tableName = 'leasepetinforp';
			$tableColumnPrefix = 'leasepet';
			$leasestatus=1;
		}
		else if($table=='RESIDENT')
		{
			$tableName = 'leaseresidentinforp';
			$tableColumnPrefix = 'leaseresident';
			$leasestatus=1;
		}
		else if($table=='VEHICLEPAR')
		{
			$tableName = 'leasevehicleparkrp';
			$tableColumnPrefix = 'leasevehiclepark';
			$leasestatus=1;
		}
		else if($table=='UTILITIES')
		{
			$tableName = 'leaseutilityinforp';
			$tableColumnPrefix = 'leaseutility';
			$leasestatus=1;
		}
		else if($table=='APPLIANCES')
		{
			$tableName = 'leaseapplianceinforp';
			$tableColumnPrefix = 'leaseappliance';
			$leasestatus=1;
		}
		else
		{
			mysqli_close($this->connection);
			return "NewRecordError";
		}	
		/* Select all the existing Grouplables in the LeaseResident table */ 	
		$qry = "SELECT DISTINCT({$tableColumnPrefix}grouplabel) FROM {$tableName} where {$tableColumnPrefix}mastermemberid='{$mastermemberid}' AND {$tableColumnPrefix}mastermemberno='{$mastermemberno}' AND {$tableColumnPrefix}memberid='{$leaseMemberId}' AND {$tableColumnPrefix}memberno='{$leaseMemberNo}' AND {$tableColumnPrefix}masterleaseid={$masterleaseid} ORDER BY {$tableColumnPrefix}grouplabel ASC";
		  
		
		$result = mysqli_query($this->connection, $qry);
		 
		if(!$result || mysqli_num_rows($result) <= 0)
		{
			mysqli_close($this->connection);
		    return "NewRecordError";
		}
		
		else
		{
			 
			$ar2= [];
			while($row = mysqli_fetch_assoc($result))
			{
				array_push($ar2, $row);  
			}
			$selectlabel = $ar2[0][$tableColumnPrefix.'grouplabel'];
			error_log(print_r($selectlabel,true)); 
			$lastGroupText = array_values(array_slice($ar2, -1))[0];
			error_log(print_r($lastGroupText,true)); 
			$lastGroupText = $lastGroupText[$tableColumnPrefix.'grouplabel'];
			error_log(print_r($lastGroupText,true)); 
			$lastGroupText = explode('_', $lastGroupText);
			error_log(print_r($lastGroupText,true)); 
			$lastGroupNum = $lastGroupText[1];
			$lastGroupNum++;
			error_log(print_r($lastGroupNum,true)); 
			
			/* e** is used to denote the emergency contact details */
			$etableColumnPrefix = "leaseecontact";
			
			/* Select All the fields from leaseecontact based on the groupLabel */
			$select = "SELECT * FROM {$tableName} where {$tableColumnPrefix}mastermemberid='{$mastermemberid}' AND {$tableColumnPrefix}mastermemberno='{$mastermemberno}' AND {$tableColumnPrefix}memberid='{$leaseMemberId}' AND {$tableColumnPrefix}memberno='{$leaseMemberNo}' AND {$tableColumnPrefix}masterleaseid={$masterleaseid} AND {$tableColumnPrefix}grouplabel='{$selectlabel}'";
			$eselectlabel = "SELECT DISTINCT({$etableColumnPrefix}grouplabel) FROM {$etable} where {$etableColumnPrefix}mastermemberid='{$mastermemberid}' AND {$etableColumnPrefix}mastermemberno='{$mastermemberno}' AND {$etableColumnPrefix}memberid='{$leaseMemberId}' AND {$etableColumnPrefix}memberno='{$leaseMemberNo}' AND {$etableColumnPrefix}masterleaseid={$masterleaseid} AND {$etableColumnPrefix}mastergrouplabel='{$selectlabel}'";
			 
			 
			$selectResult = mysqli_query($this->connection, $select);
			if($estatus ==1)
			{	$eselectGroup = mysqli_query($this->connection, $eselectlabel);
				$egroupLabel= [];
				while($row = mysqli_fetch_assoc($eselectGroup))
				{
					array_push($egroupLabel, $row);  
				}
				$selectedlabel = $egroupLabel[0][$etableColumnPrefix.'grouplabel'];
				/* Query to get the List of all the records to be dupliacted from leaseEcontact table */
				$eselect = "SELECT * FROM {$etable} where {$etableColumnPrefix}mastermemberid='{$mastermemberid}' AND {$etableColumnPrefix}mastermemberno='{$mastermemberno}' AND {$etableColumnPrefix}memberid='{$leaseMemberId}' AND {$etableColumnPrefix}memberno='{$leaseMemberNo}' AND {$etableColumnPrefix}masterleaseid={$masterleaseid} AND {$etableColumnPrefix}grouplabel='{$selectedlabel}'";
				 
				$eselectResult = mysqli_query($this->connection, $eselect);
			}
			
			if(!$selectResult || mysqli_num_rows($selectResult) <= 0)
			{	error_log("gggggggggggg6");
				mysqli_close($this->connection);
				return "NewRecordError";
			}
			else
			{	error_log("gggggggggggg9");
				$arr= [];
				while($row = mysqli_fetch_assoc($selectResult))
				{
					array_push($arr, $row);  
				}
				
				/*  ======================================
						for Emergency Contact 
				    ======================================
				 */
				if($estatus ==1)
				{	
					error_log("gggggggggggg1");
					$econtactqry = "SELECT DISTINCT({$etableColumnPrefix}grouplabel) FROM {$etable} where {$etableColumnPrefix}mastermemberid='{$mastermemberid}' AND {$etableColumnPrefix}mastermemberno='{$mastermemberno}' AND {$etableColumnPrefix}memberid='{$leaseMemberId}' AND {$etableColumnPrefix}memberno='{$leaseMemberNo}' AND {$etableColumnPrefix}masterleaseid={$masterleaseid} ORDER BY {$etableColumnPrefix}grouplabel ASC";
					
					$econtactresult = mysqli_query($this->connection, $econtactqry);
					$ar3= [];
					while($row = mysqli_fetch_assoc($econtactresult))
					{
						array_push($ar3, $row);  
					}
					$eselectlabel = $ar3[0][$etableColumnPrefix.'grouplabel'];
					 
					$elastGroupText = array_values(array_slice($ar3, -1))[0];
					 
					$elastGroupText = $elastGroupText[$etableColumnPrefix.'grouplabel'];
					 
					$elastGroupText = explode('_', $elastGroupText);
					 
					$elastGroupNum = $elastGroupText[1];
					$elastGroupNum++;
			
					 
					$earr= [];
					while($row = mysqli_fetch_assoc($eselectResult))
					{
						array_push($earr, $row);  
					}
					
					$evalues = '';
					for($j = 0; $j<count($earr);$j++)
					{	
						$enewValue='';
						for($k = 0; $k < count($einputIds);$k++)
						{
							 
							if($efeedId[$k]==(string)$earr[$j][$etableColumnPrefix.'feedid'])
							{
								$enewValue=$einputValues[$k];
								
								 
							}
						}	
						
						$evalues = "{$evalues}  ('".$earr[$j][$etableColumnPrefix.'mastermemberid']."',
												 '".$earr[$j][$etableColumnPrefix.'mastermemberno']."',
												 '".$earr[$j][$etableColumnPrefix.'memberid']."',
												 '".$earr[$j][$etableColumnPrefix.'memberno']."',
												 '".$earr[$j][$etableColumnPrefix.'masterleaseid']."',
												 'GROUP_".$elastGroupNum."',
												 '".$earr[$j][$etableColumnPrefix.'feedid']."',
												 '".$earr[$j][$etableColumnPrefix.'subcategory']."',
												 '".$earr[$j][$etableColumnPrefix.'fieldid']."',
												 '".$earr[$j][$etableColumnPrefix.'dropdown']."',
												 '".$earr[$j][$etableColumnPrefix.'fieldtype']."',
												 '".$earr[$j][$etableColumnPrefix.'fieldname']."',
												 '".$earr[$j][$etableColumnPrefix.'optlabel']."',
												 '".trim($enewValue)."',
												 '".$earr[$j][$etableColumnPrefix.'defaultreq']."',
												 '".$earr[$j][$etableColumnPrefix.'defaultvalue']."',
												 '".$earr[$j][$etableColumnPrefix.'req']."',
												 '".$earr[$j][$etableColumnPrefix.'hide']."',
												 '".$earr[$j][$etableColumnPrefix.'edit']."',
												 '".$earr[$j][$etableColumnPrefix.'status']."',
												 '".$earr[$j][$etableColumnPrefix.'reserve']."',
												 '".$earr[$j][$etableColumnPrefix.'createdby']."',
												 '".$earr[$j][$etableColumnPrefix.'modifiedby']."',
												 '".$earr[$j][$etableColumnPrefix.'order']."',
												 '".$earr[$j][$etableColumnPrefix.'xmlmastertag']."',
												 '".$earr[$j][$etableColumnPrefix.'xmlblocktag']."',
												 '".$earr[$j][$etableColumnPrefix.'xmlchildtag']."',
												 'GROUP_".$lastGroupNum."'),";
						
						
							
						
					}
					$evalues = rtrim($evalues,',');
					$einsert  = "INSERT INTO {$etable} ({$etableColumnPrefix}mastermemberid,
													{$etableColumnPrefix}mastermemberno,
													{$etableColumnPrefix}memberid,
													{$etableColumnPrefix}memberno,
													{$etableColumnPrefix}masterleaseid,
													{$etableColumnPrefix}grouplabel,		
                                                    {$etableColumnPrefix}feedid,
                                                    {$etableColumnPrefix}subcategory,
                                                    {$etableColumnPrefix}fieldid,
                                                    {$etableColumnPrefix}dropdown,
                                                    {$etableColumnPrefix}fieldtype,
                                                    {$etableColumnPrefix}fieldname,
													{$etableColumnPrefix}optlabel,	
                                                    {$etableColumnPrefix}value,
                                                    {$etableColumnPrefix}defaultreq,
                                                    {$etableColumnPrefix}defaultvalue,
													{$etableColumnPrefix}req,
													{$etableColumnPrefix}hide,
													{$etableColumnPrefix}edit,
													{$etableColumnPrefix}status,
													{$etableColumnPrefix}reserve,
													{$etableColumnPrefix}createdby,
													{$etableColumnPrefix}modifiedby,
													{$etableColumnPrefix}order,
													{$etableColumnPrefix}xmlmastertag,
													{$etableColumnPrefix}xmlblocktag,
													{$etableColumnPrefix}xmlchildtag,
													{$etableColumnPrefix}mastergrouplabel
													) 
													VALUES {$evalues}";
					
					 
					$eaddedResult = mysqli_query($this->connection, $einsert);
				}
				
				/* for other than Emergency Contact */
				//error_log(print_r($arr,true));
				$values = '';
				error_log("gggggggggggg2");
				for($j = 0; $j<count($arr);$j++)
				{	
					$newValue='';
					for($k = 0; $k < count($inputIds);$k++)
					{
						 
						if($feedId[$k]==(string)$arr[$j][$tableColumnPrefix.'feedid'])
						{
							$newValue=$inputValues[$k];
							
						}
					}	
					
					$values = "{$values}    ('".$arr[$j][$tableColumnPrefix.'mastermemberid']."',
											 '".$arr[$j][$tableColumnPrefix.'mastermemberno']."',
											 '".$arr[$j][$tableColumnPrefix.'memberid']."',
											 '".$arr[$j][$tableColumnPrefix.'memberno']."',
											 '".$arr[$j][$tableColumnPrefix.'masterleaseid']."',
											 'GROUP_".$lastGroupNum."',
											 '".$arr[$j][$tableColumnPrefix.'feedid']."',
											 '".$arr[$j][$tableColumnPrefix.'subcategory']."',
											 '".$arr[$j][$tableColumnPrefix.'fieldid']."',
											 '".$arr[$j][$tableColumnPrefix.'dropdown']."',
											 '".$arr[$j][$tableColumnPrefix.'fieldtype']."',
											 '".$arr[$j][$tableColumnPrefix.'fieldname']."',
											 '".$arr[$j][$tableColumnPrefix.'optlabel']."',
											 '".trim($newValue)."',
											 '".$arr[$j][$tableColumnPrefix.'defaultreq']."',
											 '".$arr[$j][$tableColumnPrefix.'defaultvalue']."',
											 '".$arr[$j][$tableColumnPrefix.'req']."',
											 '".$arr[$j][$tableColumnPrefix.'hide']."',
											 '".$arr[$j][$tableColumnPrefix.'edit']."',
											 '".$arr[$j][$tableColumnPrefix.'status']."',
											 '".$arr[$j][$tableColumnPrefix.'reserve']."',
											 '".$arr[$j][$tableColumnPrefix.'createdby']."',
											 '".$arr[$j][$tableColumnPrefix.'modifiedby']."',
											 '".$arr[$j][$tableColumnPrefix.'order']."',
											 '".$arr[$j][$tableColumnPrefix.'xmlmastertag']."',
											 '".$arr[$j][$tableColumnPrefix.'xmlblocktag']."',
											 '".$arr[$j][$tableColumnPrefix.'xmlchildtag']."'
											 ),";
					
					
					 	
					
				}	
				$values = rtrim($values,',');
				$insert  = "INSERT INTO {$tableName} ({$tableColumnPrefix}mastermemberid,
													{$tableColumnPrefix}mastermemberno,
													{$tableColumnPrefix}memberid,
													{$tableColumnPrefix}memberno,
													{$tableColumnPrefix}masterleaseid,
													{$tableColumnPrefix}grouplabel,		
                                                    {$tableColumnPrefix}feedid,
                                                    {$tableColumnPrefix}subcategory,
                                                    {$tableColumnPrefix}fieldid,
                                                    {$tableColumnPrefix}dropdown,
													{$tableColumnPrefix}fieldtype,
                                                    {$tableColumnPrefix}fieldname,
													{$tableColumnPrefix}optlabel,	
                                                    {$tableColumnPrefix}value,
                                                    {$tableColumnPrefix}defaultreq,
                                                    {$tableColumnPrefix}defaultvalue,
													{$tableColumnPrefix}req,
													{$tableColumnPrefix}hide,
													{$tableColumnPrefix}edit,
													{$tableColumnPrefix}status,
													{$tableColumnPrefix}reserve,
													{$tableColumnPrefix}createdby,
													{$tableColumnPrefix}modifiedby,
													{$tableColumnPrefix}order,
													{$tableColumnPrefix}xmlmastertag,
													{$tableColumnPrefix}xmlblocktag,
													{$tableColumnPrefix}xmlchildtag
													) 
													VALUES {$values}";
				
				 
				$addedResult = mysqli_query($this->connection, $insert);
				if(!$addedResult)
				{	 
				  error_log("Error in Adding New Records");	
				  mysqli_close($this->connection);
				  return 'NewRecordError';
				}
				else
				{
					$selectUpdated = "SELECT * FROM {$tableName} where {$tableColumnPrefix}mastermemberid='{$mastermemberid}' AND {$tableColumnPrefix}mastermemberno='{$mastermemberno}' AND {$tableColumnPrefix}memberid='{$leaseMemberId}' AND {$tableColumnPrefix}memberno='{$leaseMemberNo}' AND {$tableColumnPrefix}masterleaseid={$masterleaseid} AND {$tableColumnPrefix}grouplabel='GROUP_{$lastGroupNum}'  ORDER BY 	{$tableColumnPrefix}grouplabel,{$tableColumnPrefix}feedid ASC";
					error_log($selectUpdated);
					$resultUpdated = mysqli_query($this->connection, $selectUpdated);
					if(!$resultUpdated || mysqli_num_rows($resultUpdated) <= 0)
					{
					  error_log("Unknown Error");	
					  mysqli_close($this->connection);
					  return 'NewRecordError';
					}
					else
					{
						$resultSet= [];
						while($newRow = mysqli_fetch_assoc($resultUpdated))
						{
							array_push($resultSet, $newRow);  
						}
						mysqli_close($this->connection);
						return $resultSet;
					}
				}	
			}
		}		
		 
	}
	
	
	
/*
	=======================================================
	    Function to Add Only New Emergency Contact Details 
    =======================================================
	*/		
	
	function addNewEmergencyContact($mastermemberid,$mastermemberno,$etable,$etableColumnPrefix,$einputValues,$einputIds,$egrouplabel,$efeedId,$masterGroupLabel)
	{
		if(!$this->DBLogin())
		{
		  $this->HandleError("Database login failed!");
		  return false;
		}
		$masterleaseid = $_SESSION['masterleaseid'];
		$leaseMemberId=$_SESSION['leaseMemberId'];
		$leaseMemberNo=$_SESSION['leaseMemberNo'];
		

		$econtactqry = "SELECT DISTINCT({$etableColumnPrefix}grouplabel) FROM {$etable} where {$etableColumnPrefix}mastermemberid='{$mastermemberid}' AND {$etableColumnPrefix}mastermemberno='{$mastermemberno}' AND {$etableColumnPrefix}memberid='{$leaseMemberId}' AND {$etableColumnPrefix}memberno='{$leaseMemberNo}' AND {$etableColumnPrefix}masterleaseid={$masterleaseid}  AND  {$etableColumnPrefix}mastergrouplabel='{$masterGroupLabel}' ORDER BY {$etableColumnPrefix}grouplabel ASC";
 
		$econtactresult = mysqli_query($this->connection, $econtactqry);
		$ar3= [];
		if(!empty($econtactresult) && mysqli_num_rows($econtactresult)>0)
		{
				error_log("kkkkkkkkkkkkkk");
				//continue, nothing to check
		}
		else
		{
			$emasterqry = "SELECT DISTINCT({$etableColumnPrefix}mastergrouplabel) FROM {$etable} where {$etableColumnPrefix}mastermemberid='{$mastermemberid}' AND {$etableColumnPrefix}mastermemberno='{$mastermemberno}' AND {$etableColumnPrefix}memberid='{$leaseMemberId}' AND {$etableColumnPrefix}memberno='{$leaseMemberNo}' AND {$etableColumnPrefix}masterleaseid={$masterleaseid}   ORDER BY {$etableColumnPrefix}grouplabel ASC  LIMIT 1";
			$emasterLabel = mysqli_query($this->connection, $emasterqry);
			$labelRow = mysqli_fetch_array($emasterLabel);
			$currentMasterGroupLabel = $labelRow["{$etableColumnPrefix}mastergrouplabel"];
			$econtactqry = "SELECT DISTINCT({$etableColumnPrefix}grouplabel) FROM {$etable} where {$etableColumnPrefix}mastermemberid='{$mastermemberid}' AND {$etableColumnPrefix}mastermemberno='{$mastermemberno}' AND {$etableColumnPrefix}memberid='{$leaseMemberId}' AND {$etableColumnPrefix}memberno='{$leaseMemberNo}' AND {$etableColumnPrefix}masterleaseid={$masterleaseid}  AND  {$etableColumnPrefix}mastergrouplabel='{$currentMasterGroupLabel}' ORDER BY {$etableColumnPrefix}grouplabel ASC";
			 
			$econtactresult = mysqli_query($this->connection, $econtactqry);
		}	
			
		while($row = mysqli_fetch_assoc($econtactresult))
		{
			array_push($ar3, $row);  
		}
		$eselectlabel = $ar3[0][$etableColumnPrefix.'grouplabel'];
		 
		$elastGroupText = array_values(array_slice($ar3, -1))[0];
		$elastGroupText = $elastGroupText[$etableColumnPrefix.'grouplabel'];
		$elastGroupText = explode('_', $elastGroupText);
		$elastGroupNum = $elastGroupText[1];
		$elastGroupNum++;
		$newGrouplable = "GROUP_".$elastGroupNum;
		/* Selection for Duplicating Rows in leaseecontact table */
		$eselectQuery = "SELECT * FROM {$etable} where {$etableColumnPrefix}mastermemberid='{$mastermemberid}' AND {$etableColumnPrefix}mastermemberno='{$mastermemberno}' AND {$etableColumnPrefix}memberid='{$leaseMemberId}' AND {$etableColumnPrefix}memberno='{$leaseMemberNo}' AND {$etableColumnPrefix}masterleaseid={$masterleaseid}  AND  {$etableColumnPrefix}mastergrouplabel='{$masterGroupLabel}'  AND  {$etableColumnPrefix}grouplabel='{$eselectlabel}' ORDER BY {$etableColumnPrefix}grouplabel ASC";
		$eselectResult = mysqli_query($this->connection, $eselectQuery);
		$earr= [];
		while($row = mysqli_fetch_assoc($eselectResult))
		{
			array_push($earr, $row);  
		}
		
		
		$evalues = '';
		for($j = 0; $j<count($earr);$j++)
		{	
			$enewValue='';
			for($k = 0; $k < count($einputIds);$k++)
			{
				 
				if($efeedId[$k]==(string)$earr[$j][$etableColumnPrefix.'feedid'])
				{
					$enewValue=$einputValues[$k];
					 
				}
			}	
			
			$evalues = "{$evalues}  ('".$earr[$j][$etableColumnPrefix.'mastermemberid']."',
									 '".$earr[$j][$etableColumnPrefix.'mastermemberno']."',
									 '".$earr[$j][$etableColumnPrefix.'memberid']."',
									 '".$earr[$j][$etableColumnPrefix.'memberno']."',
									 '".$earr[$j][$etableColumnPrefix.'masterleaseid']."',
									 '".$newGrouplable."',
									 '".$earr[$j][$etableColumnPrefix.'feedid']."',
									 '".$earr[$j][$etableColumnPrefix.'subcategory']."',
									 '".$earr[$j][$etableColumnPrefix.'fieldid']."',
									 '".$earr[$j][$etableColumnPrefix.'dropdown']."',
									 '".$earr[$j][$etableColumnPrefix.'fieldtype']."',
									 '".$earr[$j][$etableColumnPrefix.'fieldname']."',
									 '".$earr[$j][$etableColumnPrefix.'optlabel']."',
									 '".trim($enewValue)."',
									 '".$earr[$j][$etableColumnPrefix.'defaultreq']."',
									 '".$earr[$j][$etableColumnPrefix.'defaultvalue']."',
									 '".$earr[$j][$etableColumnPrefix.'req']."',
									 '".$earr[$j][$etableColumnPrefix.'hide']."',
									 '".$earr[$j][$etableColumnPrefix.'edit']."',
									 '1',
									 '".$earr[$j][$etableColumnPrefix.'reserve']."',
									 '".$earr[$j][$etableColumnPrefix.'createdby']."',
									 '".$earr[$j][$etableColumnPrefix.'modifiedby']."',
									 '".$earr[$j][$etableColumnPrefix.'order']."',
									 '".$earr[$j][$etableColumnPrefix.'xmlmastertag']."',
									 '".$earr[$j][$etableColumnPrefix.'xmlblocktag']."',
									 '".$earr[$j][$etableColumnPrefix.'xmlchildtag']."',
									 '".$masterGroupLabel."'),";
			
			
				
			
		}
		$evalues = rtrim($evalues,',');
		$einsert  = "INSERT INTO {$etable} ({$etableColumnPrefix}mastermemberid,
										{$etableColumnPrefix}mastermemberno,
										{$etableColumnPrefix}memberid,
										{$etableColumnPrefix}memberno,
										{$etableColumnPrefix}masterleaseid,
										{$etableColumnPrefix}grouplabel,		
										{$etableColumnPrefix}feedid,
										{$etableColumnPrefix}subcategory,
										{$etableColumnPrefix}fieldid,
										{$etableColumnPrefix}dropdown,
										{$etableColumnPrefix}fieldtype,
										{$etableColumnPrefix}fieldname,
										{$etableColumnPrefix}optlabel,	
										{$etableColumnPrefix}value,
										{$etableColumnPrefix}defaultreq,
										{$etableColumnPrefix}defaultvalue,
										{$etableColumnPrefix}req,
										{$etableColumnPrefix}hide,
										{$etableColumnPrefix}edit,
										{$etableColumnPrefix}status,
										{$etableColumnPrefix}reserve,
										{$etableColumnPrefix}createdby,
										{$etableColumnPrefix}modifiedby,
										{$etableColumnPrefix}order,
										{$etableColumnPrefix}xmlmastertag,
										{$etableColumnPrefix}xmlblocktag,
										{$etableColumnPrefix}xmlchildtag,
										{$etableColumnPrefix}mastergrouplabel
										) 
										VALUES {$evalues}";
		
		$eaddedResult = mysqli_query($this->connection, $einsert);
		if(!$eaddedResult)
		{	 
		  error_log("Error in Adding New Records");	
		  mysqli_close($this->connection);
		  return 'NewRecordError';
		}
		else
		{
			$selectUpdated = "SELECT * FROM {$etable} where {$etableColumnPrefix}mastermemberid='{$mastermemberid}' AND {$etableColumnPrefix}mastermemberno='{$mastermemberno}' AND {$etableColumnPrefix}memberid='{$leaseMemberId}' AND {$etableColumnPrefix}memberno='{$leaseMemberNo}' AND {$etableColumnPrefix}masterleaseid={$masterleaseid} AND {$etableColumnPrefix}mastergrouplabel='{$masterGroupLabel}' AND {$etableColumnPrefix}grouplabel='{$newGrouplable}'  ORDER BY 	{$etableColumnPrefix}grouplabel,{$etableColumnPrefix}feedid ASC";
			 
			$resultUpdated = mysqli_query($this->connection, $selectUpdated);
			if(!$resultUpdated || mysqli_num_rows($resultUpdated) <= 0)
			{
			  error_log("Unknown Error");	
			  mysqli_close($this->connection);
			  return 'NewRecordError';
			}
			else
			{
				$resultSet= [];
				while($newRow = mysqli_fetch_assoc($resultUpdated))
				{
					array_push($resultSet, $newRow);  
				}
				mysqli_close($this->connection);
				return $resultSet;
			}
		}
	}
	
	
	
	/*
	=======================================================
	    Function to Delete Records from Pet,Resident,Vech Record 
    =======================================================
	*/	
	function deleteLeaseRecord($mastermemberid,$mastermemberno,$table,$tableColumnPrefix,$groupLabel)
	{	 
		if(!$this->DBLogin())
		{
		  $this->HandleError("Database login failed!");
		  return false;
		}
		$masterleaseid = $_SESSION['masterleaseid'];
		$leasememberid=$_SESSION['leaseMemberId'];
		$leasememberno=$_SESSION['leaseMemberNo'];	
	
	
		$qry = "Delete from {$table} where {$tableColumnPrefix}mastermemberid='{$mastermemberid}' AND {$tableColumnPrefix}mastermemberno='{$mastermemberno}' AND {$tableColumnPrefix}memberid='".$_SESSION['leaseMemberId']."' AND {$tableColumnPrefix}memberno='{$_SESSION['leaseMemberNo']}' AND {$tableColumnPrefix}masterleaseid={$masterleaseid} AND {$tableColumnPrefix}grouplabel='{$groupLabel}'";
		$deleteResult = mysqli_query($this->connection, $qry);
		if(mysqli_affected_rows($this->connection) > 0)
		{
			if($table=='leaseresidentinforp')
			{
				$table = 'leaseecontactinforp';
				$tableColumnPrefix = 'leaseecontact';
				$qry = "Delete from {$table} where {$tableColumnPrefix}mastermemberid='{$mastermemberid}' AND {$tableColumnPrefix}mastermemberno='{$mastermemberno}' AND {$tableColumnPrefix}memberid='".$_SESSION['leaseMemberId']."' AND {$tableColumnPrefix}memberno='{$_SESSION['leaseMemberNo']}' AND {$tableColumnPrefix}masterleaseid={$masterleaseid} AND {$tableColumnPrefix}mastergrouplabel='{$groupLabel}'";	
				$deleteResult = mysqli_query($this->connection, $qry);
				mysqli_close($this->connection);
				return "deleteSuccess";
			}	
			else
			{
				mysqli_close($this->connection);
				return "deleteSuccess";
			}
		}
		else
		{
			mysqli_close($this->connection);
			return "deleteError";
		}	
		
	}
	
	/*
	=======================================================
	    Function to Delete Records from Pet,Resident,Vech Record 
    =======================================================
	*/	
	function deleteEmergencyContact($mastermemberid,$mastermemberno,$tableName,$tableColumnPrefix,$masterGroupLabel,$groupLabel)
	{	 
		if(!$this->DBLogin())
		{
		  $this->HandleError("Database login failed!");
		  return false;
		}
		$masterleaseid = $_SESSION['masterleaseid'];
		$leasememberid=$_SESSION['leaseMemberId'];
		$leasememberno=$_SESSION['leaseMemberNo'];	
	
		
		$econtactqry = "SELECT DISTINCT({$tableColumnPrefix}grouplabel) FROM {$tableName} where {$tableColumnPrefix}mastermemberid='{$mastermemberid}' AND {$tableColumnPrefix}mastermemberno='{$mastermemberno}' AND {$tableColumnPrefix}memberid='{$_SESSION['leaseMemberId']}' AND {$tableColumnPrefix}memberno='{$_SESSION['leaseMemberNo']}' AND {$tableColumnPrefix}masterleaseid={$masterleaseid}  AND  {$tableColumnPrefix}mastergrouplabel='{$masterGroupLabel}' ORDER BY {$tableColumnPrefix}grouplabel ASC";
		
		 		
		$econtactresult = mysqli_query($this->connection, $econtactqry);
		 
		$count = mysqli_num_rows($econtactresult);
		 
		if($count>=2)
		{
			$qry = "Delete from {$tableName} where {$tableColumnPrefix}mastermemberid='{$mastermemberid}' AND {$tableColumnPrefix}mastermemberno='{$mastermemberno}' AND {$tableColumnPrefix}memberid='".$_SESSION['leaseMemberId']."' AND {$tableColumnPrefix}memberno='{$_SESSION['leaseMemberNo']}' AND {$tableColumnPrefix}masterleaseid={$masterleaseid} AND {$tableColumnPrefix}grouplabel='{$groupLabel}' AND  {$tableColumnPrefix}mastergrouplabel='{$masterGroupLabel}'";
			$deleteResult = mysqli_query($this->connection, $qry);
		} 		
		else if($count==1){
			$qry = "Update {$tableName} SET {$tableColumnPrefix}status = 0 where {$tableColumnPrefix}mastermemberid='{$mastermemberid}' AND {$tableColumnPrefix}mastermemberno='{$mastermemberno}' AND {$tableColumnPrefix}memberid='".$_SESSION['leaseMemberId']."' AND {$tableColumnPrefix}memberno='{$_SESSION['leaseMemberNo']}' AND {$tableColumnPrefix}masterleaseid={$masterleaseid} AND {$tableColumnPrefix}grouplabel='{$groupLabel}' AND  {$tableColumnPrefix}mastergrouplabel='{$masterGroupLabel}'";
			$deleteResult = mysqli_query($this->connection, $qry);
		}	 
		
		
		
		if(mysqli_affected_rows($this->connection) > 0)
		{
			 
		}
		else
		{
			mysqli_close($this->connection);
			return "deleteError";
		}	
		
	}
	
	/*
	=======================================================
	    Function to Get Generated Master Lease ID Record 
    =======================================================
	*/	
	
	
	
	function showMasterLeaseRecord($masterleaseid,$mastermemberid,$mastermemberno,$leasememberid,$leasememberno)
	{
		if(!$this->DBLogin())
		{
		  $this->HandleError("Database login failed!");
		  return false;
		}
		if(isset($masterleaseid)&&isset($mastermemberid)&&isset($mastermemberno))
		{
			$qry = "Select * from 	masterleaseid where 	masterleaseid = {$masterleaseid} AND masterleasemastermemberid='{$mastermemberid}' AND masterleasemastermemberno='{$mastermemberno}' AND masterleasememberid='{$leasememberid}' AND masterleasememberno='{$leasememberno}'";
			 
			$result = mysqli_query($this->connection,$qry);
			//error_log(print_r($result , true));
			if(!$result || mysqli_num_rows($result) <= 0)
			{
			  error_log("Error in feteching the records from the repositories or no Matched Records found");	
			  mysqli_close($this->connection);
			  return false;
			}
			else
			{
				$ar2= [];
				while($row = mysqli_fetch_assoc($result))
				{
					array_push($ar2, $row);  
				}
				mysqli_close($this->connection);
				 return $ar2;
			}	
		}	
	}
	
	
	

	/*
	=======================================================
	    Function for   Calculation  Setup -- List of Monthly Charges
    =======================================================
	*/	
	

function calculationTotalRent($tempId,$mastermemberid,$mastermemberno,$tableName,$pre)
{
	if(!$this->DBLogin())
	{
	  $this->HandleError("Database login failed!");
	  return false;
	}
	
	$qry  = "select * from {$tableName} where {$pre}tempid={$tempId} AND {$pre}select=1 and {$pre}subcategory='monthly' AND  {$pre}fieldtype='Dollars'";
	 
	$result = mysqli_query($this->connection,$qry);
	if(!$result || mysqli_num_rows($result) <= 0)
	{
	  error_log("Error in feteching the records from the repositories or no Matched Records found");	
	  mysqli_close($this->connection);
	  return false;
	}
	else
	{
		$ar2= [];
		while($row = mysqli_fetch_assoc($result))
		{
			array_push($ar2, $row);  
		}
		mysqli_close($this->connection);
		return $ar2;
	}	
}

/*
	=======================================================
	    Function for   Calculation  Setup -- List of Move In Charges
    =======================================================
	*/	
	

function calculateCharges($tempId,$mastermemberid,$mastermemberno,$tableName,$pre,$subcategory)
{
	if(!$this->DBLogin())
	{
	  $this->HandleError("Database login failed!");
	  return false;
	}
	
	$qry  = "select * from {$tableName} where {$pre}tempid={$tempId} AND {$pre}select=1 and {$pre}subcategory='{$subcategory}' AND  {$pre}fieldtype='Dollars'";
	 
	$result = mysqli_query($this->connection,$qry);
	if(!$result || mysqli_num_rows($result) <= 0)
	{
	  error_log("Error in feteching the records from the repositories or no Matched Records found");	
	  mysqli_close($this->connection);
	  return false;
	}
	else
	{
		$ar2= [];
		while($row = mysqli_fetch_assoc($result))
		{
			array_push($ar2, $row);  
		}
		mysqli_close($this->connection);
		return $ar2;
	}	
}


/*
	=======================================================
	    Function for   Calculation  Setup -- List of total parking storage charges
    =======================================================
	*/	
	

function calculateParkingStorageCharges($tempId,$mastermemberid,$mastermemberno,$tableName,$pre,$machString)
{
	if(!$this->DBLogin())
	{
	  $this->HandleError("Database login failed!");
	  return false;
	}
	
	$qry  = "select * from {$tableName} where {$pre}tempid={$tempId} AND {$pre}select=1 and {$pre}fieldid LIKE'{$machString}%'";
	 error_log($qry);
	$result = mysqli_query($this->connection,$qry);
	if(!$result || mysqli_num_rows($result) <= 0)
	{
	  error_log("Error in feteching the records from the repositories or no Matched Records found");	
	  mysqli_close($this->connection);
	  return false;
	}
	else
	{
		$ar2= [];
		while($row = mysqli_fetch_assoc($result))
		{
			array_push($ar2, $row);  
		}
		mysqli_close($this->connection);
		return $ar2;
	}	
}



/*
	=======================================================
	    Function for   Calculation  Setup --- Select System fields
    =======================================================
	*/	
function calculationOthers($tempId,$mastermemberid,$mastermemberno,$tableName,$pre)
{
	if(!$this->DBLogin())
	{
	  $this->HandleError("Database login failed!");
	  return false;
	}
	
	$qry  = "select * from {$tableName} where {$pre}tempid={$tempId} AND {$pre}select=1 and {$pre}subcategory='system'";
	 
	$result = mysqli_query($this->connection,$qry);
	if(!$result || mysqli_num_rows($result) <= 0)
	{
	  error_log("Error in feteching the records from the repositories or no Matched Records found");	
	  mysqli_close($this->connection);
	  return false;
	}
	else
	{
		$ar2= array();
		while($row = mysqli_fetch_assoc($result))
		{	$cat = $row['finrpfieldid'];
			$ar2[$cat][] = $row;
		 
		}
		mysqli_close($this->connection);
		return $ar2;
	}	
}	
/*
	=======================================================
	    Function for   Calculation  Setup
    =======================================================
	*/	
function calculationRecords($tempId,$mastermemberid,$mastermemberno,$tableName,$pre)
{
	if(!$this->DBLogin())
	{
	  $this->HandleError("Database login failed!");
	  return false;
	}
	
	$qry  = "select * from {$tableName} where {$pre}tempid={$tempId}";
	 
	$result = mysqli_query($this->connection,$qry);
	if(!$result || mysqli_num_rows($result) <= 0)
	{
	  error_log("Error in feteching the records");	
	  mysqli_close($this->connection);
	  return false;
	}
	else
	{
		$ar2= array();
		while($row = mysqli_fetch_assoc($result))
		{	$groupLabel = $row['calrpgrouplabel'];
			$defaultFeedId = $row['calrpdefaultrpfeedid'];
			$value = $row['calrpfieldvalue'];
			$ar2[$groupLabel][$defaultFeedId] = $value;
		}
		error_log(print_r($ar2,true));
		mysqli_close($this->connection);
		return $ar2;
	}	
}	


/*
	=======================================================
	    Function for   Calculation  Setup -- calculation-setup
    =======================================================
	*/	
function calculationSetupInsert($templateId,$mastermemberid,$mastermemberno,$values)
{
	if(!$this->DBLogin())
	{
	  $this->HandleError("Database login failed!");
	  return false;
	}
	
	$del = "DELETE from calulationsetuprp where 	calrpmastermemberid='{$mastermemberid}' AND 	calrpmastermemberno = '{$mastermemberno}' AND calrptempid = {$templateId}";
	$delResult = mysqli_query($this->connection,$del);
	$insert = "insert into calulationsetuprp(calrpmastermemberid,
											 calrpmastermemberno,
											 calrptempid,
											 calrpgrouplabel,
											 calrpcategory,
											 calrpsubcategory,
											 calrpcurrentrowid,
											 calrpdefaultrpfeedid,
											 calrpfieldid,
											 calrpfieldname,
											 calrpfieldvalue,
											 calrpmodifiedby) Values  {$values}";
											 
		error_log($insert);
		$insertResult = mysqli_query($this->connection,$insert);		
		if(!$insertResult)
		{
		  error_log("Error in Inserting the records");	
		  mysqli_close($this->connection);
		  return false;
		}
		else
		{
			 
			mysqli_close($this->connection);
			return true;
		}	
}

/*
	=====================================================================================
	|||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||
	=====================================================================================
		                       XML GENERATION FUNCTIONS
    =====================================================================================
	|||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||
	=====================================================================================
*/
	/*
	=======================================================
	      Function to identify table prefix
    =======================================================
	*/					 
	function leaseTablePrefix($table)
	{
		$pre =[];
		if($table == "property")
		{	
			$pre['tableName']="leasepropertyinforp";
			$pre['tablepre']="leaseproperty";
		}
		elseif($table == "unitaddres")
		{	
			$pre['tableName']="leaseunitaddressrp";
			$pre['tablepre']="leaseunitaddress";
		}
		elseif($table == "leaseterms")
		{	
			$pre['tableName']="leaseleasetermrp";
			$pre['tablepre']="leaseleaseterm";
		}
		elseif($table == "financials")
		{	
			$pre['tableName']="leasefinancialinforp";
			$pre['tablepre']="leasefinancial";
		}
		elseif($table == "limits")
		{	
			$pre['tableName']="leaselimitinforp";
			$pre['tablepre']="leaselimit";
		}
		elseif($table == "resident")
		{	
			$pre['tableName']="leaseresidentinforp";
			$pre['tablepre']="leaseresident";
		}
		elseif($table == "econtact")
		{	
			$pre['tableName']="leaseecontactinforp";
			$pre['tablepre']="leaseecontact";
		}
		
		elseif($table == "pets")
		{	
			$pre['tableName']="leasepetinforp";
			$pre['tablepre']="leasepet";
		}
		elseif($table == "vehiclepar")
		{	
			$pre['tableName']="leasevehicleparkrp";
			$pre['tablepre']="leasevehiclepark";
		}
		elseif($table == "appliances")
		{	
			$pre['tableName']="leaseapplianceinforp";
			$pre['tablepre']="leaseappliance";
		}
		elseif($table == "utilities")
		{	
			$pre['tableName']="leaseutilityinforp";
			$pre['tablepre']="leaseutility";
		}
		elseif($table == "storage")
		{	
			$pre['tableName']="leasestorageinforp";
			$pre['tablepre']="leasestore";
		}
		elseif($table == "misc")
		{	
			$pre['tableName']="leasemiscinforp";
			$pre['tablepre']="leasemisc";
		}
		else
		{
			mysqli_close($this->connection);
			return false;
		}
		return($pre);
	}

	/*
	=======================================================
	   Function TO get details based on tablle name passed
    =======================================================
	*/	
	
	function xmlGetTableRecords($tablename,$masterleaseid,$mastermemberid,$mastermemberno,$leasememberid,$leasememberno)
	{   if($masterleaseid==$_SESSION['masterleaseid'])
		{	if(!$this->DBLogin())
			{	
				$this->HandleError("Database login failed!");
				return false;
			}
			$tableInfo = $this->leaseTablePrefix($tablename);
			$dbTableName = $tableInfo['tableName'];
			$pre = $tableInfo['tablepre'];
			$query = "
				SELECT *
					FROM {$dbTableName} WHERE {$pre}masterleaseid ={$masterleaseid} AND {$pre}mastermemberid = '{$mastermemberid}' AND  {$pre}mastermemberno = '{$mastermemberno}' AND 	{$pre}memberid='{$leasememberid}' AND {$pre}memberno='{$leasememberno}' AND {$pre}status=1
			    
				ORDER BY 	{$pre}grouplabel,{$pre}order,{$pre}feedid ASC"; 
			 
			 
			$result = mysqli_query($this->connection,$query);
			 
			if(!$result || mysqli_num_rows($result) <= 0)
				{
				  error_log("Error in feteching the records from the repositories or no Matched Records found");	
				  mysqli_close($this->connection);
				  return false;
				}
			else
			{
				$ar2= [];
				while($row = mysqli_fetch_assoc($result))
				{
					array_push($ar2, $row);  
				}
				mysqli_close($this->connection);
				 
				
				return $ar2;
			}
		}	
		else
		{
			return false;
		}	
	}
	

	/*
	=======================================================
	   Function TO get Formatted Result for Given Table
    =======================================================
	*/	
	
	function xmlGetFormattedRecords($tablename,$masterleaseid,$mastermemberid,$mastermemberno,$leasememberid,$leasememberno)
	{   if($masterleaseid==$_SESSION['masterleaseid'])
		{	if(!$this->DBLogin())
			{	
				$this->HandleError("Database login failed!");
				return false;
			}
			$tableInfo = $this->leaseTablePrefix($tablename);
			$dbTableName = $tableInfo['tableName'];
			$pre = $tableInfo['tablepre'];
			$query = "
				SELECT *
					FROM {$dbTableName} WHERE {$pre}masterleaseid ={$masterleaseid} AND {$pre}mastermemberid = '{$mastermemberid}' AND  {$pre}mastermemberno = '{$mastermemberno}' AND 	{$pre}memberid='{$leasememberid}' AND {$pre}memberno='{$leasememberno}' AND {$pre}status=1
			    
				ORDER BY 	{$pre}grouplabel,{$pre}order,{$pre}feedid ASC"; 
			 
			 
			$result = mysqli_query($this->connection,$query);
			 
			if(!$result || mysqli_num_rows($result) <= 0)
				{
				  error_log("Error in feteching the records from the repositories or no Matched Records found");	
				  mysqli_close($this->connection);
				  return false;
				}
			else
			{
				$ar2= [];
				while($row = mysqli_fetch_assoc($result))
				{
					
					if($tablename == 'econtact')
					{
						$grouplabel = $row[$pre.'grouplabel'];
						$mastergrouplabel = $row[$pre.'mastergrouplabel'];
						$xmlmastertag = $row[$pre.'xmlmastertag'];
						
						$ar2[$mastergrouplabel][$grouplabel][$xmlmastertag][] = $row;
					}
					else
					{		
						$grouplabel = $row[$pre.'grouplabel'];
						$subcategory = $row[$pre.'subcategory'];
						$xmlmastertag = $row[$pre.'xmlmastertag'];
					
						$ar2[$grouplabel][$xmlmastertag][] = $row;
					}	
				}
				mysqli_close($this->connection);
				 
				
				return $ar2;
			}
		}	
		else
		{
			return false;
		}	
	}
	
	/*
	=======================================================
	   Function TO getGroup Lables
    =======================================================
	*/	
	
	function xmlGetGroupLable($tablename,$masterleaseid,$mastermemberid,$mastermemberno,$leasememberid,$leasememberno)
	{   if($masterleaseid==$_SESSION['masterleaseid'])
		{	if(!$this->DBLogin())
			{	
				$this->HandleError("Database login failed!");
				return false;
			}
			$tableInfo = $this->leaseTablePrefix($tablename);
			$dbTableName = $tableInfo['tableName'];
			$pre = $tableInfo['tablepre'];
			
			if($tablename == 'econtact')
			{
				 
				$query = "
				SELECT   {$pre}mastergrouplabel,{$pre}grouplabel 
					FROM {$dbTableName} WHERE {$pre}masterleaseid ={$masterleaseid} AND {$pre}mastermemberid = '{$mastermemberid}' AND  {$pre}mastermemberno = '{$mastermemberno}' AND 	{$pre}memberid='{$leasememberid}' AND {$pre}memberno='{$leasememberno}' AND {$pre}status=1
				
				group by{$pre}mastergrouplabel,{$pre}grouplabel
				ORDER BY 	{$pre}grouplabel,{$pre}order,{$pre}feedid ASC"; 
			}
			else
			{
				$query = "
				SELECT DISTINCT({$pre}grouplabel)
					FROM {$dbTableName} WHERE {$pre}masterleaseid ={$masterleaseid} AND {$pre}mastermemberid = '{$mastermemberid}' AND  {$pre}mastermemberno = '{$mastermemberno}' AND 	{$pre}memberid='{$leasememberid}' AND {$pre}memberno='{$leasememberno}' AND {$pre}status=1
			    
				ORDER BY 	{$pre}grouplabel,{$pre}order,{$pre}feedid ASC"; 
			}	
			$result = mysqli_query($this->connection,$query);
			 
			if(!$result || mysqli_num_rows($result) <= 0)
				{
				  error_log("Error in feteching the records from the repositories or no Matched Records found 4");	
				  mysqli_close($this->connection);
				  return false;
				}
			else
			{
				$ar2= [];
				while($row = mysqli_fetch_assoc($result))
				{
					if($tablename == 'econtact')
					{
						$grouplabel = $row[$pre.'grouplabel'];
						$mastergrouplabel = $row[$pre.'mastergrouplabel'];
						$xmlmastertag = $row[$pre.'xmlmastertag'];
						$ar2[$mastergrouplabel][] = $row;
					}	
					else
					{	
						array_push($ar2, $row);  
					}	
				}
				mysqli_close($this->connection);
				 
				 
				return $ar2;
			}
		}	
		else
		{
			return false;
		}	
	}
	

	
	


	
/* 
   ===========================================================================
   ===============Sarfaraz code starts here  ================
   ===========================================================================
*/
/* Author :Sarfaraz 
 * Date : 3 May 2017 
 * Used in: documenttemplate.php
 * Description :  
 * This function is created for inserting documents 
 * in documents table to the specific  members*/        
        
function insertDocument
(
	$mastermemberid,
	$mastermemberno,
	$membername,
	$memberno,
	$memberstate,
	$username,
	$documentname,
	$memberid,
	$documentuploaded
)
{
	if(!$this->DBLogin())
	{
	  $this->HandleError("Database login failed!");
	  return false;
	}
	
		/* $result['uploadResult'] = 	$uploadResult;
		$result['folderName'] = $ftp_folderPath;
		$result['docName'] = 	$actual_image_name; */
	$ftpResult = $this->insertFile($documentname, $mastermemberid,$mastermemberno, $membername, $memberstate);
	if($ftpResult['fileFormat'])
	{
		
	
		if($ftpResult['uploadResult'])
		{
		
		
			$qry='INSERT INTO documents(
				 mastermemberid,
				 mastermemberno,
				 ftpfoldername,
				 ftpbaseurl,
				 username,
				 documentname,
				 dateuploaded,
				 documentuploaded
				  
				)
				VALUES(
				"' . $this->SanitizeForSQL($this->connection,$mastermemberid) . '",
				"' . $this->SanitizeForSQL($this->connection,$mastermemberno) . '",
				"' . $this->SanitizeForSQL($this->connection, $ftpResult['folderName']) . '",
				"' . $this->SanitizeForSQL($this->connection,$this->ftpbaseurl) . '",
				"' . $this->SanitizeForSQL($this->connection,$username) . '",
				"' . $this->SanitizeForSQL($this->connection, $ftpResult['docName']) . '",
					now(),
				"' . $this->SanitizeForSQL($this->connection, $documentuploaded) . '"
				
				);
		  ';
		
			if (mysqli_query($this->connection, $qry)) 
			{
				 mysqli_close($this->connection);
				 return true ;
			}
			else
			{
				 mysqli_close($this->connection);
				 return false;
			} 
		}
		else
		{
			 mysqli_close($this->connection);
		     return false;
		}	
	}
	else
	{
		 mysqli_close($this->connection);
				 return false;
	}	
	
}  
        
        
/*
* Author :Sarfaraz 
* Date : 3 May 2017 
* Used in: documenttemplate.php
* Description :   
* function for inserting image& concatinating MemberId & 
* member number to actual filaname
*/
function insertFile($document,$mastermemberid ,$mastermemberno,$membername ,$memberstate) {
	
	
		$dir = $mastermemberid."-".$mastermemberno;
		$actual_image_name='';
		$uploadResult = '';
		$ftp_folderPath = '';
		$fileFormat = false;
		$valid_formats = array("pdf","doc","docx","DOCX","rtf","PDF","DOC","RTF","xlsx","XSLX") ;
		if(isset($_POST) and $_SERVER['REQUEST_METHOD'] == "POST")
        {
                $name = $_FILES['document']['name'];
                $size = $_FILES['document']['size'];
                $img = (pathinfo($name));
                $docName =  preg_replace("/[^A-Za-z0-9\_\-\.]/", '', $img["filename"]);
                $imgSize = strlen($docName);
                if(strlen($name))
                        {
                        //list($txt, $ext) = explode(".", $name);
						$ext = pathinfo($name, PATHINFO_EXTENSION);
						if(in_array($ext,$valid_formats))
                        {
                            $fileFormat = true;
							$size = strlen($name);
                            $actual_image_name =  $mastermemberid."-".$mastermemberno."-".rand().rand()."-".$docName.".".$ext;
                            //move_uploaded_file($tmp, $path.$actual_image_name);
							
							 
						   /* -------------------------------------------------
								function call to  upload it to Remote Server 
							 --------------------------------------------------- */
							// connect to FTP server (port 21)
							$conn_id = $this->ftpConnect();
							if($conn_id)
							{								 
								$ftp_fileName = $actual_image_name;
								$ftp_fileTemp = $_FILES["document"]["tmp_name"];
								$ftp_folderName = $dir;
								$ftp_folderPath = "/".$ftp_folderName;
								$ftp_folderStatus = 0;
								$ftp_folderCurrent = 0;
								// turn on passive mode transfers (some servers need this)
								ftp_pasv($conn_id, true);
								$pushd = ftp_pwd($conn_id);
								if ($pushd !== false && @ftp_chdir($conn_id, $ftp_folderPath))
								{
									$ftp_folderCurrent = 1; 
									$ftp_folderStatus = 1;
								}	 
								else
								{
									if(ftp_mkdir($conn_id, $ftp_folderName))
									{
										// folder created
										$ftp_folderStatus = 1;
										
									}
									else
									{	ftp_close($conn_id);
										return false;
									}		
								 }

								if($ftp_folderStatus)
								{
									if($ftp_folderCurrent)
									{
										// do nothing	
									}
									else
									{
										ftp_chdir($conn_id, $ftp_folderName);
									}		
										
									$upload = ftp_put($conn_id, $ftp_fileName, $ftp_fileTemp, FTP_BINARY);	 
									if($upload)
									{
										$uploadResult = 1;
									}
									else
									{
										$uploadResult = 0;
									}		
								}	
								 
								// If you are using PHP4 then you need to use this code:
								// (because the "ftp_chmod" command is just available in PHP5+)
								if (!function_exists('ftp_chmod')) {
								   function ftp_chmod($ftp_stream, $mode, $filename){
									   ftp_close($conn_id);
										return ftp_site($ftp_stream, sprintf('CHMOD %o %s', $mode, $filename));
								   }
								}
								 
								/* // try to chmod the new file to 666 (writeable)
								if (ftp_chmod($conn_id, 0666, $ftp_path) !== false) {
									print $ftp_path . " chmoded successfully to 666\n";
								} else {
									echo  "could not chmod file\n";
								} 
								*/
								 
								$contents = ftp_nlist($conn_id, ".");
								//ftp_delete($conn_id, "tableFinal.text");
								ftp_close($conn_id);
							}
								
							/* ------------------------------------------------- */
							
                        } 
												
		$result['uploadResult'] = 	$uploadResult;
		$result['fileFormat'] = 	$fileFormat;
		$result['folderName'] =     $dir;
		$result['docName'] = 	    $actual_image_name;
		
		
        }} return $result;

   }
           
	


// check file name and type
function checkFileNameType($mastermemberid, $mastermemberno, $mfilePath, $mfilename){
	 if (!$this->DBLogin())
            {
            $this->HandleError("Database login failed!");
            return false;
            }

         

        $qry = "Select * from documents where mastermemberid = '" . $mastermemberid . "' and mastermemberno = '" . $mastermemberno . "' AND documentuploaded='".$mfilename."'";

        $result = mysqli_query($this->connection, $qry);

        if (!$result || mysqli_num_rows($result) <= 0)
            {
            mysqli_close($this->connection);
            return 'nameUnique';
            } else
            {
				mysqli_close($this->connection);
				return 'nameExists';
            }
}

	
							
/*
* Author :Sarfaraz 
* Date : 3 May 2017 
* Used in: documenttemplate.php
* Description :   
* function to retrieve the available documents in
* documents table based on mastermemberid & mastermemberno
*/   
   
function retrieveDocuments($mastermemberid, $mastermemberno)
        {
        if (!$this->DBLogin())
            {
            $this->HandleError("Database login failed!");
            return false;
            }

        $ar = [];

        $qry = "Select * from documents where mastermemberid = '" . $mastermemberid . "' and mastermemberno = '" . $mastermemberno . "'";

        $result = mysqli_query($this->connection, $qry);

        if (!$result || mysqli_num_rows($result) <= 0)
            {
            mysqli_close($this->connection);
            return false;
            } else
            {
            while ($row = mysqli_fetch_assoc($result))
                {
                array_push($ar, $row);
                }
            return $ar;
            }
        }
        
/*
* Author :Sarfaraz 
* Date : 3 May 2017 
* Used in: documenttemplate.php
* Description :   
* function to delete the selected member documents 
*/        
function deleteDocuments($iddocuments)
    {
    if (!$this->DBLogin())
        {
        $this->HandleError("Database login failed!");
        return false;
        }
    $qry = "DELETE FROM documents WHERE iddocuments ='" . $iddocuments . "'";

		$result = mysqli_query($this->connection, $qry);
		/*checking if delted */
		if(mysqli_affected_rows($this->connection) > 0)
                  {
                      return'File was sucessfully Deleted';
                  }
                    else
                    {	
                    return 'Error in Deleting';
                    }
		
	
    }

    

	/*
	* Author :Sarfaraz 
	* Date : 3 May 2017 
	* Used in: documenttemplate.php
	* Description :   
	* function to retrieve the available documents in
	* documents table based on mastermemberid & mastermemberno
	*/   
   
	function retrieveDocumentsMember($mastermemberid, $mastermemberno, $memberid)
    {
		if (!$this->DBLogin())
		{
			$this->HandleError("Database login failed!");
			return false;
		}
		$ar = [];
		if($memberid=='')
		{
			$qry = "Select * from documents where mastermemberid = '" . $mastermemberid . "' and mastermemberno = '" . $mastermemberno . "'";
		}else
		{
			$qry = "Select * from documents where mastermemberid = '" . $mastermemberid . "' and mastermemberno = '" . $mastermemberno . "' and memberid = '".$memberid."';";
		}
		
        $result = mysqli_query($this->connection, $qry);
		if (!$result || mysqli_num_rows($result) <= 0)
        {
            mysqli_close($this->connection);
            return false;
        } 
		else
		{
			while ($row = mysqli_fetch_assoc($result))
			{
				array_push($ar, $row);
			}
			return $ar;
		}
    }	
/* ======================================================================================

									E SIganture SetUp  

======================================================================================== */			

/* Author :Sarfaraz 
 * Date : 24 May 2017 
 * Used in: ajax-esign.php
 * Description :  
 * This function is created for inserting esignature 
 * in esignaturesetup table to the specific  members*/        
        
function insertEsign
(
	$esignmemberid,
	$memberid,
	$esignmemberno,
	$esignmastermemberid,
	$esignmastermemberno,
	$esigntimelimit,
	$esignreminderfrequency,
	$esignmessage,
	$esignautocounter,
	$esigncountersignname,
	$esigncreatedby,
	$esignmodifiedby,
	$primaryName,
	$primaryEmail,
	$ccEmail,
	$ccName,
	$multiMember
	 
)
{
	$error_text = ''; 
	if(!$this->DBLogin())
	{
	  $this->HandleError("Database login failed!");
	  return false;
	}
	 
	$count_members =  count($multiMember);
	if($count_members<1){
			mysqli_close($this->connection);
			return false;
	}
	else
	{		
		for($i = 0; $i<$count_members;$i++)
		{	 
			$memberdetails  = explode('-', $multiMember[$i]);
			$esignmemberid = $memberdetails[3];
			$memberid =$memberdetails[0];
			$esignmemberno=$memberdetails[4];
			$esignmastermemberid=$memberdetails[1];
			$esignmastermemberno=$memberdetails[2];
			
			$emaillist = $this->deleteEsign($memberid);
			$esignaturesetup = $this->deleteEsignatureRec($memberid);
 
			$qry='INSERT INTO esignaturesetup(
					esignmemberid,
					memberid,
					esignmemberno,
					esignmastermemberid,
					esignmastermemberno,
					esigntimelimit,
					esignreminderfrequency,
					esignmessage,
					esignautocounter,
					esigncountersignname,
					esigncreatedby,
					esigncreated,
					esignlastmodified,
					esignmodifiedby
					)
					VALUES(
					"' . $this->SanitizeForSQL($this->connection,$esignmemberid) . '",
					"' . $this->SanitizeForSQL($this->connection,$memberid) . '",
					"' . $this->SanitizeForSQL($this->connection,$esignmemberno) . '",
					"' . $this->SanitizeForSQL($this->connection,$esignmastermemberid) . '",
					"' . $this->SanitizeForSQL($this->connection,$esignmastermemberno) . '",
					"' . $this->SanitizeForSQL($this->connection,$esigntimelimit) . '",
					"' . $this->SanitizeForSQL($this->connection, $esignreminderfrequency) . '",
					"' . $this->SanitizeForSQL($this->connection, $esignmessage) . '",
					"' . $this->SanitizeForSQL($this->connection, $esignautocounter) . '",
					"' . $this->SanitizeForSQL($this->connection, $esigncountersignname) . '",
					"' . $this->SanitizeForSQL($this->connection, $esigncreatedby) . '",
					now(),
					now(),
					"' . $this->SanitizeForSQL($this->connection, $esignmodifiedby) . '"
					);
			  ';
			
			
			
			if (mysqli_query($this->connection, $qry)) 
			{
				 
				$esignID = mysqli_insert_id ($this->connection); 
				if($esignID!='')
				{
					
					for($k=0;$k<count($primaryName);$k++)
					{
						$this->insertEmails
						(
							$esignID,
							$memberid,
							$primaryName[$k],
							$primaryEmail[$k],
							$emailtype="primary"
						);
						
					}
					if($ccName!='')
					{
						for($k=0;$k<count($ccName);$k++)
						{
							
							$this->insertEmails
							(
								$esignID,
								$memberid,
								$ccName[$k],
								$ccEmail[$k],
								$emailtype="cc"
							);
							
						}
					}
					
				}
				//mysqli_close($this->connection);
				//return $esignID;
			}
			else
			{
				$error_text = 'inertionFailed';
			}  
		}
		mysqli_close($this->connection);	
		if($error_text=='')
		{
			return('updateSuccess');
		}
		else
		{
			return('updateError');
		}		
		
	}
	
	  
}  
  
  
  	
	
/* Author :Sarfaraz 
 * Date : 25 May 2017 
 * Used in: ajax-esign.php
 * Description :  
 * This function is created for inserting esignature 
 * email in emaillist table to the specific  members*/        
        
function insertEmails
(
	$esignid,
	$memberid,
	$emailname,
	$emailmailid,
	$emailtype
)
{
	$qry='INSERT INTO emaillist(
			esignid,
			memberid,
			emailname,
			emailmailid,
			emailtype,
			emailcreated
			)
			VALUES(
			"' . $this->SanitizeForSQL($this->connection,$esignid) . '",
			"' . $this->SanitizeForSQL($this->connection,$memberid) . '",
			"' . $this->SanitizeForSQL($this->connection,$emailname) . '",
			"' . $this->SanitizeForSQL($this->connection,$emailmailid) . '",
			"' . $this->SanitizeForSQL($this->connection,$emailtype) . '",
			now()
			);
	  ';
	
	if (mysqli_query($this->connection, $qry)) 
	{
		 return true ;
	}
	else
    {
		return false;
    }    
	
}  
 /* function to check if givenE signature Data is valid */

function checkEsignatureMember($mastermemberid,$mastermemberno,$memberSelect,$memberno,$ememberid)
{
	if(!$this->DBLogin())
	{
	  $this->HandleError("Database login failed!");
	  return false;
	}
	
	$qry =  "select *  from members where memberid='{$memberSelect}' AND memberno='{$memberno}' AND membermasterid='{$mastermemberid}' AND membermasterno='{$mastermemberno}'";
	$result = mysqli_query($this->connection, $qry);
	if (!$result || mysqli_num_rows($result) <= 0)
	{
			mysqli_close($this->connection);
			return 0;
	}
	else
	{
		mysqli_close($this->connection);
		return 1;
	}
} 
  
/* Function to get details E signature Data */
function getEsignatureData($ememberid, $memberid, $memberno,$mastermemberid,$mastermemberno)
{
	if(!$this->DBLogin())
	{
	  $this->HandleError("Database login failed!");
	  return false;
	}
	$qry ="SELECT * FROM `esignaturesetup` WHERE memberid='{$ememberid}' AND esignmemberid='{$memberid}' AND esignmemberno='{$memberno}' AND esignmastermemberid='{$mastermemberid}' AND esignmastermemberno='{$mastermemberno}'";
	 
	$result = mysqli_query($this->connection, $qry);

	if (!$result || mysqli_num_rows($result) <= 0)
		{
			mysqli_close($this->connection);
			return false;
		} 
		else
		
		{	$ar =[];
			while ($row = mysqli_fetch_assoc($result))
			{
				array_push($ar, $row);
			}
			mysqli_close($this->connection);
			return $ar;
		}
} 
  
 /* Function to get email list */
 
	function getElist($esignID)
	{
		if(!$this->DBLogin())
		{
		  $this->HandleError("Database login failed!");
		  return false;
		}
		$qry ="SELECT * FROM `emaillist` WHERE memberid={$esignID}";
		
		$result = mysqli_query($this->connection, $qry);

		if (!$result || mysqli_num_rows($result) <= 0)
			{
				mysqli_close($this->connection);
				return false;
			} 
			else
			
			{	$ar =[];
				while ($row = mysqli_fetch_assoc($result))
				{
					array_push($ar, $row);
				}
				mysqli_close($this->connection);
				return $ar;
			}
	} 
  
/* Delete Signature Profile */  
function  deleteEsignProfile($memberno,$esignid,$memberid)
{
	if(!$this->DBLogin())
	{
	  $this->HandleError("Database login failed!");
	  return false;
	}
	$esign = $this->deleteEsign($esignid);
	$esignRecord = $this->deleteEsignatureRec($esignid);
	if($esign=='File was sucessfully Deleted' && $esignRecord=='File was sucessfully Deleted')
	{
		mysqli_close($this->connection);
		return 'sucessdelete';
	}
	else 
	{
		mysqli_close($this->connection);
		return 'errordelete';
	}
}
        
/*
* Author :Sarfaraz 
* Date : 26 May 2017 
* Used in: ajax-esign.php
* Description :   
* function to delete the email list of
* selected members
*/        
function deleteEsign($esignID)
    {
        $qry = "DELETE FROM emaillist WHERE memberid ='" . $esignID . "'";
		$result = mysqli_query($this->connection, $qry);
		/*checking if delted */
		if(mysqli_affected_rows($this->connection) > 0)
		{
			  
			  return'File was sucessfully Deleted';
		}
		else
		{	
			 
			return 'Error in Deleting';
		}
		
	
    }       
/*
* Author :Sarfaraz 
* Date : 27 May 2017 
* Used in: ajax-esign.php
* Description :   
* function to delete the esign record
* from esignaturesetup table from selected members
*/        
function deleteEsignatureRec($esignID)
{
 
	$qry = "DELETE FROM esignaturesetup WHERE memberid ='" . $esignID . "'";

	$result = mysqli_query($this->connection, $qry);
	/*checking if delted */
	if(mysqli_affected_rows($this->connection) > 0)
    {
		//mysqli_close($this->connection);
		return'File was sucessfully Deleted';
    }
	else
	{	
		//mysqli_close($this->connection);
		return 'Error in Deleting';
	}
}






/* ----------------  package-setup.php ------------------ */
/* 
===========================================================================
===============Pradeep code starts here  ================
===========================================================================
*/
/* Author :Pradeep 
 * Date : 16 May 2017 
 * Used in: ajax-package-setup.php
 * Description :  
 * This function is created for inserting package 
 * in package table to the specific  members*/   
	
	
	function Insertpackagedata($packagename,$packagetype,$mastermemberid,$mastermemberno,$user)
	{
		if(!$this->DBLogin())
        {	
            $this->HandleError("Database login failed!");
            return false;
        }
		
		$packagestatus=0;
		 
		$check  = "SELECT * FROM package WHERE pkgname = '{$packagename}' AND pkgmastermemberid ='{$mastermemberid}' AND pkgmastermemberno = '{$mastermemberno}'";
		 
		$checkResult =  mysqli_query($this->connection, $check);
		/* If defaultData Contains no entries for this member, insert all the records */
		if(!$checkResult || mysqli_num_rows($checkResult) <= 0)
		{
			$qry  = "INSERT INTO package 
			(
				pkgmastermemberid,
				pkgmastermemberno,
				pkgtype,
				pkgname,
				pkgstatus,
				pkgcreatedby
				
			)
			 VALUES
                  (
					'".$mastermemberid."',
                    '".$mastermemberno."',
                    '".$packagetype."',
                    '".$packagename."',
                    ".$packagestatus.",
                    '".$user."'
				  )";
			$result = mysqli_query($this->connection, $qry);
			if($result){
				
				return('created');
			}
			else
			{
				return('not created');
			}
		}else{
			return('not created');
		}
	
	}

	
	
	/* Author :Pradeep 
 * Date : 17 May 2017 
 * Used in: package-setup.php
 * Description :  
 * This function is select the packages 
 * from package table of specific  members*/   
	
	
	function Getpackage($mastermemberid, $mastermemberno)
	{
		if(!$this->DBLogin())
        {	
            $this->HandleError("Database login failed!");
            return false;
        }
		
		 
		$check  = "SELECT * FROM package WHERE pkgmastermemberid ='{$mastermemberid}' AND pkgmastermemberno = '{$mastermemberno}'";
		$checkResult =  mysqli_query($this->connection, $check);
		$ar=[];
		/* If defaultData Contains no entries for this member, insert all the records */
		if (!$checkResult || mysqli_num_rows($checkResult) <= 0)
        {
            mysqli_close($this->connection);
            return false;
        } 
		else
		{
			while ($row = mysqli_fetch_assoc($checkResult))
			{
				array_push($ar, $row);
			}
			return $ar;
		}
	
	}
	
/* Author :Pradeep 
 * Date : 18 May 2017 
 * Used in: ajax-package-setup.php
 * Description :  
 * This function is insert the selected & required
   documents to selected package of specific  members*/   
	
	
	function documentinsert($packageid,$docid,$mastermemberid,$mastermemberno,$user,$select,$required )
	{
		if(!$this->DBLogin())
        {	
            $this->HandleError("Database login failed!");
            return false;
        }
		
		$check  = "SELECT * FROM  packagedetails WHERE pkgdetailmastermemberid ='{$mastermemberid}' AND pkgdetailmastermemberno = '{$mastermemberno}'  AND pkgdetailmasterid = '{$packageid}'  AND pkgdetaildocid = '{$docid}'";
		
		$checkResult =  mysqli_query($this->connection, $check);
		$ar=[];
		/* If defaultData Contains no entries for this member, insert all the records */
		if (!$checkResult || mysqli_num_rows($checkResult) <= 0)
        {
			if($select==1 || $required==1)
			{
				$qry  = "INSERT INTO packagedetails 
				(
					pkgdetailmasterid,
					pkgdetaildocid,
					pkgdetaildocselect,
					pkgdetaildocreq,
					pkgdetailmastermemberid,
					pkgdetailmastermemberno,
					pkgdetailcreatedby
					
				)
				 VALUES
					  (
						".$packageid.",
						".$docid.",
						".$select.",
						".$required.",
						'".$mastermemberid."',
						'".$mastermemberno."',
						'".$user.",'
					  )";
					  $result = mysqli_query($this->connection, $qry);
					  if( $result)
					  {
						  return true;
					  }else{
						  return false;
					  }
			}
				
        } 
		else
		{
			$update_query = "update packagedetails set
		               pkgdetaildocselect = ".$select.",
		               pkgdetaildocreq =".$required.",
		               pkgdetailmodifiedby = '".$user."'						
				       where
				       pkgdetailmasterid = " . $packageid . " and  pkgdetaildocid = ".$docid." and  pkgdetailmastermemberid = '".$mastermemberid."' and  pkgdetailmastermemberno ='".$mastermemberno."'" ;
					   $result = mysqli_query($this->connection, $update_query );
					   if( $result)
					  {
						  return true;
					  }else{
						  return false;
					  }
		}
	
	}
	

	
	/* Author :Pradeep 
 * Date : 17 May 2017 
 * Used in: ajax-package-setup.php
 * Description :  
 * This function is select the document details
   from documentdetails table of specific  members and specific packageid*/   
	
	
	function retrievedocumentdetails($mastermemberid, $mastermemberno,$package)
	{
		if(!$this->DBLogin())
        {	
            $this->HandleError("Database login failed!");
            return false;
        }
		
		 
		$check  = "SELECT * FROM packagedetails WHERE pkgdetailmastermemberid ='{$mastermemberid}' AND pkgdetailmastermemberno = '{$mastermemberno}' and  pkgdetailmasterid ='".$package."'";
		 
		$checkResult =  mysqli_query($this->connection, $check);
		$ar=[];
		/* If defaultData Contains no entries for this member, insert all the records */
		if (!$checkResult || mysqli_num_rows($checkResult) <= 0)
        {
            mysqli_close($this->connection);
            return false;
        } 
		else
		{
			while ($row = mysqli_fetch_assoc($checkResult))
			{
				array_push($ar, $row);
			}
			return $ar;
		}
	
	}
	
	
	/* Author :Pradeep 
 * Date : 18 May 2017 
 * Used in: ajax-package-setup.php
 * Description :  
 * This function is insert the selected & required
   documents to selected package of specific  members*/   
	
	
	function assignpackagetomember($packageid,$mastermemberid,$mastermemberno,$memberid,$memberno,$user,$assign)
	{
		if(!$this->DBLogin())
        {	
            $this->HandleError("Database login failed!");
            return false;
        }
		
		 
		$check  = "SELECT * FROM  packageassign WHERE pkgassignmastermemberid ='{$mastermemberid}' AND pkgassignmastermemberno = '{$mastermemberno}'  AND pkgassignmasterid = '{$packageid}'  AND 	pkgassignmemberid = '{$memberid}' AND 	pkgassignmemberno = '{$memberno}'";
		
		$checkResult =  mysqli_query($this->connection, $check);
		$ar=[];
		/* If defaultData Contains no entries for this member, insert all the records */
		if (!$checkResult || mysqli_num_rows($checkResult) <= 0)
        {
			if($assign==1)
			{
				$qry  = "INSERT INTO packageassign 
				(
					pkgassignmasterid,
					pkgassignmastermemberid,
					pkgassignmastermemberno,
					pkgassignmemberid,
					pkgassignmemberno,
					pkgassigncreatedby
					
				)
				 VALUES
					  (
						 ".$packageid.",
						'".$mastermemberid."',
						'".$mastermemberno."',
						'".$memberid."',
						'".$memberno."',
						'".$user.",'
					  )";
					  $result = mysqli_query($this->connection, $qry);
					  if( $result)
					  {
						  return true;
					  }else{
						  return false;
					  }
			}
				
        }
		else if($assign==0)
		{
			$check  = "delete  FROM  packageassign WHERE pkgassignmastermemberid ='{$mastermemberid}' AND pkgassignmastermemberno = '{$mastermemberno}'  AND pkgassignmasterid = '{$packageid}'  AND 	pkgassignmemberid = '{$memberid}' AND 	pkgassignmemberno = '{$memberno}'";
			$result = mysqli_query($this->connection, $check);
					 
					  if( $result)
					  {
						  return true;
					  }else{
						  return false;
					  }
		}
		else
		{
			$update_query = "update packageassign set
		               pkgassignmasterid = ".$packageid."
		               pkgassignlastmodified = '".$user."'						
				       where
				       pkgassignmemberid = " . $memberid . " and  pkgassignmemberno = ".$memberno." and  pkgassignmastermemberid = '".$mastermemberid."' and  pkgassignmastermemberno ='".$mastermemberno."'" ;
					   $result = mysqli_query($this->connection, $update_query );
					   if( $result)
					  {
						  return true;
					  }else{
						  return false;
					  }
		}
	
	}
	
	
		/* Author :Pradeep 
 * Date : 17 May 2017 
 * Used in: ajax-package-setup.php
 * Description :  
 * This function is select the assign package details
   from packageassign table of specific  mastermembers and specific packageid*/   
	
	
	function retrieveassignpackagedetails($mastermemberid,$mastermemberno,$package)
	{
		if(!$this->DBLogin())
        {	
            $this->HandleError("Database login failed!");
            return false;
        }
		
		 
		$check  = "SELECT * FROM  packageassign WHERE pkgassignmastermemberid ='{$mastermemberid}' AND pkgassignmastermemberno = '{$mastermemberno}' and  pkgassignmasterid ='".$package."'";
		
		$checkResult =  mysqli_query($this->connection, $check);
		$ar=[];
		/* If defaultData Contains no entries for this member, insert all the records */
		if (!$checkResult || mysqli_num_rows($checkResult) <= 0)
        {
            mysqli_close($this->connection);
            return false;
        } 
		else
		{
			while ($row = mysqli_fetch_assoc($checkResult))
			{
				array_push($ar, $row);
			}
			return $ar;
		}
	
	}
	
/* Author :Pradeep 
 * Date : 19 May 2017 
 * Used in: package-setup.php
 * Description :  
 * This function is to delete package from  package table and packageassign table 
   of selected mastermember*/		
		/* Deleteing a  package */  
	
	
	function deletememberpackage($package,$mastermemberid,$mastermemberno)
	{
		if(!$this->DBLogin())
        {	
            $this->HandleError("Database login failed!");
            return false;
        }
		
		 
		$check  = "delete  FROM   package WHERE pkgmastermemberid ='{$mastermemberid}' AND pkgmastermemberno = '{$mastermemberno}' and  pkgid ='".$package."'";
		
		$checkResult =  mysqli_query($this->connection, $check);
		/* If defaultData Contains no entries for this member, insert all the records */
		if ($checkResult )
        {
			$check1="delete  FROM  packageassign WHERE pkgassignmastermemberid ='{$mastermemberid}' AND pkgassignmastermemberno = '{$mastermemberno}' and  pkgassignmasterid ='".$package."'";
			$checkResult1 =  mysqli_query($this->connection, $check);
			if ($checkResult1)
				{
					return true;
				}else{
					return false;
				}
        } 
		else
		{
			return false;
		}
	
	}
	
	
	
/* 
   ===========================================================================
                 Default Reorder Setup  Page  starts
   ===========================================================================
	*/		
	


/* Author :Pradeep 
 * Date : 24 July 2017 
 * Used in:ajax-reorder.php
 * Description :  
 * This function is to display
	default lables*/
	
	function orderSelectAll($membertemplate,$mastermemberid,$mastermemberno,$current_property)
	{
		
  		if(!$this->DBLogin())
        {	
            $this->HandleError("Database login failed!");
            return false;
        }
		
		if(1)
		{	
				
			$qry = " 
				SELECT 'APPLIANCES'  table_name, A1.*
				FROM applianceinforp as A1 WHERE apprptempid = ".$membertemplate." AND apprpselect = 1  AND 	apprpsubcategory != 'system' 
				
				UNION 
				
				SELECT 'FINANCIALS'  table_name, F1.*			
				FROM financialinforp   as F1 WHERE finrptempid = ".$membertemplate." AND finrpselect = 1 AND 	finrpsubcategory != 'system' 
			   
				UNION 
				
				SELECT 'LEASETERMS'  table_name,L1.*	
				FROM leasetermrp  as L1	WHERE lstrmrptempid = ".$membertemplate." AND lstrmrpselect = 1 AND 	lstrmrpsubcategory != 'system' 
				
				UNION 
				
				SELECT  'LIMITS'      table_name, L2.*
				FROM limitinforp as L2 WHERE limitrptempid = ".$membertemplate." AND limitrpselect=1 AND 	limitrpsubcategory != 'system' 
			   
				UNION 
				
				SELECT 'MISC' table_name,M1.* 	
				FROM miscinforp as M1 WHERE miscrptempid = ".$membertemplate." AND miscrpselect=1 AND 	miscrpsubcategory != 'system' 
				
				UNION 
				
				SELECT 'PETS' table_name, p1.*
				FROM petinforp as P1 WHERE petrptempid = ".$membertemplate." AND  petrpselect = 1 AND 	petrpsubcategory != 'system' 
				 
				UNION 

				SELECT 'PROPERTY' table_name, P2.* 
				FROM propertyinforp as P2 WHERE proprptempid = ".$membertemplate." AND  proprpselect = 1 AND 	proprpsubcategory != 'system' 
				
			   
				UNION 
				
				SELECT 'RESIDENT' table_name, R1.* 
				FROM residentinforp as R1 WHERE resrptempid = ".$membertemplate." AND  resrpselect = 1 AND 	resrpsubcategory != 'system' 
				
				UNION 
				
				SELECT 'UNITADDRES'  table_name, U1.*
				FROM unitaddressrp as U1 WHERE unitrptempid = ".$membertemplate." AND  unitrpselect = 1 AND 	unitrpsubcategory != 'system' 
				
				UNION 
				
				SELECT  'UTILITIES'  table_name,U2.*
				FROM utilityinforp as U2 WHERE utilityrptempid = ".$membertemplate."  AND  utilityrpselect = 1 AND 	utilityrpsubcategory != 'system' 
			  
				UNION 
				SELECT 'VEHICLEPAR'  table_name,V1.*
				FROM vehicleparkrp as V1 WHERE vehrptempid = ".$membertemplate." AND  vehrpselect = 1 AND 	vehrpsubcategory != 'system' 
				
				ORDER BY 	apprporder ASC 
				";
				
				$result = mysqli_query($this->connection, $qry);
			
				if(!$result || mysqli_num_rows($result) <= 0)
				{
				  error_log("Error in feteching the records from the repositories or no Matched Records found");	
				  mysqli_close($this->connection);
				  return false;
				}
				else
				{
					$ar2= [];
					while($row = mysqli_fetch_assoc($result))
					{
						array_push($ar2, $row);  
					}
					mysqli_close($this->connection);
					$resultset = [];
					 
					foreach($ar2 as $temp_row) 
					{
						if($temp_row['table_name'] == "APPLIANCES")
						{
						   $resultset['APPLIANCES'][] =  $temp_row; 
						}
						 elseif($temp_row['table_name'] == "FINANCIALS")
						{
							$resultset['FINANCIALS'][] =$temp_row;
							 
						}
						 
						elseif($temp_row['table_name'] == "LEASETERMS")
						{
							$resultset['LEASETERMS'][] =$temp_row;
						}
						elseif($temp_row['table_name'] == "LIMITS")
						{
							$resultset['LIMITS'][] =$temp_row;
						}
						elseif($temp_row['table_name'] == "MISC")
						{
							$resultset['MISC'][] =$temp_row;
						}
						elseif($temp_row['table_name'] == "PETS")
						{
							$resultset['PETS'][] =$temp_row;
						}
						elseif($temp_row['table_name'] == "PROPERTY")
						{
							$resultset['PROPERTY'][] =$temp_row;
						}
						elseif($temp_row['table_name'] == "RESIDENT")
						{
							$resultset['RESIDENT'][] = $temp_row;
						}
						elseif($temp_row['table_name'] == "UNITADDRES")
						{
							$resultset['UNITADDRES'][] = $temp_row;
						}
						elseif($temp_row['table_name'] == "UTILITIES")
						{
							$resultset['UTILITIES'][] = $temp_row;
						}
						elseif($temp_row['table_name'] == "VEHICLEPAR")
						{
							$resultset['VEHICLEPAR'][] = $temp_row;
						} 	
						else
						{
							
						}
					}
					return $resultset;
				}
		}
		else
		{			/* 
					 'defaultcategory'  table_name,
					 'defaultfeedid'	apprpfeedid,
					 'defaulttempid'    apprptempid, 
					 'defaultedit'		apprpselect, 
					 'defaultfieldname' apprpfieldname, 
					 'defaultoptlabel'  apprpoptlabel, 
					 'defaultfieldtype' apprpfieldtype, 
					 'defaultcreated' apprpcreated, 
					 'defaultmodifiedby' apprpmodifiedby, 
				   */
					
					$qry ="SELECT  defaultcategory  as  'table_name', defaultfeedid	   as  'apprpfeedid',defaultfieldid	   as  'apprpfieldid', defaultsubcategory	as  'apprpsubcategory',  defaulttempid as  'apprptempid', defaultedit		   as  'apprpselect', defaultfieldname    as  'apprpfieldname', defaultoptlabel     as  'apprpoptlabel',   defaultfieldtype    as  'apprpfieldtype', defaultmodifiedby   as  'apprpmodifiedby', A.*  FROM defaultdata A WHERE  defaultmemberno='".$memberno."' AND defaultmemberid = '{$memberid}' AND  	defaultmembermasterno = '{$mastermemberno}' AND  defaultmembermasterid='".$mastermemberid."' 
					 AND defaulttempid = {$membertemplate} AND defaultpropertyid={$selected_property} ORDER BY 	apprpfeedid ASC";
					
		            
					$checkResult = mysqli_query($this->connection, $qry);
					$ar2= [];
					while($row = mysqli_fetch_assoc($checkResult))
					{
						array_push($ar2, $row);  
					}
					mysqli_close($this->connection);
					$resultset = [];
					 
					foreach($ar2 as $temp_row) 
					{
						if($temp_row['table_name'] == "APPLIANCES")
						{
						   $resultset['APPLIANCES'][] =  $temp_row; 
						}
						 elseif($temp_row['table_name'] == "FINANCIALS")
						{
							$resultset['FINANCIALS'][] =$temp_row;
							 
						}
						 
						elseif($temp_row['table_name'] == "LEASETERMS")
						{
							$resultset['LEASETERMS'][] =$temp_row;
						}
						elseif($temp_row['table_name'] == "LIMITS")
						{
							$resultset['LIMITS'][] =$temp_row;
						}
						elseif($temp_row['table_name'] == "MISC")
						{
							$resultset['MISC'][] =$temp_row;
						}
						elseif($temp_row['table_name'] == "PETS")
						{
							$resultset['PETS'][] =$temp_row;
						}
						elseif($temp_row['table_name'] == "PROPERTY")
						{
							$resultset['PROPERTY'][] =$temp_row;
						}
						elseif($temp_row['table_name'] == "RESIDENT")
						{
							$resultset['RESIDENT'][] = $temp_row;
						}
						elseif($temp_row['table_name'] == "UNITADDRES")
						{
							$resultset['UNITADDRES'][] = $temp_row;
						}
						elseif($temp_row['table_name'] == "UTILITIES")
						{
							$resultset['UTILITIES'][] = $temp_row;
						}
						elseif($temp_row['table_name'] == "VEHICLEPAR")
						{
							$resultset['VEHICLEPAR'][] = $temp_row;
						} 	
						else
						{
							
						}
					}
					return $resultset;
			}
			
			
			
			
	}
	
	/* Author :Pradeep 
 * Date : 24 July 2017 
 * Used in:ajax-reorder.php
 * Description :  
 * This function is to update
	order number lables*/
	function updateordernumber($mastermemberid,$mastermemberno,$prefix,$tablename,$orderid,$feedid,$ordernumber,$temporderid)
	{
		if(!$this->DBLogin())
        {
            $this->HandleError("Database login failed!");
            return false;
        }   
		
		$orderFeedidLen = sizeof($feedid);
		$orderIdLen = sizeof($orderid);
		
		if(($orderFeedidLen > 0) && ($orderIdLen >0))
		{
			if($orderFeedidLen  == $orderIdLen)
			{
				for($i=0;$i<$orderFeedidLen;$i++)
				{
					$check = "select * from {$tablename} where {$prefix}id = {$orderid[$i]} AND  {$prefix}tempid = {$temporderid} AND {$prefix}feedid = {$feedid[$i]} ";
					
					$checkResult = mysqli_query($this->connection, $check);
			
					if(!$checkResult || mysqli_num_rows($checkResult) <= 0)
					{
					  error_log("NoValidRecordFound");	
					  mysqli_close($this->connection);
					  return "NotExist";
					}
					else
					{
						$updateQry = "UPDATE  {$tablename} set  {$prefix}order = {$ordernumber[$i]} where {$prefix}id = {$orderid[$i]} AND  {$prefix}tempid = {$temporderid} AND {$prefix}feedid = {$feedid[$i]}";
						
						$updateResult = mysqli_query($this->connection, $updateQry);
						
						/* if((mysqli_affected_rows($this->connection)) <= 0)
						{
							 
							error_log("orderIdUpdatationFailed");	
							mysqli_close($this->connection);
							return false;
						} */
						 
						
					}	
				}	
			}
			else
			{
				mysqli_close($this->connection);
				return false;
			}		
		}
		else
		{
			mysqli_close($this->connection);
			return false;
		}
		
	}
	/* 
   ===========================================================================
                 Default Reorder Setup  Page  ends
   ===========================================================================
	*/		
		
	
	/* 
   ===========================================================================
                 Default Repository Setup  Page  starts
   ===========================================================================
	*/	
	
	/* Developer :Pradeep 
 * Date : 24 July 2017 
 * Used in:ajax-defaultrp.php
 * Description :  
 * This function is to add
	New field to defaultrp table*/
	function insertdefaultrpdata(
		$selectnewCat,		
		$selectSubCatField,	
		$fieldType,			
		$selectFieldName,	
		$selectFieldId,
		$optionalLable,
		$xmlBlockTag,		
		$xmlFieldTag,		
		$dropdownTtype,
		$select,
		$selectedTemp,
		$targetTable,
		$tablePre,
		$mastermemberid,
		$mastermemberno
	)
	{
		if(!$this->DBLogin())
        {
            $this->HandleError("Database login failed!");
            return false;
        } 
			
		$qry='INSERT INTO defaultrp(
			defrpcategory,
			defrpsubcategory,
			defrpselect,
			defrpfieldname,
			defrpoptlabel,
			defrpfieldtype,
			defrpfieldid,
			defrpdropdown,
			
			defrpdefaultreq,
			defrpreq,
			defrphide,
			defrpedit,
			defrporder,
			
			defrpxmlmastertag,
			defrpxmlblocktag,
			defrpxmlchildtag
			)
			VALUES(
			"' .  $this->SanitizeForSQL($this->connection,$selectnewCat) . '",
			"' . $this->SanitizeForSQL($this->connection,$selectSubCatField) . '",
			' .  $this->SanitizeForSQL($this->connection,$select) . ',
			"' . $this->SanitizeForSQL($this->connection,$selectFieldName) . '",
			"' . $this->SanitizeForSQL($this->connection,$optionalLable) . '",
			"' . $this->SanitizeForSQL($this->connection,$fieldType) . '",
			"' . $this->SanitizeForSQL($this->connection,$selectFieldId) . '",
			"' . $this->SanitizeForSQL($this->connection,$dropdownTtype) . '",
			0,
			0,
			0,
			0,
			0,
			"' . $this->SanitizeForSQL($this->connection,$xmlBlockTag) . '",
			"",
			"' . $this->SanitizeForSQL($this->connection,$xmlFieldTag) . '"
			);
	  ';
	  
		$result = mysqli_query($this->connection, $qry);
		
		if($result)
		  {	 	$lastInsertedID = mysqli_insert_id($this->connection);	
				if(!empty($selectedTemp) && $selectedTemp!='')
			 	{	 
					$qryDefaultrp  = "SELECT * FROM defaultrp where defrpfeedid = {$lastInsertedID} AND 	defrpfieldid = '{$selectFieldId}'";	
					 
					$newRecord = mysqli_query($this->connection, $qryDefaultrp);
					$defaultRPrecords = [];
					if($newRecord)
					{
						foreach($newRecord as $newRecordRow)
						{
							array_push($defaultRPrecords, $newRecordRow);	
						}
					}	
					foreach($selectedTemp as $tempID)
					{	
						$qrySelTemplate = "SELECT * from templaterp where temprpuid={$tempID}"; 
						$templateRecords = mysqli_query($this->connection, $qrySelTemplate);
						$templateRow = [];
						if(!empty($templateRecords))
						{
							foreach($templateRecords as $templateRecordRow)
							{
								array_push($templateRow, $templateRecordRow);	
							}
						}	
						if(!empty($defaultRPrecords))
						{ 
							if(!empty($templateRow))
							{
								error_log(print_r($defaultRPrecords,true));
								error_log(print_r($templateRow,true));
								
								$insert = "INSERT INTO {$targetTable}(
								{$tablePre}tempid,
								{$tablePre}feedid, 
								{$tablePre}subcategory,
								{$tablePre}fieldid,
								{$tablePre}dropdown,
								{$tablePre}select,
								{$tablePre}fieldname,
								{$tablePre}optlabel,
								{$tablePre}fieldtype,
								{$tablePre}defaultreq,
								{$tablePre}req,
								{$tablePre}hide,
								{$tablePre}edit,
								{$tablePre}modifiedby,
								{$tablePre}order
								) 
								values
								( {$templateRow[0]['temprpuid']},
								  {$defaultRPrecords[0]['defrpfeedid']}, 
								 '{$defaultRPrecords[0]['defrpsubcategory']}', 
								 '{$defaultRPrecords[0]['defrpfieldid']}',
								 '{$defaultRPrecords[0]['defrpdropdown']}',
								 '{$defaultRPrecords[0]['defrpselect']}',
								 '{$defaultRPrecords[0]['defrpfieldname']}',
								 '{$defaultRPrecords[0]['defrpoptlabel']}',
								 '{$defaultRPrecords[0]['defrpfieldtype']}',
								 {$defaultRPrecords[0]['defrpdefaultreq']},
								 {$defaultRPrecords[0]['defrpreq']},
								 {$defaultRPrecords[0]['defrphide']},
								 {$defaultRPrecords[0]['defrpedit']},
								 'SuperAdmin',
								 0
								)";
								error_log($insert);
								$templateRecords = mysqli_query($this->connection, $insert);
								if($templateRecords)
								{
									error_log("INSERTED TO SELECTED TABLES");
								}	
							}	
						}	
					}
					mysqli_close($this->connection);
					return "inserted";
				}	
				

			 mysqli_close($this->connection);
			  return "inserted";
		  }
		  else
		  {
			  mysqli_close($this->connection);
			  return false;
		  }
	}
	
	
	/* Developer :Pradeep 
 * Date : 24 July 2017 
 * Used in:ajax-defaultrp.php
 * Description :  
 * This function is to select all the fields 
  respect to category*/
	
	function selectedoptionresults($defcategory)
	{
		if(!$this->DBLogin())
        {	
            $this->HandleError("Database login failed!");
            return false;
        }
		
		 
		$check  = "SELECT * FROM  defaultrp WHERE defrpcategory ='".$defcategory."'";
		
		$checkResult =  mysqli_query($this->connection, $check);
		$ar=[];
		/* If defaultData Contains no entries for this member, insert all the records */
		if (!$checkResult || mysqli_num_rows($checkResult) <= 0)
        {
            mysqli_close($this->connection);
            return false;
        } 
		else
		{
			while ($row = mysqli_fetch_assoc($checkResult))
			{
				array_push($ar, $row);
			}
			return $ar;
		}
	
	}
	
	/* Developer :Pradeep 
 * Date : 24 July 2017 
 * Used in:ajax-defaultrp.php
 * Description :  
 * This function is to update the fields 
  respect to category*/
  
  function updatedefaultrpdata($select1,$category1,$field_name1,
  $sub_Category1,$optional_lable1,$field_type1,
  $unique_name1,$default_req1,$require1,$hide1,$editable1,
  $updaterowId)
	{
		if(!$this->DBLogin())
        {	
            $this->HandleError("Database login failed!");
            return false;
        }
		
		 
		$qry = 'update defaultrp set
		           defrpcategory= "' . $this->SanitizeForSQL($this->connection,$category1) . '",
		           defrpsubcategory= "' . $this->SanitizeForSQL($this->connection,$sub_Category1) . '",
		           defrpselect= "' . $this->SanitizeForSQL($this->connection,$select1) . '",
				   defrpfieldname="' . $this->SanitizeForSQL($this->connection,$field_name1) . '",
				   defrpoptlabel= "' . $this->SanitizeForSQL($this->connection,$optional_lable1) . '",
				   defrpfieldtype= "' . $this->SanitizeForSQL($this->connection,$field_type1) . '",
				   defrpfieldid= "' . $this->SanitizeForSQL($this->connection,$unique_name1) . '",
				   defrpdefaultreq= "' . $this->SanitizeForSQL($this->connection,$default_req1) . '",
				   defrpreq= "' . $this->SanitizeForSQL($this->connection,$require1) . '",
				   defrphide= "' . $this->SanitizeForSQL($this->connection,$hide1) . '",
				   defrpedit= "' . $this->SanitizeForSQL($this->connection,$editable1) . '"
				   
				   where
				   	defrpfeedid= "' .$updaterowId. '" ' ;
		
		$checkResult =  mysqli_query($this->connection, $qry);
		/* If defaultData Contains no entries for this member, insert all the records */
		if (!$checkResult)
        {
            mysqli_close($this->connection);
            return false;
        } 
		else
		{
			mysqli_close($this->connection);
            return "done";
		}
	
	}
  
  
  
	 	/* Developer :Pradeep 
 * Date : 24 July 2017 
 * Used in:ajax-defaultrp.php
 * Description :  
 * This function is to delete therow in 
 defaultrp*/
	 
	 function deleterow($deleterpId)
	{
		if(!$this->DBLogin())
        {	
            $this->HandleError("Database login failed!");
            return false;
        }
		
		 
		$check  = "delete  FROM   defaultrp WHERE defrpfeedid ='".$deleterpId."'";
		$checkResult1 =  mysqli_query($this->connection, $check);
			if ($checkResult1)
				{
					return 'deleted';
				}else{
					return false;
				}
        
	
	}
	/* 
     Get All CATEGORY
	*/
	function getDefaultCategories()
	{
		if(!$this->DBLogin())
        {
            $this->HandleError("Database login failed!");
            return false;
        }
		$qry = "SELECT DISTINCT(defrpcategory) FROM defaultrp defaultrp ORDER BY defrpcategory ASC";
		$result =  mysqli_query($this->connection, $qry);
		$ar = [];
		if (!$result || mysqli_num_rows($result) <= 0)
        {
            mysqli_close($this->connection);
            return false;
        } 
		else
		{
			while ($row = mysqli_fetch_assoc($result))
			{
				array_push($ar, $row);
			}
			return $ar;
		}
	}	
	
	
	/* 
     Get All SUBCATEGORY
	*/
	function getDefaultSubCategories($category)
	{
		if(!$this->DBLogin())
        {
            $this->HandleError("Database login failed!");
            return false;
        }
		$qry = "SELECT DISTINCT(defrpsubcategory) FROM defaultrp WHERE defrpcategory='{$category}' ORDER BY defrpsubcategory ASC";
		$result =  mysqli_query($this->connection, $qry);
		$ar = [];
		if (!$result || mysqli_num_rows($result) <= 0)
        {
            mysqli_close($this->connection);
            return false;
        } 
		else
		{
			while ($row = mysqli_fetch_assoc($result))
			{
				array_push($ar, $row);
			}
			return $ar;
		}
	}

/* 
     Get All Default FieldType
	*/
	function getDefaultFieldType()
	{
		if(!$this->DBLogin())
        {
            $this->HandleError("Database login failed!");
            return false;
        }
		$qry = "SELECT DISTINCT(defrpfieldtype) FROM defaultrp ORDER BY defrpfieldtype ASC";
		$result =  mysqli_query($this->connection, $qry);
		$ar = [];
		if (!$result || mysqli_num_rows($result) <= 0)
        {
            mysqli_close($this->connection);
            return false;
        } 
		else
		{
			while ($row = mysqli_fetch_assoc($result))
			{
				array_push($ar, $row);
			}
			return $ar;
		}
	}	

	/* 
     Get All Default XML BLOCK TAG NAMES
	*/
	function getDefaultxmlMasterTag($category)
	{
		if(!$this->DBLogin())
        {
            $this->HandleError("Database login failed!");
            return false;
        }
		$qry = "SELECT DISTINCT(defrpxmlmastertag) FROM defaultrp where defrpcategory='{$category}'  ORDER BY 	defrpxmlmastertag	 ASC";
		$result =  mysqli_query($this->connection, $qry);
		$ar = [];
		if (!$result || mysqli_num_rows($result) <= 0)
        {
            mysqli_close($this->connection);
            return false;
        } 
		else
		{
			while ($row = mysqli_fetch_assoc($result))
			{
				array_push($ar, $row);
			}
			return $ar;
		}
	}	

	/* 
     Get All Default DEFRPFIELD ID 
	*/
	function getDefaultfieldId()
	{
		if(!$this->DBLogin())
        {
            $this->HandleError("Database login failed!");
            return false;
        }
		$qry = "SELECT DISTINCT(defrpfieldid) FROM defaultrp ORDER BY 	defrpfieldid	 ASC";
		$result =  mysqli_query($this->connection, $qry);
		$ar = [];
		if (!$result || mysqli_num_rows($result) <= 0)
        {
            mysqli_close($this->connection);
            return false;
        } 
		else
		{
			while ($row = mysqli_fetch_assoc($result))
			{
				array_push($ar, $row);
			}
			return $ar;
		}
	}	
	/* 
     Get All Dropdown Names
	*/
	function getDefaultdropDownNames()
	{
		if(!$this->DBLogin())
        {
            $this->HandleError("Database login failed!");
            return false;
        }
		$qry = "SELECT DISTINCT(dropdownrpname) FROM dropdownrp ORDER BY dropdownrpname	 ASC";
		$result =  mysqli_query($this->connection, $qry);
		$ar = [];
		if (!$result || mysqli_num_rows($result) <= 0)
        {
            mysqli_close($this->connection);
            return false;
        } 
		else
		{
			while ($row = mysqli_fetch_assoc($result))
			{
				array_push($ar, $row);
			}
			return $ar;
		}
	}	

	/* 
     New Field Name Validation
	*/
	function getDefaultFIDValidity($fieldID)
	{
		if(!$this->DBLogin())
        {
            $this->HandleError("Database login failed!");
            return false;
        }
		$qry = "SELECT 	defrpfieldid FROM defaultrp WHERE 	defrpfieldid = '{$fieldID}' ORDER BY 	defrpfieldid	 ASC";
		
		error_log($qry);
		$result =  mysqli_query($this->connection, $qry);
		if (mysqli_num_rows($result) > 0) 
		{
			 
			mysqli_close($this->connection);
			return "exists";
		} 
			else
		{
			mysqli_close($this->connection);
			return "notexists";
		}
	}


	/* 
     Get  Record based on feed ID  from Default Repository  
	*/
	function getDefaultRecord($feedId)
	{
		if(!$this->DBLogin())
        {
            $this->HandleError("Database login failed!");
            return false;
        }
		$qry = "SELECT * FROM defaultrp WHERE defrpfeedid = '{$feedId}'";
		$result =  mysqli_query($this->connection, $qry);
		if (mysqli_num_rows($result) > 0) 
		{
			$ar = []; 
			while ($row = mysqli_fetch_assoc($result))
			{
				array_push($ar, $row);
			}
			mysqli_close($this->connection);
			return $ar;
		}
		else
		{
			mysqli_close($this->connection);
			return "unknownFeedID";
		}		
		
	}
	
	/* 
   ===========================================================================
                 Default repository Setup  Page  ends
   ===========================================================================
	*/	
	
	
	
	/* 
   ===========================================================================
   ===============Pradeep code ends here  ================
   ===========================================================================
	*/	
	
	/* Redirect to a specific url */
	function RedirectToURL($url)
    {
        header("Location: $url");
        exit;
    }	
	
	
	
	
	
	

/*
=======================================================
			NEW DEFAULT SETUP PAGE 
				 ---------
=======================================================
*/		
/* function to display  template names */
function assignedTemeplates($mastermemberid,$mastermemberno, $memberID,$memberNumber, $template_level)
{	if(!$this->DBLogin())
	{	
		$this->HandleError("Database login failed!");
		return false;
	}
	$qry = '';
	if($template_level == "member_level"){
		$qry = "select *, date(assignlastmodified) as assignlastmodifieddate from templaterp T RIGHT JOIN assigntemplaterp A ON T.temprpuid = A.assignmemebertemp where assignmastermemberid='".$mastermemberid."' AND 	assignmastermemberno ='".$mastermemberno."' AND assignmemberid='".$memberID."' AND assignmemberno='".$memberNumber."' AND assignmemberproperty= -1 ORDER BY temprpname * 1, temprpname ASC";
	}
	else
	{
		$qry = "select *, date(assignlastmodified) as assignlastmodifieddate from templaterp T RIGHT JOIN assigntemplaterp A ON T.temprpuid = A.assignmemebertemp where assignmastermemberid='".$mastermemberid."' AND 	assignmastermemberno ='".$mastermemberno."' AND assignmemberid='".$memberID."' AND assignmemberno='".$memberNumber."' AND assignmemberproperty=".$template_level." ORDER BY temprpname * 1, temprpname ASC";
	}	
	error_log($qry);
	$result = mysqli_query($this->connection,$qry);
	$ar = [];
	//$row = mysqli_fetch_assoc($result);
	
	$ar= [];
	while($row = mysqli_fetch_assoc($result))
	{
		array_push($ar, $row);  
	}
	mysqli_close($this->connection);
	return $ar;
}	

	
/* function to display  UnAssigned Template  names */
function notAssignedTemeplates($mastermemberid,$mastermemberno, $memberID,$memberNumber, $template_level)
{	if(!$this->DBLogin())
	{	
		$this->HandleError("Database login failed!");
		return false;
	}
	$qry = '';
	if($template_level == "member_level"){
		$qry = "select *, date(temprpcreated) as temprpcreatedDate from templaterp T WHERE T.temprpuid NOT IN (SELECT assignmemebertemp from assigntemplaterp where assignmastermemberid='".$mastermemberid."' AND 	assignmastermemberno ='".$mastermemberno."' AND assignmemberid='".$memberID."' AND assignmemberno='".$memberNumber."' AND assignmemberproperty= -1) AND tempmemberno ='".$mastermemberno."' AND temprpmemberid='".$mastermemberid."' ORDER BY temprpname * 1, temprpname ASC";
	}
	else
	{
		$qry = "select *, date(temprpcreated) as temprpcreatedDate from templaterp T WHERE T.temprpuid NOT IN (SELECT assignmemebertemp from assigntemplaterp where assignmastermemberid='".$mastermemberid."' AND 	assignmastermemberno ='".$mastermemberno."' AND assignmemberid='".$memberID."' AND assignmemberno='".$memberNumber."' AND assignmemberproperty= ".$template_level.") AND tempmemberno ='".$mastermemberno."' AND temprpmemberid='".$mastermemberid."' ORDER BY temprpname * 1, temprpname ASC";
	}	
	error_log($qry);
	$result = mysqli_query($this->connection,$qry);
	$ar = [];
	//$row = mysqli_fetch_assoc($result);
	
	$ar= [];
	while($row = mysqli_fetch_assoc($result))
	{
		array_push($ar, $row);  
	}
	mysqli_close($this->connection);
	return $ar;
}	

	
	// Function to show selected twmplate
	function selectedTemplate($templateID,$mastermemberid,$mastermemberno)
	{
		if(!$this->DBLogin())
        {
            $this->HandleError("Database login failed!");
            return false;
        }
		
		$qry ="select * from templaterp where 	temprpmemberid = '{$mastermemberid}' AND tempmemberno='{$mastermemberno}' AND temprpuid =  {$templateID}";
		 
		$templateNames = mysqli_query($this->connection, $qry);
		if(!$templateNames || mysqli_num_rows($templateNames) <= 0)
		{
			mysqli_close($this->connection);
		    return "NoRecordError";
		}
		else
		{
			$templateList= [];
			 
			while($row = mysqli_fetch_assoc($templateNames))
			{
				array_push($templateList, $row);  
				
			}
			mysqli_close($this->connection);
			return $templateList;
		}	
		
	}
	
    // Assign the template to the selected member
	function updateNewTemplateAssign($mastermemberid,$mastermemberno,$memberid,$memberno,$membername,$membertemplate,$assignactive,$memberproperty)
	{
		if(!$this->DBLogin())
        {	
            $this->HandleError("Database login failed!");
            return false;
        }
		 
		 
		$check ="SELECT * FROM assigntemplaterp WHERE assignmastermemberid='".$mastermemberid."' AND assignmastermemberno='".$mastermemberno."' AND assignmemberid ='".$memberid."' AND assignmemberno='".$memberno."' AND  assignmemebertemp =".$membertemplate." AND assignactive='".$assignactive."' AND  assignmemberproperty=".$memberproperty."";
		$check2 = 'Select * from members where membermasterid = "' .$mastermemberid.'" and membermasterno = "' .$mastermemberno. '"and 	memberid="'.$memberid.'" and memberno="'.$memberno.'"';
		//$check3 = 'Select * from templaterp where membermasterid = "' .$mastermemberid.'" and membermasterno = "' .$mastermemberno. '"and 	memberid="'.$memberid.'" and memberno="'.$memberno.'"';
		$qry = "INSERT INTO assigntemplaterp(assignmastermemberid,assignmastermemberno,assignmemberid,assignmemberno,assignmemebertemp,assignactive,assignmemberproperty,assigncreatedby) 
				VALUES('".$mastermemberid."','".$mastermemberno."','".$memberid."','".$memberno."',".$membertemplate.",'".$assignactive."',".$memberproperty.",'".$_SESSION['user']."' )";
		
		 
		$check_result = mysqli_query($this->connection, $check);
		$check_result2 = mysqli_query($this->connection, $check2);
		
		if(!$check_result || mysqli_num_rows($check_result) <= 0)
		{	
			if(!$check_result2 || mysqli_num_rows($check_result2) <= 0)
			{	
				mysqli_close($this->connection);
				error_log("sql injection attack prevention");
				$arr = array ('response'=>'updateError');
				return $arr;
			}
			else
			{
				if (mysqli_query($this->connection, $qry)) 
				{	$lastid = mysqli_insert_id($this->connection);
					/* Delete */
					$check = "SELECT * FROM defaultdata WHERE defaultmemberid = '{$memberid}' AND defaultmemberno ='{$memberno}' AND defaultmembermasterid = '{$mastermemberid}' AND  defaultmembermasterno ='{$mastermemberno}' AND defaultpropertyid={$memberproperty} AND defaulttempid={$membertemplate}";
					$checkResult =  mysqli_query($this->connection, $check);
					/* If defaultData Contains no entries for this member, insert all the records */
					if($checkResult || mysqli_num_rows($checkResult) > 0)
					{	
						$delete = "delete FROM defaultdata WHERE defaultmemberid = '{$memberid}' AND defaultmemberno ='{$memberno}' AND defaultmembermasterid = '{$mastermemberid}' AND  defaultmembermasterno ='{$mastermemberno}' AND defaultpropertyid={$memberproperty} AND defaulttempid={$membertemplate}";	
						$deleteResult =  mysqli_query($this->connection, $delete);
					}
							$qry ="";
							for($counter = 0; $counter<(count($this->loopTablesCat));$counter++)
							{
								$categoryName = $this->loopTablesCat[$counter];
								$tableName = $this->loopTablesNames[$counter];
								$tablePrefix = $this->loopTablesprefix[$counter];
								
								$qry  = $qry."  SELECT '{$categoryName}'  table_name, A1.*
												FROM {$tableName} as A1 WHERE {$tablePrefix}tempid = ".$membertemplate." AND {$tablePrefix}select = 1 AND  {$tablePrefix}defaultreq = 1
												UNION";
							}	
							
							$qry = rtrim($qry," UNION ")." ORDER BY 	apprpfeedid ASC;";
							 error_log($qry);
							$result = mysqli_query($this->connection, $qry);
							
								if(!$result || mysqli_num_rows($result) <= 0)
								{
								  error_log("Error in feteching the records from the repositories or no Matched Records found");	
								  mysqli_close($this->connection);
								  return false;
								}
								else
								{
									$ar2= [];
									$values = '';
									while($row = mysqli_fetch_assoc($result))
									{

										/* -------------------- */
										$values= "{$values}('{$memberid}','{$memberno}','{$membername}','{$mastermemberid}','{$mastermemberno}','{$row['table_name']}',{$row['apprptempid']},{$row['apprpfeedid']},'{$row['apprpsubcategory']}','{$row['apprpfieldid']}','{$row['apprpdropdown']}','{$row['apprpfieldtype']}','{$row['apprpfieldname']}','{$row['apprpoptlabel']}','',{$row['apprpdefaultreq']},{$memberproperty},'{$_SESSION['user']}'),";
									}
									
											
									$values = rtrim($values,',');
									$qry  = "INSERT INTO defaultdata (defaultmemberid,defaultmemberno,defaultmembername,defaultmembermasterid,defaultmembermasterno,defaultcategory,defaulttempid,defaultfeedid,defaultsubcategory,defaultfieldid,defaultdropdown,defaultfieldtype,defaultfieldname,defaultoptlabel,defaultvalue,defaultedit,defaultpropertyid,defaultmodifiedby) VALUES {$values}";
									$defaultDataUpdated = mysqli_query($this->connection, $qry);
									if($defaultDataUpdated){
										
									//	return true;
									}
									else
									{
										//return false;
									}
		
										/* -------------------- */
										
									}
									mysqli_close($this->connection);
									 
									 error_log(print_r($values,true));
									 
									$arr = array ('response'=>'updated','lastId'=>$lastid);
									return $arr ;
				}
				else
			   {
					mysqli_close($this->connection);
					$arr = array ('response'=>'updateError');
					return $arr;
			   }
			}			   
		}
		else if(mysqli_num_rows($check_result) <= 0){
			
			/* Update table records query will be executed here */
			/* Update table records query will be executed here */
		}
		else
		{
			error_log("Problem with the updateTemplateAssign()");
			$arr = array ('response'=>'exists');
			mysqli_close($this->connection);
			return $arr;
		}
	}	
	
	
	
	function showDefaultDataFields($mastermemberid,$mastermemberno,$memberid,$memberno,$tempid)
	{
					if(!$this->DBLogin())
					{	
						$this->HandleError("Database login failed!");
						return false;
					}
					$qry ="SELECT  * FROM defaultdata A WHERE  defaultmemberno='".$memberno."' AND defaultmemberid = '{$memberid}' AND  	defaultmembermasterno = '{$mastermemberno}' AND  defaultmembermasterid='".$mastermemberid."' 
					 AND defaulttempid = {$tempid}  ORDER BY 	defaultfeedid ASC";
					 
					$checkResult = mysqli_query($this->connection, $qry);
					$ar2= [];
					while($row = mysqli_fetch_assoc($checkResult))
					{
						array_push($ar2, $row);  
					}
					mysqli_close($this->connection);
					$resultset = [];
					 
					foreach($ar2 as $temp_row) 
					{
						if(!empty($temp_row['defaultcategory']))
						{
						   $tableName = $temp_row['defaultcategory'];
						   $resultset[$tableName][] =  $temp_row; 
						}
						else
						{
							
						}
					}
					
					return $resultset;
	}
	
	
	
	function newUpdateDefaultData($mastermemberID, $mastermemberNO,$memberID,$memberNO,$tempID,$values)
	{
		if(!$this->DBLogin())
		{	
			$this->HandleError("Database login failed!");
			return false;
		}
		$qry ="SELECT  * FROM defaultdata A WHERE  defaultmemberno='".$memberNO."' AND defaultmemberid = '{$memberID}' AND  	defaultmembermasterno = '{$mastermemberNO}' AND  defaultmembermasterid='".$mastermemberID."' 
		 AND defaulttempid = {$tempID}  ORDER BY 	defaultfeedid ASC";
		 
		 
		$checkResult = mysqli_query($this->connection, $qry);
		$ar2= [];
		while($row = mysqli_fetch_assoc($checkResult))
		{
			array_push($ar2, $row);  
		}
		
		$resultset = [];
		 
		foreach($ar2 as $temp_row) 
		{	$feed = $temp_row['defaultid'];
			$value = $values[$feed];
			$updateQry = "update defaultdata SET defaultvalue='{$value}' where defaultid ={$feed}"; 
			error_log($updateQry);
			$checkResult = mysqli_query($this->connection, $updateQry);
		}
 

		mysqli_close($this->connection);
		return true;
		
	}
	
	
	
	
	
	
	
	function showTemplateFields($mastermemberid, $mastermemberno, $table,$templateId, $memberid, $memberno){
		if(!$this->DBLogin())
		{	
			$this->HandleError("Database login failed!");
			return false;
		}
		$tableName= '';
		$tableColumnPrefix = '';
		$defaultcategory = '';
		$pre= '';
		if($table=='APPLIANCES')
		{
			$tableName = 'applianceinforp';
			$defaultcategory = $table;
			$pre = 'apprp';
		}
		else if($table=='FINANCIALS')
		{
			$tableName = 'financialinforp';
			$defaultcategory = $table;
			$pre = 'finrp';
		}
		else if($table=='LEASETERMS')
		{
			$tableName = 'leasetermrp';
			$defaultcategory = $table;
			$pre = 'lstrmrp';
		}
		else if($table=='LIMITS')
		{
			$tableName = 'limitinforp';
			$defaultcategory = $table;
			$pre = 'limitrp';
		}
		else if($table=='MISC')
		{
			$tableName = 'miscinforp';
			$defaultcategory = $table;
			$pre = 'miscrp';
		}
		else if($table=='PETS')
		{
			$tableName = 'petinforp';
			$defaultcategory = $table;
			$pre = 'petrp';
		}
		else if($table=='PROPERTY')
		{
			$tableName = 'propertyinforp';
			$defaultcategory = $table;
			$pre = 'proprp';
		}
		else if($table=='RESIDENT')
		{
			$tableName = 'residentinforp';
			$defaultcategory = $table;
			$pre = 'resrp';
		}
		else if($table=='ECONTACT')
		{
			$tableName = 'econtactinforp';
			$defaultcategory = $table;
			$pre = 'econtactrp';
		}
		else if($table=='UNITADDRES')
		{
			$tableName = 'unitaddressrp';
			$defaultcategory = $table;
			$pre = 'unitrp';
		}
		else if($table=='UTILITIES')
		{
			$tableName = 'utilityinforp';
			$defaultcategory = $table;
			$pre = 'utilityrp';
		}
		else if($table=='VEHICLEPAR')
		{
			$tableName = 'vehicleparkrp';
			$defaultcategory = $table;
			$pre = 'vehrp';
		}
		else if($table=='STORAGE')
		{
			$tableName = 'storageinforp';
			$defaultcategory = $table;
			$pre = 'storerp';
		}
		else
		{
			 
			return false;
		}
		
		$qry = "select * from {$tableName} where 	{$pre}tempid ={$templateId} AND {$pre}defaultreq = 1 and {$pre}select=1";
		error_log($qry);
		$checkResult = mysqli_query($this->connection, $qry);
		$ar2= [];
		while($row = mysqli_fetch_assoc($checkResult))
		{
			array_push($ar2, $row);  
		}
	    mysqli_close($this->connection);
		return $ar2;
		
	}
	
	function updateDefaultsGlobal($mastermemberid, $mastermemberno, $templateId,$fieldselect, $fieldName, $fieldValue, $templateselect, $templateName)
	{
		if(!$this->DBLogin())
		{	
			$this->HandleError("Database login failed!");
			return false;
		}
		
		$seletedTemplateCount = count($templateselect);
		foreach($templateselect as $key => $val)
		{
			 $template= $templateName[$key];
			 foreach($fieldselect as $fieldKey => $fieldVal)
			{
				 $fieldIndex= $fieldKey;
				 $fieldId= $fieldName[$fieldIndex];
				 $fieldInputVal =  $fieldValue[$fieldIndex];
				 $updateQuery = "UPDATE defaultdata SET defaultvalue='{$fieldInputVal}' WHERE defaultmembermasterid='{$mastermemberid}' AND defaultmembermasterno = '{$mastermemberno}' AND defaulttempid ={$template}  AND 	defaultfieldid = '{$fieldId}'; ";
				 $checkResult = mysqli_query($this->connection, $updateQuery);	
					
			}
		}
		
		 mysqli_close($this->connection);
		return true;
		
		
		
	}
	
	
	
	/* 
	   ===========================================================================
	   ===============Any thing below this will be deleted later  ================
	   ===========================================================================
	*/
	
	
	
	
	function QueryCalculations($memberid, $memberno, $dates, $selected)
	{
		if(!$this->DBLogin())
        {
            $this->HandleError("Database login failed!");
            return false;
        }
		
		$ar = [];
		$calculations = [];
		
		$datear = explode(" - ", $dates);
		
		$datestart = date("Y/m/d H:i:s", strtotime($datear[0]));
		$dateend = date("Y/m/d H:i:s", strtotime($datear[1]));
		
		$datestart = date('Y-m-d H:i:s', strtotime($datestart . ' -1 day'));
		$dateend = date('Y-m-d H:i:s', strtotime($dateend . ' +1 day'));
		
		if($selected == 'All')
		{
			$qry = "select sum(case when profilestatus = 'C' and appresult != '' and appresult != 'E' then 1 else 0 end) as opportunity,
						sum(case when appresult = 'A' then 1 else 0 end) as prequalified, 
						sum(case when appresult = 'C' then 1 else 0 end) as ontheedge,
						sum(case when appresult = 'D' then 1 else 0 end) as waitlist,
						sum(case when totaleviction > 0 then 1 else 0 end) as eviction,   
						sum(case when landlordcollection > 0 then 1 else 0 end) as landlord,
						sum(case when pastdueutilities > 0 then 1 else 0 end) as pastdue,
						sum(case when apprentqualify = 'Y' then 1 else 0 end) as rentqualifier   
						from prequal.applicant
						where appmemberid = '$memberid' AND appmemberno = '$memberno'
						AND profilecreateddate BETWEEN '$datestart' AND '$dateend';";
		}
        else
		{
			$qry = "select sum(case when profilestatus = 'C' and appresult != '' and appresult != 'E' then 1 else 0 end) as opportunity,
						sum(case when appresult = 'A' then 1 else 0 end) as prequalified, 
						sum(case when appresult = 'C' then 1 else 0 end) as ontheedge,
						sum(case when appresult = 'D' then 1 else 0 end) as waitlist,
						sum(case when totaleviction > 0 then 1 else 0 end) as eviction,   
						sum(case when landlordcollection > 0 then 1 else 0 end) as landlord,
						sum(case when pastdueutilities > 0 then 1 else 0 end) as pastdue,
						sum(case when apprentqualify = 'Y' then 1 else 0 end) as rentqualifier   
						from prequal.applicant
						where appmemberid = '$memberid' AND appmemberno = '$memberno' AND apppropertyid = '$selected'
						AND profilecreateddate BETWEEN '$datestart' AND '$dateend';";
		}	
		
		$result = mysqli_query($this->connection,$qry);
		
		if(!$result || mysqli_num_rows($result) <= 0)
		{
			return ['0', '0', '0', '0', '0', '0', '0', '0'];
		}

		$row = mysqli_fetch_assoc($result);
		
		mysqli_close($this->connection);
		
		return array_map(function($v){return $v ?: '0';}, $row);
		
	}
	 //*******************************************//
    //--- Authenticate XML POST Keys - Prequal ---
	//    Match the provided keyid and Key
    //*******************************************//
	function GetAuthenticationKeys($autkeyid,$autkey)

	{
		if(!$this->DBLogin())
        	{
            	 $this->HandleError("Database login failed!");
            	 return false;
        	}

 	    $qry = "Select * from authorizationkeys where keyid = '".$autkeyid."' and keyvalue = '".$autkey."'";
        
        $result = mysqli_query($this->connection,$qry);
              
        
        if(!$result || mysqli_num_rows($result) <= 0)
        {
            $this->HandleError("Authorization Error. Unable to match the provided key");
			mysqli_close($this->connection);
            return false;
        
        } else 
        
        {
		   mysqli_close($this->connection);
           return true ;  
        }
  
	}   
    
	//*******************************************//
    //--- Retrieve the Keys for psoting- Prequal---
	//    Retrieve the key using the provided keyid
    //*******************************************//
	function RetrieveAuthenticationKeys($autkeyid)

	{
		if(!$this->DBLogin())
        	{
            	 $this->HandleError("Database login failed!");
            	 return false;
        	}

 	    $qry = "Select * from authorizationkeys where keyid = '".$autkeyid."'";
        
        $result = mysqli_query($this->connection,$qry);
              
        
        if(!$result || mysqli_num_rows($result) <= 0)
        {
            $this->HandleError("Authorization Error. Unable to match the provided key");
			mysqli_close($this->connection);
            return false;
        
        } else 
        
        {
			$row = mysqli_fetch_assoc($result);
		    mysqli_close($this->connection);
		    return $row;
            
        }
  
	}
	
    //--- Membership Add/Update Function used to create or update records - Called from api_getmembers.php --- 
       
	function LoadMembers($memberid,$memberno,$membername,$membertoken,$userid,$memberadd1,$memberadd2,$membercity,$memberstate,$memberzip,$memberphone,$memberemail1,$memberemail2)

	{
		if(!$this->DBLogin())
        	{
            	  $this->HandleError("Database login failed!");
            	  return false;
        	}

 	    $qry = 'Select * from members where MemberId = "' .$memberid.'" and MemberNo = "' .$memberno. '" ;';
		               
        $result = mysqli_query($this->connection,$qry); 
        
        if(!$result || mysqli_num_rows($result) <= 0)
        {
          $qry = 'INSERT INTO members (MemberId,
                  MemberNo,
				  MemberName,
                  MemberAddress1,
                  MemberAddress2,
                  MemberCity,
                  MemberState,
                  MemberZip,
                  MemberPhone,
				  MemberEmail1,
				  MemberEmail2,
                  MemberStatus,
                  MemberToken,
                  MemberCreated,
                  MemberCreatedBy
                  )
                  VALUES
                  (
                  
                  "' . $this->SanitizeForSQL($this->connection,$memberid) . '",
                  "' . $this->SanitizeForSQL($this->connection,$memberno) . '",
				  "' . $this->SanitizeForSQL($this->connection,$membername) . '",
                  "' . $this->SanitizeForSQL($this->connection,$memberadd1) . '",
                  "' . $this->SanitizeForSQL($this->connection,$memberadd2) . '",
                  "' . $this->SanitizeForSQL($this->connection,$membercity) . '",
                  "' . $this->SanitizeForSQL($this->connection,$memberstate) . '",
                  "' . $this->SanitizeForSQL($this->connection,$memberzip) . '",
                  "' . $this->SanitizeForSQL($this->connection,$memberphone) . '",
				  "' . $this->SanitizeForSQL($this->connection,$memberemail1) . '",
				  "' . $this->SanitizeForSQL($this->connection,$memberemail2) . '",
                  "A",
                  "' . $this->SanitizeForSQL($this->connection,$membertoken) . '",
                  NOW() ,
                  "' . $this->SanitizeForSQL($this->connection,$userid) . '"
                  )' ;
                                                     
                  if (mysqli_query($this->connection, $qry)) {
                     mysqli_close($this->connection);	 
					 // notify the member by email
					 $this->SendMemberConfirmationEmail($memberemail1);
                     return true ;
                   }
                   else
                   {
                     mysqli_close($this->connection);
                     return false;
                   }
               
        } else 
        
        {
           // already exists - update the member
           $qry = 'update members set MemberName = "' . $this->SanitizeForSQL($this->connection,$membername) . '",
		           MemberAddress1 = "' . $this->SanitizeForSQL($this->connection,$memberadd1) . '",
		           MemberAddress2 = "' . $this->SanitizeForSQL($this->connection,$memberadd2) . '",
				   MemberCity = "' . $this->SanitizeForSQL($this->connection,$membercity) . '",
				   MemberState = "' . $this->SanitizeForSQL($this->connection,$memberstate) . '",
				   MemberZip = "' . $this->SanitizeForSQL($this->connection,$memberzip) . '",
				   MemberPhone = "' . $this->SanitizeForSQL($this->connection,$memberphone) . '",
				   MemberEmail1 = "' . $this->SanitizeForSQL($this->connection,$memberemail1) . '",
				   MemberEmail2 = "' . $this->SanitizeForSQL($this->connection,$memberemail2) . '",
				   MemberToken = "' . $this->SanitizeForSQL($this->connection,$membertoken) . '",
				   MemberUpdated = NOW() ,
				   MemberUpdatedBy = "' . $this->SanitizeForSQL($this->connection,$userid) . '"
				   where
				   MemberId = "' .$memberid.'"
				   and MemberNo = "' .$memberno. '" ' ;
				   				    
				   if (mysqli_query($this->connection, $qry)) {
                     mysqli_close($this->connection);
                     return true ;
                   }
                   else
                   {
                     mysqli_close($this->connection);
                     return false;
                   }           
           
        }

	}
    //AO-----------------------------------------------------------------------------------
	function check_insert_properties($x)
	{
		$str = "";
		$ar = [];
        $checked = $x;
              
        for($i = 0; $i <= 4; $i++)
		{
			if(in_array(strval($i), $checked))
			{
				switch($checked[array_search(strval($i), $checked)])
				{
					case '0': $str .= "'Y', ";
						break;
					case '1': $str .= "'Y', ";
						break;
					case '2': $str .= "'Y', ";
						break;
					case '3': $str .= "'Y', ";
						break;
					case '4': $str .= "'Y', ";
						break;
				}
			}
			else
			{
				switch($i)
				{
					case 0: $str .= "'', ";
						break;
					case 1: $str .= "'', ";
						break;
					case 2: $str .= "'', ";
						break;
					case 3: $str .= "'', ";
						break;
					case 4: $str .= "'', ";
						break;
				}
			}
		}
        return $str;
    }
	
	function check_update_property($x)
	{
		$str = "";
		$ar = [];
        $checked = $x;
              
        for($i = 0; $i <= 4; $i++)
		{
			if(in_array(strval($i), $checked))
			{
				switch($checked[array_search(strval($i), $checked)])
				{
					case '0': $str .= "Studio = 'Y', ";
						break;
					case '1': $str .= "OneBedroom = 'Y', ";
						break;
					case '2': $str .= "TwoBedroom = 'Y', ";
						break;
					case '3': $str .= "ThreeBedroom = 'Y', ";
						break;
					case '4': $str .= "FourBedroom = 'Y', ";
						break;
				}
			}
			else
			{
				switch($i)
				{
					case 0: $str .= "Studio = '', ";
						break;
					case 1: $str .= "OneBedroom = '', ";
						break;
					case 2: $str .= "TwoBedroom = '', ";
						break;
					case 3: $str .= "ThreeBedroom = '', ";
						break;
					case 4: $str .= "FourBedroom = '', ";
						break;
				}
			}
		}
        return $str;
    }
	
	function check_billing($billing, $address1, $address2, $city, $state, $zip, $update)
	{
		$str = '';
		
		if($billing == 'Y')
		{
			if($update != 'Y')
			{
				$str = "'address1', 'address2', "
			. "'city', 'state', 'zip' ";
			}
			else
			{
				$str = "BillingAddress1 = '{$this->SanitizeForSQL($this->connection,$address1)}', BillingAddress2 = '{$this->SanitizeForSQL($this->connection,$address2)}', "
				. "BillingCity = '{$this->SanitizeForSQL($this->connection,$city)}', BillingState = '{$this->SanitizeForSQL($this->connection,$state)}', "
				. "BillingZip = '{$this->SanitizeForSQL($this->connection,$zip)}' ";
			}
		}
		else
		{
			if($update != 'Y')
			{
				$str = "'{$this->SanitizeForSQL($this->connection,$address1)}', '{$this->SanitizeForSQL($this->connection,$address2)}', "
				. "'{$this->SanitizeForSQL($this->connection,$city)}', '{$this->SanitizeForSQL($this->connection,$state)}', '{$this->SanitizeForSQL($this->connection,$zip)}' ";
			}
			else
			{
				$str = "BillingAddress1 = '{$this->SanitizeForSQL($this->connection,$address1)}', BillingAddress2 = '{$this->SanitizeForSQL($this->connection,$address2)}', "
				. "BillingCity = '{$this->SanitizeForSQL($this->connection,$city)}', BillingState = '{$this->SanitizeForSQL($this->connection,$state)}', BillingZip = '{$this->SanitizeForSQL($this->connection,$zip)}' ";
			}
		}
		return $str;
	}
	

	
	function InitMember($memberid, $memberno)
	{		
		if(!$this->DBLogin())
        	{
            	  $this->HandleError("Database login failed!");
            	  return false;
        	}

 	    $qry = "SELECT * FROM members WHERE MemberId = '"
		. $this->SanitizeForSQL($this->connection, $memberid) . "' AND MemberNo = '"
		. $this->SanitizeForSQL($this->connection, $memberno) . "';";

        $result = mysqli_query($this->connection,$qry);
		
		$row = mysqli_fetch_assoc($result);
		
		return $row;
	}
	
	function Properties($memberid, $memberno)
	{		
		if(!$this->DBLogin())
        	{
            	  $this->HandleError("Database login failed!");
            	  return false;
        	}
		$ar = [];	

 	    $qry = "SELECT * FROM property WHERE PropertyMemberId = '"
		. $this->SanitizeForSQL($this->connection, $memberid) . "' AND PropertyMemberNo = '"
		. $this->SanitizeForSQL($this->connection, $memberno) . "' "
		. "order by PropertyState,PropertyCity,PropertyName;" ;

        $result = mysqli_query($this->connection,$qry);
		
		while($row = mysqli_fetch_assoc($result))
		{
			array_push($ar, $row);  
		}
		
		return $ar;
	}
	
	function addNewProperty($property_name, $property_address1, $property_address2, $city, $state, $zip,
						 $phone, $email1, $email2, $property, $chk_prty, $schedule, $bill_to,
						 $billing_address1, $billing_address2, $billing_city, $billing_state, $billing_zip, $url,
						 $runcriminal)
	{
		
		if(!$this->DBLogin())
        	{
            	  $this->HandleError("Database login failed!");
            	  return false;
        	}
			
			$prty_str =  $this->check_insert_properties($chk_prty);
			
			$billing_str = $this->check_billing($bill_to, $billing_address1, $billing_address2, $billing_city,
										 $billing_state, $billing_zip, 'N');

 	    $qry = "INSERT INTO prequal.property "
		. "(idproperty, PropertyMemberId, PropertyMemberNo, PropertyName, PropertyAddress1, PropertyAddress2, "
		. "PropertyCity, PropertyState, PropertyZip,  PropertyPhone, PropertyStatus, PropertyToken, PropertyCreated, "
		. "PropertyCreatedBy, PropertyUpdated, PropertyUpdatedBy,  PropertyEmail1, PropertyEmail2, PropertyType, "
		. "Studio, OneBedroom, TwoBedroom, ThreeBedroom, FourBedroom, PropertySchedule,  BillingtoMember, "
		. "BillingAddress1, BillingAddress2, BillingCity, BillingState,  BillingZip, onlineweburl,allowcriminalrun) "
		. "VALUES( "
		. "0, "
		. "'{$_SESSION['s_MemberId']}', " 
		. "'{$_SESSION['s_MemberNo']}', " 
		. "'{$this->SanitizeForSQL($this->connection,$property_name)}', "
		. "'{$this->SanitizeForSQL($this->connection,$property_address1)}', "
		. "'{$this->SanitizeForSQL($this->connection,$property_address2)}', "
		. "'{$this->SanitizeForSQL($this->connection,$city)}', "
		. "'{$this->SanitizeForSQL($this->connection,$state)}', "
		. "'{$this->SanitizeForSQL($this->connection,$zip)}', "
		. "'{$this->SanitizeForSQL($this->connection,$phone)}', "
		. "'A', "
		. "'{$_SESSION['s_Token']}|" . mt_rand(100000, 10000000) . "', "
		. "NOW(), "
		. "'{$this->SanitizeForSQL($this->connection,$_SESSION['MemberName'])}', "
		. "NOW(), "
		. "'{$this->SanitizeForSQL($this->connection,$_SESSION['MemberName'])}', "
		. "'{$this->SanitizeForSQL($this->connection,$email1)}', "
		. "'{$this->SanitizeForSQL($this->connection,$email2)}', "
		. "'{$this->SanitizeForSQL($this->connection,$property)}', "
		. $prty_str
		. "'{$this->SanitizeForSQL($this->connection,$schedule)}', "
		. "'{$this->SanitizeForSQL($this->connection,$bill_to)}', "
		. $billing_str
		. ", '$url', "
		. "'{$this->SanitizeForSQL($this->connection,$runcriminal)}') " ;
		
        $result = mysqli_query($this->connection,$qry);
		
		$qry = "INSERT INTO prequal.notifications (notifymemberid, notifymemberno, notifydate, notifytype,
		notifytitle, notifydetails, notifyreadflag)
		VALUES ('{$_SESSION['s_MemberId']}', '{$_SESSION['s_MemberNo']}',
		NOW(), 'S', 'Criteria Setup', '$property_name needs criteria information', '');";
		
		$result = mysqli_query($this->connection,$qry);
		
	}
	
	function updateProperty($prty_id, $property_name, $property_address1, $property_address2, $city, $state, $zip,
						 $phone, $email1, $email2, $property, $chk_prty, $schedule, $bill_to,
						 $billing_address1, $billing_address2, $billing_city, $billing_state, $billing_zip, $url,
						 $runcriminal)
	{
		if(!$this->DBLogin())
        	{
            	  $this->HandleError("Database login failed!");
            	  return false;
        	}
			
			$prty_str =  $this->check_update_property($chk_prty);
			
			$billing_str = $this->check_billing($bill_to, $billing_address1, $billing_address2, $billing_city,
										 $billing_state, $billing_zip, 'Y');
			
			$qry = "UPDATE prequal.property SET "
			. "PropertyName = '{$this->SanitizeForSQL($this->connection,$property_name)}', "
			. "PropertyAddress1 = '{$this->SanitizeForSQL($this->connection,$property_address1)}', "
			. "PropertyAddress2 = '{$this->SanitizeForSQL($this->connection,$property_address2)}', "
			. "PropertyCity = '{$this->SanitizeForSQL($this->connection,$city)}', "
			. "PropertyState = '{$this->SanitizeForSQL($this->connection,$state)}', "
			. "PropertyZip = '{$this->SanitizeForSQL($this->connection,$zip)}', "
			. "PropertyPhone = '{$this->SanitizeForSQL($this->connection,$phone)}', "
			. "PropertyEmail1 = '{$this->SanitizeForSQL($this->connection,$email1)}', "
			. "PropertyEmail2 = '{$this->SanitizeForSQL($this->connection,$email2)}', "
			. "PropertyType = '{$this->SanitizeForSQL($this->connection,$property)}', "
			. $prty_str
			. "PropertySchedule = '{$this->SanitizeForSQL($this->connection,$schedule)}', "
			. "BillingtoMember = '{$this->SanitizeForSQL($this->connection,$bill_to)}', "
			. "onlineweburl = '$url', "
			. "allowcriminalrun = '$runcriminal', "
			. $billing_str 
			. "WHERE idproperty = '{$this->SanitizeForSQL($this->connection,$prty_id)}';";
			
			$result = mysqli_query($this->connection,$qry);
	}
	
	function updateCriteria($prty_id, $neg1, $neg2, $neg3, $neg4, $neg5, $lmonthly, $hmonthly,
                            $multiplier, $fico, $concession_chk, $concession_amount,
							$rent_concession_chk, $rent_concession_amount, $waived,
							$pet_chk, $pet_discount_amount, $gift_chk, $gift_details,
                            $offer_chk, $offer_details, $changed, $pay_obligation, $single_family_rent,
							$dep_req,$dep_text1,$dep_score1,$dep_text2,$dep_score2,
							$dep_text3,$dep_score3)
	{
		
		if(!$this->DBLogin())
        	{
            	  $this->HandleError("Database login failed!");
            	  return false;
        	}
			
			
			if($changed == 'Y')
			{
			$qry = "UPDATE prequal.property SET "
			. "neggaswater = '{$this->SanitizeForSQL($this->connection,$neg1)}', "
			. "negcell = '{$this->SanitizeForSQL($this->connection,$neg2)}', "
			. "negcable = '{$this->SanitizeForSQL($this->connection,$neg3)}', "
			. "landlorddebt = '{$this->SanitizeForSQL($this->connection,$neg4)}', "
			. "eviction = '{$this->SanitizeForSQL($this->connection,$neg5)}', "
			. "lowrent = '{$this->SanitizeForSQL($this->connection,$lmonthly)}', "
			. "highrent = '{$this->SanitizeForSQL($this->connection,$hmonthly)}', "
			. "rent2income = '{$this->SanitizeForSQL($this->connection,$multiplier)}', "
			. "minscore = '{$this->SanitizeForSQL($this->connection,$fico)}', "
			. "moveconcession = '{$this->SanitizeForSQL($this->connection,$concession_chk)}', "
			. "moveconcessionamount = '{$this->SanitizeForSQL($this->connection,$concession_amount)}', "
			. "rentconcession = '{$this->SanitizeForSQL($this->connection,$rent_concession_chk)}', "
			. "rentconcessionamount = '{$this->SanitizeForSQL($this->connection,$rent_concession_amount)}', "
			. "waivefee = '{$this->SanitizeForSQL($this->connection,$waived)}', "
			. "petrent = '{$this->SanitizeForSQL($this->connection,$pet_chk)}', "
			. "petrentdiscount = '{$this->SanitizeForSQL($this->connection,$pet_discount_amount)}', "
			. "giftcard = '{$this->SanitizeForSQL($this->connection,$gift_chk)}', "
			. "giftcarddetail = '{$this->SanitizeForSQL($this->connection,$gift_details)}', "
			. "customoffer = '{$this->SanitizeForSQL($this->connection,$offer_chk)}', "
			. "customofferdetail = '{$this->SanitizeForSQL($this->connection,$offer_details)}', "
			. "criteriaupdatedate = NOW(), "
			. "criteriaupdatedby = '{$this->SanitizeForSQL($this->connection,$_SESSION['MemberName'])}', "  //. "criteriaupdatedby = '{$_SESSION['username']}'"
			. "Singlefamilyrent = {$this->SanitizeForSQL($this->connection,$single_family_rent)}, "
			. "deductmonthlyobligation = '$pay_obligation', "
			. "depositrequirement = '$dep_req', "
			. "deposit1text = '{$this->SanitizeForSQL($this->connection,$dep_text1)}', "
			. "deposit1FICO = {$this->SanitizeForSQL($this->connection,$dep_score1)}, "
			. "deposit2text = '{$this->SanitizeForSQL($this->connection,$dep_text2)}', "
			. "deposit2FICO = {$this->SanitizeForSQL($this->connection,$dep_score2)}, "
			. "deposit3text = '{$this->SanitizeForSQL($this->connection,$dep_text3)}', "
			. "deposit3FICO = {$this->SanitizeForSQL($this->connection,$dep_score3)} "
			. "WHERE idproperty = '{$this->SanitizeForSQL($this->connection,$prty_id)}';" ;
            
			//error_log(print_r($qry,true)) ;
			
			$result = mysqli_query($this->connection,$qry);

			$backup = "INSERT INTO prequal.criteriahistory "
			. "(criteriaMbrid, criteriaMbrNo, PropertyId, neggaswater, negcell, negcable, landlorddebt, "
			. "eviction, Lowrent, highrent, rent2incratio, minimumscore, moveconcession, moveconcessionamount, "
			. "rentconcession, rentconcessionamount, waivefee, petrent, petrentdiscount, giftcard, giftcarddetail, "
			. "customoffer, customofferdetail, recorddate, userid, deductmonthlyobligation, singlefamilyrent, "
			. "depositrequirement,deposit1text,deposit1FICO,deposit2text,deposit2FICO, "
			. "deposit3text,deposit3FICO) "
			. "VALUES ("
			. "'{$_SESSION['s_MemberId']}', "
			. "'{$_SESSION['s_MemberNo']}', "
			. "'{$this->SanitizeForSQL($this->connection,$prty_id)}', "
			. "'{$this->SanitizeForSQL($this->connection,$neg1)}', "
			. "'{$this->SanitizeForSQL($this->connection,$neg2)}', "
			. "'{$this->SanitizeForSQL($this->connection,$neg3)}', "
			. "'{$this->SanitizeForSQL($this->connection,$neg4)}', "
			. "'{$this->SanitizeForSQL($this->connection,$neg5)}', "
			. "'{$this->SanitizeForSQL($this->connection,$lmonthly)}', "
			. "'{$this->SanitizeForSQL($this->connection,$hmonthly)}', "
			. "'{$this->SanitizeForSQL($this->connection,$multiplier)}', "
			. "'{$this->SanitizeForSQL($this->connection,$fico)}', "
			. "'{$this->SanitizeForSQL($this->connection,$concession_chk)}', "
			. "$concession_amount, "
			. "'{$this->SanitizeForSQL($this->connection,$rent_concession_chk)}', "
			. "$rent_concession_amount, "
			. "'{$this->SanitizeForSQL($this->connection,$waived)}', "
			. "'{$this->SanitizeForSQL($this->connection,$pet_chk)}', "
			. "$pet_discount_amount, "
			. "'{$this->SanitizeForSQL($this->connection,$gift_chk)}', "
			. "'{$this->SanitizeForSQL($this->connection,$gift_details)}', "
			. "'{$this->SanitizeForSQL($this->connection,$offer_chk)}', "
			. "'{$this->SanitizeForSQL($this->connection,$offer_details)}', "
			. "NOW(), "
			. "'{$this->SanitizeForSQL($this->connection,$_SESSION['MemberName'])}', "
			. "'{$this->SanitizeForSQL($this->connection,$pay_obligation)}', "                                                                
			. "'{$this->SanitizeForSQL($this->connection,$single_family_rent)}', "
			. "$dep_req, "
			. "'{$this->SanitizeForSQL($this->connection,$dep_text1)}', "
			. "{$this->SanitizeForSQL($this->connection,$dep_score1)}, "
			. "'{$this->SanitizeForSQL($this->connection,$dep_text2)}', "
			. "{$this->SanitizeForSQL($this->connection,$dep_score2)}, "
			. "'{$this->SanitizeForSQL($this->connection,$dep_text3)}', "
			. "{$this->SanitizeForSQL($this->connection,$dep_score3)} ; " ;
			
			
			$result = mysqli_query($this->connection, $backup);

			}

	}
	
	// rizwan 09/16/2016 added the property add and update function for tricon american home integration
	function CheckProperty($memberid, $memberno, $externalid)
	{		
		if(!$this->DBLogin())
        	{
            	  $this->HandleError("Database login failed!");
            	  return false;
        	}

		$qry = "SELECT * FROM property WHERE PropertyMemberId = '"
		. $this->SanitizeForSQL($this->connection, $memberid) . "' and PropertyMemberNo = '"
		. $this->SanitizeForSQL($this->connection, $memberno) . "' and propertyexternalid = '"
		. $this->SanitizeForSQL($this->connection, $externalid) . "';";
		 	    
        $result = mysqli_query($this->connection,$qry);
		
		if(!$result || mysqli_num_rows($result) <= 0)
		{
			return false ;
		}
		else
		{
		    return true ;
		}
	}
	
	function addInterfaceProperty($property_memberid, $property_memberno, $property_extid, $property_name, $property_address1, $property_address2, $city, $state, $zip,$phone, $email1, $property_rent, $property_beds, $property_baths, $property_lat, $property_lng, $property_sqft, $property_url, $membername, $membertoken,$partnerid)
						 			 					  
	{
		
		if(!$this->DBLogin())
        	{
            	  $this->HandleError("Database login failed!");
            	  return false;
        	}
			
			// set bedroom type
			$onebed  ='' ;
			$twobed  ='' ;
			$threebed='' ;
			$fourbed ='' ;
			
			if($property_beds == "1" )
			{
			  $onebed =  "Y" ;
			} elseif($property_beds == "2")
			{
			  $twobed = "Y" ;
			} elseif($property_beds == "3")
			{
			  $threebed = "Y" ;
			} elseif($property_beds == "4" || $property_beds == "5" || $property_beds == "6")
			{
			  $fourbed = "Y" ;
			}
			//error_log(print_r('Insert level',true)) ;
			//error_log(print_r($property_beds,true)) ;
			//error_log(print_r($onebed,true)) ;
			//error_log(print_r($twobed,true)) ;
			//error_log(print_r($threebed,true)) ;
			//error_log(print_r($fourbed,true)) ;

 	    $qry = "INSERT INTO prequal.property "
		. "(idproperty, PropertyMemberId, PropertyMemberNo, PropertyName, PropertyAddress1, PropertyAddress2, "
		. "PropertyCity, PropertyState, PropertyZip,  PropertyPhone, PropertyStatus, PropertyToken, PropertyCreated, "
		. "PropertyCreatedBy, PropertyUpdated, PropertyUpdatedBy,  PropertyEmail1, PropertyEmail2, PropertyType, "
		. "Studio, OneBedroom, TwoBedroom, ThreeBedroom, FourBedroom, PropertySchedule, BillingtoMember, "
		. "BillingAddress1, BillingAddress2, BillingCity, BillingState,BillingZip, onlineweburl,allowcriminalrun, "
		. "propertyexternalid,propertybaths,propertylat,propertylng,propertysqft,propertyweburl,Lowrent,highrent,rent2income, "
		. "minscore,deductmonthlyobligation,depositrequirement,deposit1text,deposit1FICO,deposit2text,deposit2FICO,deposit3text,deposit3FICO, "
		. "criteriaupdatedate,propertypartnerid,singlefamilyrent) "
		. "VALUES( "
		. "0, "
		. "'{$property_memberid}', " 
		. "'{$property_memberno}', " 
		. "'{$this->SanitizeForSQL($this->connection,$property_name)}', "
		. "'{$this->SanitizeForSQL($this->connection,$property_address1)}', "
		. "'{$this->SanitizeForSQL($this->connection,$property_address2)}', "
		. "'{$this->SanitizeForSQL($this->connection,$city)}', "
		. "'{$this->SanitizeForSQL($this->connection,$state)}', "
		. "'{$this->SanitizeForSQL($this->connection,$zip)}', "
		. "'{$this->SanitizeForSQL($this->connection,$phone)}', "
		. "'A', "
		. "'{$membertoken}|" . mt_rand(100000, 10000000) . "', "
		. "NOW(), "
		. "'AUTO', "
		. "NOW(), "
		. "'AUTO', "
		. "'{$this->SanitizeForSQL($this->connection,$email1)}', "
		. "'', "
		. "'S', "
		. "'', "
		. "'{$onebed}',"
		. "'{$twobed}',"
		. "'{$threebed}',"
		. "'{$fourbed}',"
		. "'', "
		. "'Y', "
		. "'', "
		. "'', "
		. "'', "
		. "'', "
		. "'', "
		. "'', "
		. "'', " 
		. "'{$property_extid}', "
		. "'{$property_baths}', "
		. "'{$property_lat}', "
		. "'{$property_lng}', "
		. "'{$property_sqft}', "
		. "'{$property_url}', "
		. "0.00, "
		. "0.00, "
		. "3.0, "
		. "500, "
		. "'Y', "
		. "'Y', "
		. "'Cosigner required', "
		. "550,"
		. "'1.5 month security deposit', "
		. "600,"
		. "'1 month security deposit', "
		. "600,"
		. "NOW(), "
		. "'{$partnerid}', "
		. "{$property_rent}) " ;
		
		//error_log(print_r($qry,true)) ;
		if(!mysqli_query($this->connection,$qry))
        {
			//error_log(print_r($qry,true));
			$this->HandleDBError("Error inserting data to the property table:$qry");
			mysqli_close($this->connection);
            return false;
        } else
		{
			mysqli_close($this->connection);
			//error_log(print_r('Successfully insert property',true));
			return true ;
		}
        
	}
	
	function updateInterfaceProperty($property_memberid, $property_memberno, $property_extid, $property_name, $property_address1, $property_address2, $city, $state, $zip,
						 $phone, $email1, $property_rent, $property_beds, $property_baths, $property_url, $membername)
	{
		if(!$this->DBLogin())
        	{
            	  $this->HandleError("Database login failed!");
            	  return false;
        	}
			
			// set bedroom type
			$onebed  ='' ;
			$twobed  ='' ;
			$threebed='' ;
			$fourbed ='' ;
			
			if($property_beds == "1" )
			{
			  $onebed = "Y" ;
			} elseif($property_beds == "2")
			{
			  $twobed = "Y" ;
			} elseif($property_beds == "3")
			{
			  $threebed = "Y" ;
			} elseif($property_beds == "4" || $property_beds == "5" || $property_beds == "6")
			{
			  $fourbed = "Y" ;
			} 
			
			//error_log(print_r('Update level',true)) ;
			//error_log(print_r($property_beds,true)) ;
			//error_log(print_r($onebed,true)) ;
			//error_log(print_r($twobed,true)) ;
			//error_log(print_r($threebed,true)) ;
			//error_log(print_r($fourbed,true)) ;
			
			$qry = "UPDATE prequal.property SET "
			. "PropertyName = '{$this->SanitizeForSQL($this->connection,$property_name)}', "
			. "PropertyAddress1 = '{$this->SanitizeForSQL($this->connection,$property_address1)}', "
			. "PropertyAddress2 = '{$this->SanitizeForSQL($this->connection,$property_address2)}', "
			. "PropertyCity   = '{$this->SanitizeForSQL($this->connection,$city)}', "
			. "PropertyState  = '{$this->SanitizeForSQL($this->connection,$state)}', "
			. "PropertyZip    = '{$this->SanitizeForSQL($this->connection,$zip)}', "
			. "PropertyPhone  = '{$this->SanitizeForSQL($this->connection,$phone)}', "
			. "PropertyEmail1 = '{$this->SanitizeForSQL($this->connection,$email1)}', "
			. "singlefmilyrent= {$property_rent}, "
			. "OneBedroom = '{$onebed}', "
			. "TwoBedroom = '{$twobed}', "
			. "ThreeBedroom = '{$threebed}', "
			. "FourBedroom = '{$fourbed}', "
			. "propertyweburl = '{$property_url}' "
			 
			. "WHERE PropertyMemberId = '{$property_memberid}' and PropertyMemberNo = '{$property_memberno}' and propertyexternalid =  '{$property_extid}' ;";
			
			$result = mysqli_query($this->connection,$qry);
			mysqli_close($this->connection); 
	}
	
	function CheckAdditionalHomes($applicationid)
	{		
		if(!$this->DBLogin())
        	{
            	  $this->HandleError("Database login failed!");
            	  return false;
        	}

		$qry = "SELECT * FROM appadditional WHERE idfromapplicant = '"
		. $this->SanitizeForSQL($this->connection, $applicationid) . "';";
		 	    
        $result = mysqli_query($this->connection,$qry);
		
		if(!$result || mysqli_num_rows($result) <= 0)
		{
			return false ;
		}
		else
		{
		    return true ;
		}
	}
	
	function RetrieveAdditionalHomes($applicationid)
	{		
		if(!$this->DBLogin())
        	{
            	  $this->HandleError("Database login failed!");
            	  return false;
        	}

		$qry = "SELECT * FROM appadditional WHERE idfromapplicant = '"
		. $this->SanitizeForSQL($this->connection, $applicationid) . "';";
		 	    
        $result = mysqli_query($this->connection,$qry);
		
		if(!$result || mysqli_num_rows($result) <= 0)
        {
          mysqli_close($this->connection);
          return false;   
        }
		
		$addhomeresult = mysqli_fetch_assoc($result);
		mysqli_close($this->connection);
		
		return $addhomeresult ;
				
	}
	
	function addAdditionalHomes($applicantid, $html)			 			 					  
	{
		
		if(!$this->DBLogin())
        	{
            	  $this->HandleError("Database login failed!");
            	  return false;
        	}
									
 	    $qry = "INSERT INTO prequal.appadditional "
		. "(idappadditionaldata,idfromapplicant, additionaldata) "
		. "VALUES( "
		. "0, "
		. "'{$applicantid}', " 
		. "'{$this->SanitizeForSQL($this->connection,$html)}')" ;
				
        $result = mysqli_query($this->connection,$qry) ;
		if($result)
		{
			return true ;
		}
		 else
		{
			return false ;
		}
	}
	
	function updateAdditionalHomes($applicantid, $html)			 			 					  
	{
		
		if(!$this->DBLogin())
        	{
            	  $this->HandleError("Database login failed!");
            	  return false;
        	}
	    $qry = "UPDATE prequal.appadditional  SET "
			. "additionaldata = '{$this->SanitizeForSQL($this->connection,$html)}' " 
			. "WHERE idfromapplicant = '{$applicantid}' ;";
			
			$result = mysqli_query($this->connection,$qry);
	}
	// end of Rizwan update for Tricon American Homes interface
	
	function Login($username, $password)
	{
		if(!$this->DBLogin())
        	{
            	  $this->HandleError("Database login failed!");
            	  return false;
        	}
			
		$pass = md5($password);	
			
		$login = "SELECT applicantemail, apppassword, idapplicant FROM prequal.applicant WHERE applicantemail = '"
		. $this->SanitizeForSQL($this->connection,$username) . "' AND apppassword = '" . $this->SanitizeForSQL($this->connection,$pass) . "';";
		
		$result = mysqli_query($this->connection, $login);
		
		$rows = mysqli_num_rows($result);
		
		if($rows > 0)
		{
			$_SESSION['AppEmail'] 	 = $username;
			return true; //true if login successfull
		}
		else
		{
			return false;
		}
	}
	
	function PropertyLinks($userid, $userno)
	{
		if(!$this->DBLogin())
        	{
            	  $this->HandleError("Database login failed!");
            	  return false;
        	}
			
			$ar = [];
			
			$applications = "SELECT * FROM prequal.property WHERE PropertyMemberId = '$userid' AND PropertyMemberNo = '$userno';";
			
			$result = mysqli_query($this->connection, $applications);
			
			while($row = mysqli_fetch_assoc($result))
			{
				array_push($ar, $row);  
			}
			
			return $ar;
	}
	
	function PullApplications($username)
	{
		if(!$this->DBLogin())
        	{
            	  $this->HandleError("Database login failed!");
            	  return false;
        	}
			
			$ar = [];
			
			$applications = "SELECT * FROM prequal.applicant WHERE applicantemail = '" . $this->SanitizeForSQL($this->connection, $username) . "';";
			
			$result = mysqli_query($this->connection, $applications);
			
			while($row = mysqli_fetch_assoc($result))
			{
				array_push($ar, $row);  
			}
			
			return $ar;
	}
	
	function InitApplication($appid)
	{
		if(!$this->DBLogin())
        	{
            	  $this->HandleError("Database login failed!");
            	  return false;
        	}
		
		$qry = "SELECT * FROM prequal.applicant LEFT JOIN prequal.property ON
		(applicant.appmemberid = property.PropertyMemberId AND applicant.appmemberno = property.PropertyMemberNo
		AND applicant.apppropertyid = property.idproperty) WHERE idapplicant = '" . $this->SanitizeForSQL($this->connection, $appid) . "';";
		
		$result = mysqli_query($this->connection, $qry);
		 
		$approw = mysqli_fetch_assoc($result);

		$_SESSION['s_Token']        = $approw['PropertyToken'];
		$_SESSION['s_PropertyName'] = $approw['PropertyName'];
		$_SESSION['s_MemberId']     = $approw['PropertyMemberId'];
		$_SESSION['s_MemberNo']     = $approw['PropertyMemberNo'];
		$_SESSION['s_Address1']     = $approw['PropertyAddress1'];
		$_SESSION['s_Address2']     = $approw['PropertyAddress2'];
		$_SESSION['s_City']         = $approw['PropertyCity'];
		$_SESSION['s_State']        = $approw['PropertyState'];
		$_SESSION['s_Zip'] 			= $approw['PropertyZip'];
		$_SESSION['s_Phone'] 		= $approw['PropertyPhone'];
		$_SESSION['s_Email1'] 		= $approw['PropertyEmail1'];
		$_SESSION['s_Schedule'] 	= $approw['PropertySchedule'];
		$_SESSION['s_onlineweburl'] = $approw['onlineweburl'];
		// applicant info from session
		$_SESSION['FirstName'] 		= $approw['appfirstname'];
		$_SESSION['LastName'] 		= $approw['applastname'];
		$_SESSION['AppEmail'] 		= $approw['applicantemail'];
		$_SESSION['applicantID']  	= $approw['idapplicant'];
		
		$_POST['MidName'] = $approw['appmidname'];
		
		$_POST['Phone'] = $approw['appphone'];
		
		$_POST['Suffix'] = $approw['appsuffix'];
		$_POST['Address'] = $approw['appaddress'];
		$_POST['Unit'] = $approw['appaddressunit'];
		$_POST['City'] = $approw['appcity'];
		$_POST['State'] = $approw['appstate'];
		$_POST['Zip'] = $approw['appzip'];
		$_POST['DesireUnit'] = $approw['desireunittype'];
		$_POST['Income'] = $approw['appincome'];
		$_POST['Rent'] = $approw['monthlyrent'];
		$_POST['Message'] = $approw['messagetoproperty'];
		$_POST['FreezPin'] = $approw['appsecuritypin'];
		
		$mdate = strtotime($approw['appmoveindate']); 
		$dob   = strtotime($approw['appdob']);
		
		$_POST['Movedate'] = date('m/d/Y', $mdate);
		$_POST['dateofbirth'] = date('m/d/Y', $dob);
		
		$unitType = '';
		if($approw['Studio'] == 'Y' || $approw['OneBedroom'] == 'Y' || $approw['TwoBedroom'] == 'Y' || $approw['ThreeBedroom'] == 'Y' || $approw['FourBedroom'] == 'Y')
		   {
			$unitType .= '<option value="" selected>Select</option>' ;
			
			if($approw['Studio'] == 'Y')
			{
				if($approw['desireunittype'] == 'S')
				{
					$unitType .= '<option value="S" selected>Studio</option>' ;
				}
				else
				{
					$unitType .= '<option value="S">Studio</option>' ;
				}
			}	
			if($approw['OneBedroom'] == 'Y')
			{
				if($approw['desireunittype'] == '1')
				{
					$unitType .= '<option value="1" selected>1 Bedroom</option>' ;
				}
				else
				{
					$unitType .= '<option value="1">1 Bedroom</option>' ;
				}
			}
			if($approw['TwoBedroom'] == 'Y')
			{
				if($approw['desireunittype'] == '2')
				{
					$unitType .= '<option value="2" selected>2 Bedroom</option>' ;
				}
				else
				{
					$unitType .= '<option value="2">2 Bedroom</option>' ;
				}
			}
			if($approw['ThreeBedroom'] == 'Y')
			{
				if($approw['desireunittype'] == '3')
				{
					$unitType .= '<option value="3" selected>3 Bedroom</option>' ;
				}
				else
				{
					$unitType .= '<option value="3">3 Bedroom</option>' ;
				}
			}
			if($approw['FourBedroom'] == 'Y')
			{
				if($approw['desireunittype'] == '4')
				{
					$unitType .= '<option value="4" selected>4+ Bedroom</option>';
				}
				else
				{
					$unitType .= '<option value="4">4+ Bedroom</option>';
				}
			}
			
		   } else
		   
		   {
			 // set default value here
			 $unitType .= '<option value="" selected>Select</option>' ;
			 $unitType .= '<option value="S">Studio</option>' ;
			 $unitType .= '<option value="1">1 Bedroom</option>' ;
			 $unitType .= '<option value="2">2 Bedroom</option>' ;
			 $unitType .= '<option value="3">3 Bedroom</option>' ;
			 $unitType .= '<option value="4">4+ Bedroom</option>' ;
		   }
		   
		   $_SESSION['s_UnitType']  = $unitType ;
	
	}
	
	function VerifyCode($code, $email)
	{
		if(!$this->DBLogin())
        	{
            	  $this->HandleError("Database login failed!");
            	  return false;
        	}
		
		$qry = "SELECT * FROM prequal.emailverify WHERE loginemail = '"
		. $this->SanitizeForSQL($this->connection, $email) . "' AND generatedcode = '"
		. $this->SanitizeForSQL($this->connection, $code) . "' AND TIMESTAMPDIFF(MINUTE,generationtime,NOW()) < 20;";
		
		
		$result = mysqli_query($this->connection, $qry);
		
		if(!$result || mysqli_num_rows($result) <= 0)
        {
            return false;
		}
		else
		{
			return true;
		}
	}
	
	function CompleteVerification($code, $email)
	{
		if(!$this->DBLogin())
        	{
            	  $this->HandleError("Database login failed!");
            	  return false;
        	}
			
		$qry = "UPDATE prequal.emailverify SET verified = 'Y', verifieddate = NOW() WHERE generatedcode = '"
		. $this->SanitizeForSQL($this->connection, $code) . "' AND loginemail = '"
		. $this->SanitizeForSQL($this->connection, $email) . "';";

		$result = mysqli_query($this->connection, $qry);
			
		if(mysqli_affected_rows($this->connection) <= 0)
        {
			$this->HandleError("Unable to Verify code to the database");
			mysqli_close($this->connection);
            return false;
		}
		return true;
	}
	
	function NewCode($email)
	{
		if(!$this->DBLogin())
        	{
            	  $this->HandleError("Database login failed!");
            	  return false;
        	}

		$newcode = mt_rand(1000,10000);
		
		$qry = "UPDATE prequal.emailverify SET generatedcode = '"
		. $this->SanitizeForSQL($this->connection,$newcode) . "', generationtime = NOW() WHERE loginemail = '"
		. $this->SanitizeForSQL($this->connection,$email) . "';";

		$result = mysqli_query($this->connection,$qry);
		
		if(mysqli_affected_rows($this->connection) <= 0)
        {
			$this->HandleError("Unable to create a new code in the database from NewCode function");
			mysqli_close($this->connection);
            return false;
		}
		//send email to user
		$this->SendUserConfirmationEmail($email, $newcode);
		
		mysqli_close($this->connection);
		return true;
	}
	
	function Notifications($userid, $memberno)
	{
		if(!$this->DBLogin())
        	{
            	  $this->HandleError("Database login failed!");
            	  return false;
        	}
			
		$icon = '';
		$str = '';
		$ar = [];
			
		$qry = "SELECT TIMESTAMPDIFF(HOUR,notifydate,NOW()) AS hoursago, notifymemberid, notifymemberno, notifydate, notifytype, notifytitle, notifydetails, notifyreadflag, idnotifications
		FROM prequal.notifications WHERE notifymemberid = '$userid' AND notifymemberno = '$memberno' AND notifyreadflag = '' ORDER BY notifydate DESC;";
		
		$result = mysqli_query($this->connection,$qry);

		if(!$result || mysqli_num_rows($result) <= 0)
        {
            return [];
		}
		
		while($row = mysqli_fetch_assoc($result))
		{
			array_push($ar, $row);  
		}
			
		return $ar;
	}
	
	function HideNotification($id)
	{
		if(!$this->DBLogin())
        {
            $this->HandleError("Database login failed!");
            return false;
        }

		$qry = "UPDATE prequal.notifications SET notifyreadflag = 'Y' WHERE idnotifications = '{$id}';";

        $result = mysqli_query($this->connection,$qry);
    
        if(mysqli_affected_rows($this->connection) <= 0)
        {
			$this->HandleError("Unable hide a notification");
			mysqli_close($this->connection);
            return false;
		}
		
		mysqli_close($this->connection);
	}
	
	function Applicants($memberid, $memberno, $dates, $prty)
	{
		if(!$this->DBLogin())
        {
            $this->HandleError("Database login failed!");
            return false;
        }
		
		$ar = [];
		
		$datear = explode(" - ", $dates);
		
		$datestart = date("Y/m/d H:i:s", strtotime($datear[0]));
		$dateend = date("Y/m/d H:i:s", strtotime($datear[1]));
		
		$datestart = date('Y-m-d H:i:s', strtotime($datestart . ' -1 day'));
		$dateend = date('Y-m-d H:i:s', strtotime($dateend . ' +1 day'));
		
		if($prty == "All")
		{
			$str = "";
		}
		else
		{
			$str = "AND apppropertyid = '$prty' ";
		}
		
		$qry = "SELECT * FROM prequal.applicant
		WHERE profilestatus = 'C'
		AND appmemberid = '$memberid'
		AND appmemberno = '$memberno'
		AND profilecreateddate BETWEEN '$datestart' AND '$dateend' $str;";
		
		$result = mysqli_query($this->connection,$qry);
    
        if(mysqli_affected_rows($this->connection) <= 0)
        {
			$this->HandleError("Unable to grab Applicants");
			mysqli_close($this->connection);
            return false;
		}
		
		while($row = mysqli_fetch_assoc($result))
		{
			array_push($ar, $row);  
		}
		
		mysqli_close($this->connection);
		
		return $ar;
	}
	
	function Averages($memberid, $memberno, $sprty, $dates)
	{
		if(!$this->DBLogin())
        {
            $this->HandleError("Database login failed!");
            return false;
        }
		
		$ar = [];
		
		$datear = explode(" - ", $dates);
		
		$datestart = date("Y/m/d H:i:s", strtotime($datear[0]));
		$dateend = date("Y/m/d H:i:s", strtotime($datear[1]));
		
		$datestart = date('Y-m-d H:i:s', strtotime($datestart . ' -1 day'));
		$dateend = date('Y-m-d H:i:s', strtotime($dateend . ' +1 day'));
		
		if($sprty == 'All')
		{
			$prty = "";
		}
		else
		{
			$prty = " and i.apppropertyid = '$sprty' ";	
		}
		
		/*
		$qry = "SELECT avg(total) as Average
					FROM
					  (SELECT DATE_FORMAT(i.profilecreateddate, '%Y-%m') as 'period',
							  COUNT(idapplicant) as 'total',
							  i.appmemberid, i.appmemberno
						 FROM applicant i where i.appmemberid='$memberid' and i.appmemberno='$memberno' and Year(i.profilecreateddate) = EXTRACT(YEAR FROM NOW()) and i.profilestatus = 'C' and i.appresult != '' and i.appresult != 'E' $prty
						 GROUP BY period 
						 HAVING COUNT(idapplicant) > 0 
					  ) as T1
					  group by appmemberid, appmemberno  
					  
					  UNION ALL
					  
					SELECT (avg(total2) / count(total)) as prequal
						FROM
						  (SELECT DATE_FORMAT(i.profilecreateddate, '%Y-%m') as 'period',
								  COUNT(idapplicant) as 'total',
								  i.appmemberid, i.appmemberno
							 FROM applicant i where i.appmemberid='$memberid' and i.appmemberno='$memberno' and Year(i.profilecreateddate) = EXTRACT(YEAR FROM NOW()) and i.profilestatus = 'C' and i.appresult != '' and i.appresult != 'E' $prty
							 GROUP BY period 
							 HAVING COUNT(idapplicant) > 0
						  ) as T1, 
						  (SELECT DATE_FORMAT(i.profilecreateddate, '%Y-%m') as 'period',
								  COUNT(idapplicant) as 'total2',
								  i.appmemberid, i.appmemberno
							 FROM applicant i where i.appmemberid='$memberid' and i.appmemberno='$memberno' and Year(i.profilecreateddate) = EXTRACT(YEAR FROM NOW()) and i.profilestatus = 'C' and i.appresult='A'  $prty
						  ) as T2
					
					UNION ALL
					  
					SELECT (avg(total2) / count(total)) as prequal
						FROM
						  (SELECT DATE_FORMAT(i.profilecreateddate, '%Y-%m') as 'period',
								  COUNT(idapplicant) as 'total',
								  i.appmemberid, i.appmemberno
							 FROM applicant i where i.appmemberid='$memberid' and i.appmemberno='$memberno' and Year(i.profilecreateddate) = EXTRACT(YEAR FROM NOW()) and i.profilestatus = 'C' and i.appresult != '' and i.appresult != 'E' $prty
							 GROUP BY period 
							 HAVING COUNT(idapplicant) > 0
						  ) as T1, 
						  (SELECT DATE_FORMAT(i.profilecreateddate, '%Y-%m') as 'period',
								  COUNT(idapplicant) as 'total2',
								  i.appmemberid, i.appmemberno
							 FROM applicant i where i.appmemberid='$memberid' and i.appmemberno='$memberno' and Year(i.profilecreateddate) = EXTRACT(YEAR FROM NOW()) and i.profilestatus = 'C' and i.appresult='C'  $prty
						  ) as T2
					  
					  UNION ALL
					  
					SELECT (avg(total2) / count(total)) as prequal
						FROM
						  (SELECT DATE_FORMAT(i.profilecreateddate, '%Y-%m') as 'period',
								  COUNT(idapplicant) as 'total',
								  i.appmemberid, i.appmemberno
							 FROM applicant i where i.appmemberid='$memberid' and i.appmemberno='$memberno' and Year(i.profilecreateddate) = EXTRACT(YEAR FROM NOW()) and i.profilestatus = 'C' and i.appresult != '' and i.appresult != 'E' $prty
							 GROUP BY period 
							 HAVING COUNT(idapplicant) > 0
						  ) as T1, 
						  (SELECT DATE_FORMAT(i.profilecreateddate, '%Y-%m') as 'period',
								  COUNT(idapplicant) as 'total2',
								  i.appmemberid, i.appmemberno
							 FROM applicant i where i.appmemberid='$memberid' and i.appmemberno='$memberno' and Year(i.profilecreateddate) = EXTRACT(YEAR FROM NOW()) and i.profilestatus = 'C' and i.appresult='D'  $prty
						  ) as T2
					  
					  UNION ALL  
  
					SELECT avg(avgincome) as Average
					FROM
					  (SELECT DATE_FORMAT(i.profilecreateddate, '%Y-%m') as 'period',
							  SUM(appincome) as 'total',
							  i.appmemberid, i.appmemberno, (SUM(i.appincome) / COUNT(i.idapplicant)) as avgincome
						 FROM applicant i where i.appmemberid='$memberid' and i.appmemberno='$memberno' and profilestatus = 'C' and i.appresult != '' and i.appresult != 'E' $prty  AND profilecreateddate BETWEEN '$datestart' AND '$dateend'
						 GROUP BY period 
						 HAVING COUNT(idapplicant) > 0 
					  ) as T1
					  group by appmemberid, appmemberno
					  
					  UNION ALL
					  
					  SELECT (sum(appscore) / COUNT(idapplicant)) as avgscore
					  FROM applicant i 
					  where i.appmemberid='$memberid' and i.appmemberno='$memberno' and i.profilestatus = 'C' and i.appresult != '' and i.appresult != 'E' $prty  AND profilecreateddate BETWEEN '$datestart' AND '$dateend';";
					  */
		$qry = "SELECT avg(avgincome) as Average
					FROM
					  (SELECT DATE_FORMAT(i.profilecreateddate, '%Y-%m') as 'period',
							  SUM(appincome) as 'total',
							  i.appmemberid, i.appmemberno, (SUM(i.appincome) / COUNT(i.idapplicant)) as avgincome
						 FROM applicant i where i.appmemberid='$memberid' and i.appmemberno='$memberno' and profilestatus = 'C' and i.appresult != '' and i.appresult != 'E' $prty  AND profilecreateddate BETWEEN '$datestart' AND '$dateend'
						 GROUP BY period 
						 HAVING COUNT(idapplicant) > 0 
					  ) as T1
					  group by appmemberid, appmemberno
					  
					  UNION ALL
					  
					  SELECT (sum(appscore) / COUNT(idapplicant)) as scoreavg
					  FROM applicant i 
					  where i.appmemberid='$memberid' and i.appmemberno='$memberno' and i.profilestatus = 'C' and i.appresult != '' and i.appresult != 'E' $prty  AND profilecreateddate BETWEEN '$datestart' AND '$dateend';";
		
		$result = mysqli_query($this->connection,$qry);
		$count = 0;
    
        if(mysqli_affected_rows($this->connection) <= 0)
        {
			$this->HandleError("Unable to grab Averages");
			mysqli_close($this->connection);
            return [0, 0];
		}
		
		while($row = mysqli_fetch_assoc($result))
		{
			array_push($ar, round(floatval($row['Average'])));
			$count++;
		}
		
		mysqli_close($this->connection);
		/*
		if($count < 6)
		{
			return [0, 0, 0, 0, 0, 0];
		}*/
		if($count < 2)
		{
			return [0, 0];
		}
		return $ar;
		
	}
	
	function Members()
	{
		if(!$this->DBLogin())
        {
            $this->HandleError("Database login failed!");
            return false;
        }
		
		$ar = [];
		
		$qry = "SELECT * FROM prequal.members;";
		
		$result = mysqli_query($this->connection,$qry);
		
		if(!$result || mysqli_num_rows($result) <= 0)
        {
            $this->HandleError("Could not grab members from database");
			mysqli_close($this->connection);
            return false;
        }
		
		while($row = mysqli_fetch_assoc($result))
		{
			array_push($ar, $row);  
		}
		
		mysqli_close($this->connection);
		
		return $ar;
	}
	
	function set_timezone()
	{
		
		$timezone = "America/Los_Angeles"; // Pacific time 
		if(function_exists('date_default_timezone_set'))
		{ 
			date_default_timezone_set($timezone); 
		}
		
		//$result = mysqli_query($this->connection, "SET 'time_zone' = '".date('P')."';");
		
	}
	
	//AO--------------------------------------------------------------------------------------------
    
	//---Authenticate Token and pulled property/membership record (Added by Rizwan 05/04/2016) --- 

	function AuthenticateToken($propertyToken)
    {
		if(!$this->DBLogin())
        	{
            	  $this->HandleError("Database login failed!");
            	  return false;
        	}

 	    $qry = 'Select * from property where PropertyToken = "' .$propertyToken. '" ' ; 
        		
        $result = mysqli_query($this->connection,$qry);
        
        if(!$result || mysqli_num_rows($result) <= 0)
        {
			if ( session_status() === PHP_SESSION_NONE )
			 {
			   session_start();
             }
			
            $this->HandleError("Property Token Authentication Error. Unable to match the provided token.");
			mysqli_close($this->connection);
            return false;
        
        } else 
        
        {
		// start the session
		    
		   $row = mysqli_fetch_assoc($result);
           		   
		   if ( session_status() === PHP_SESSION_NONE )
		    {
             session_start();
            }
			
		   $unitType = "";
		        
           $_SESSION['s_Token']     = $row['PropertyToken'];
           $_SESSION['s_MemberId']  = $row['PropertyMemberId'];
		   $_SESSION['s_MemberNo']  = $row['PropertyMemberNo'];
		   $_SESSION['s_PropertyId']= $row['idproperty'] ;
		   $_SESSION['s_PropertyName']= $row['PropertyName'];
		   $_SESSION['s_Address1']  = $row['PropertyAddress1'];
		   $_SESSION['s_Address2']  = $row['PropertyAddress2'];
		   $_SESSION['s_City']      = $row['PropertyCity'];
		   $_SESSION['s_State']     = $row['PropertyState'];
		   $_SESSION['s_Zip']       = $row['PropertyZip'];
		   $_SESSION['s_Phone']     = $row['PropertyPhone'];
		   $_SESSION['s_Email1']    = $row['PropertyEmail1'];
		   $_SESSION['s_Schedule']  = $row['PropertySchedule'];
		   $_SESSION['s_onlineweburl'] = $row['onlineweburl'];
		   $_SESSION['s_propertytype'] = $row['PropertyType'];
		   $_SESSION['s_singlefamilyrent'] = $row['singlefamilyrent'];
		   
		   // build the available bedroom type and save to session
		   if($row['Studio'] == 'Y' || $row['OneBedroom'] == 'Y' || $row['TwoBedroom'] == 'Y' || $row['ThreeBedroom'] == 'Y' || $row['FourBedroom'] == 'Y' )
		   {
			
			if($row['PropertyType'] == 'S')
			{
							
				if($row['Studio'] == 'Y')
				{
					$unitType .= '<option value="S" selected>Studio</option>' ;
				}	
				if($row['OneBedroom'] == 'Y')
				{
					$unitType .= '<option value="1" selected>1 Bedroom</option>' ;
				}
				if($row['TwoBedroom'] == 'Y')
				{
					$unitType .= '<option value="2" selected>2 Bedroom</option>' ;
				}
				if($row['ThreeBedroom'] == 'Y')
				{
					$unitType .= '<option value="3" selected>3 Bedroom</option>' ;
				}
				if($row['FourBedroom'] == 'Y')
				{
					$unitType .= '<option value="4" selected>4+ Bedroom</option>';
				}
				
			}
			else
			{
				$unitType .= '<option value="" selected>Select</option>' ;
				
				if($row['Studio'] == 'Y')
				{
					$unitType .= '<option value="S">Studio</option>' ;
				}	
				if($row['OneBedroom'] == 'Y')
				{
					$unitType .= '<option value="1">1 Bedroom</option>' ;
				}
				if($row['TwoBedroom'] == 'Y')
				{
					$unitType .= '<option value="2">2 Bedroom</option>' ;
				}
				if($row['ThreeBedroom'] == 'Y')
				{
					$unitType .= '<option value="3">3 Bedroom</option>' ;
				}
				if($row['FourBedroom'] == 'Y')
				{
					$unitType .= '<option value="4">4+ Bedroom</option>';
				}
			}
			
		   } else
		   
		   {
			 // set default value here
			 $unitType .= '<option value="" selected>Select</option>' ;
			 $unitType .= '<option value="S">Studio</option>' ;
			 $unitType .= '<option value="1">1 Bedroom</option>' ;
			 $unitType .= '<option value="2">2 Bedroom</option>' ;
			 $unitType .= '<option value="3">3 Bedroom</option>' ;
			 $unitType .= '<option value="4">4+ Bedroom</option>' ;
		   }
		   
		   $_SESSION['s_UnitType']  = $unitType ;
		   
		   mysqli_close($this->connection);
			
           return true ;  
        }

	}
	
	/* Rizwan added on 9/21/2016 to authenticate the Member token and property id from partner site like Tricon American Homes
	 * The process will validate the membersip token and then pull the property token using the membership info and external property id. 
    */

	function AuthenticateMemberToken($memberToken)
    {
		if(!$this->DBLogin())
        	{
              $this->HandleError("Database login failed!");
              return false;
        	}

 	    $qry = 'Select * from members where MemberToken = "' .$memberToken. '" ' ; 
        		
        $result = mysqli_query($this->connection,$qry);
        
        if(!$result || mysqli_num_rows($result) <= 0)
        {
						
            $this->HandleError("Member Token Authentication Error. Unable to match the provided token.");
			mysqli_close($this->connection);
            return false;
        
        } else 
        
        {
			
		  $row = mysqli_fetch_assoc($result);
		  mysqli_close($this->connection) ;
		  
		  return $row ;
		  
		}
	}		
	function PullexternalProperty($memberid, $memberno, $externalid)
	{		
		if(!$this->DBLogin())
        	{
              $this->HandleError("Database login failed!");
              return false;
        	}

		$qry = "SELECT * FROM property WHERE PropertyMemberId = '"
		. $this->SanitizeForSQL($this->connection, $memberid) . "' and PropertyMemberNo = '"
		. $this->SanitizeForSQL($this->connection, $memberno) . "' and propertyexternalid = '"
		. $this->SanitizeForSQL($this->connection, $externalid) . "';";
		 	    
        $result = mysqli_query($this->connection,$qry);
		
		if(!$result || mysqli_num_rows($result) <= 0)
		{
		  return false ;
		}
		else
		{
		  $propertyResult  = mysqli_fetch_assoc($result);
		  mysqli_close($this->connection) ;
		  return $propertyResult ;
		}
	}
	
	
    /*
    Sanitize() function removes any potential threat from the
    data submitted. Prevents email injections or any other hacker attempts.
    if $remove_nl is true, newline chracters are removed from the input.
    */
    function Sanitize($str,$remove_nl=true)
    {
        $str = $this->StripSlashes($str);

        if($remove_nl)
        {
            $injections = array('/(\n+)/i',
                '/(\r+)/i',
                '/(\t+)/i',
                '/(%0A+)/i',
                '/(%0D+)/i',
                '/(%08+)/i',
                '/(%09+)/i'
                );
            $str = preg_replace($injections,'',$str);
        }

        return $str;
    }
	
	function SanitizeForSQL($link,$str)
    {
        if( function_exists( "mysqli_real_escape_string" ) )
        {
              $ret_str = mysqli_real_escape_string($link, $str );
        }
        else
        {
              $ret_str =  $str ;
        }
        return $ret_str;
    }
	
    function StripSlashes($str)
    {
        if(get_magic_quotes_gpc())
        {
            $str = stripslashes($str);
        }
        return $str;
    }  
    
	//--------- Resident Registration Functions -------------
	
	function RegisterResident()
    {
        if(!isset($_POST['submitted']))
        {
		   error_log("RegisterResident: out from step-1", 0);
           return false;
        }
        
        $formvars = array();
        
        if(!$this->ValidateRegistrationSubmission())
        {
			error_log("RegisterResident: out from step-2", 0);
            return false;
        }
        
        $this->CollectRegistrationSubmission($formvars);
        
        if(!$this->SaveToDatabase($formvars))
        {
			error_log("RegisterResident: out from step-3", 0);
            return false;
        }
        
       // if(!$this->SendUserConfirmationEmail($formvars))
       // {
       //     return false;
       // }

       // $this->SendAdminIntimationEmail($formvars);
        
        return true;
    }
	
	/*  -------------------------------------------------------------------------------------
	    update the residents - the functions called from 2nd step of application registration
	    updateResidents() - Called from applicantform-step-02.php
	    Rizwan: 05/11/2016 
	    -------------------------------------------------------------------------------------
    */
	function updateResident()
    {
        if(!isset($_POST['submitted']))
        {
		   error_log("updateResident: out from step-1", 0);
           return false;
        }
        
        $formvars    = array();
		        
        if(!$this->ValidateupdateSubmission())
        {
			error_log("updateResident: out from step-2", 0);
            return false;
        }
        
        $this->CollectupdateSubmission($formvars);
        
        if(!$this->updateToDatabase($formvars))
        {
			error_log("updateResident: out from step-3", 0);
            return false;
        }
                      
        return true;
    }

	
	function ValidateRegistrationSubmission()
    {
        $digit = $_SESSION['digit'] ;
		 
        $validator = new FormValidator();
		
		$validator->addValidation("FirstName","req","First name is required.");
        $validator->addValidation("LastName","req","Last name is required.");
        $validator->addValidation("inputEmail3","req","Email is required.");
        $validator->addValidation("inputEmail3","email","Please enter the correct email");
        $validator->addValidation("inputEmail4","req","Confirmation email is required.");
        $validator->addValidation("inputEmail4","email","Please enter the correct confirmation email.");
		$validator->addValidation("captcha","req","Incorrect captcha code is provided");
		$validator->addValidation("password","req","Password is required");
		$validator->addValidation("txtConfirmPassword","req","Password confirmation is required");
				
        if(!$validator->ValidateForm())
        {
            $error='';
            $error_hash = $validator->GetErrors();
            foreach($error_hash as $inpname => $inp_err)
            {
                $error .= $inpname.':'.$inp_err."\n";
            }
            $this->HandleError($error);
			return false;
        }
		
		if($_POST['captcha'] != $digit )
		{
			$this->error_message = 'Incorrect Security Code entered.' ;
			return false;
		}
		if($_POST['password'] != $_POST['txtConfirmPassword']  )
		{
			$this->error_message = 'Password do not match!. Please renter.' ;
			return false;
		}
		
        return true;
    }
    
	function ValidateupdateSubmission()
    { 
        $validator = new FormValidator();
		
		$validator->addValidation("dateofbirth","req","Date of Birth is required.");
		$validator->addValidation("Phone","req","Phone number is required.");
        $validator->addValidation("Address","req","Address is required.");
        $validator->addValidation("City","req","City is required");
        $validator->addValidation("State","req","State code is required.");
        $validator->addValidation("Zip","req","Zip code is required.");
		$validator->addValidation("Income","req","Monthly income is required");
		$validator->addValidation("DesireUnit","req","Unit size is required");
		$validator->addValidation("Rent","req","Desire Maximum Monthly Rent is required");
		$validator->addValidation("Movedate","req","Expected move-in date is required");
				
        if(!$validator->ValidateForm())
        {
            $error='';
            $error_hash = $validator->GetErrors();
            foreach($error_hash as $inpname => $inp_err)
            {
                $error .= $inpname.':'.$inp_err."\n";
            }
            $this->HandleError($error);
			return false;
        }
					
        return true;
    }
	
    function CollectRegistrationSubmission(&$formvars)
    {
        $formvars['FirstName']   = $this->Sanitize($_POST['FirstName']);
        $formvars['LastName']    = $this->Sanitize($_POST['LastName']);
        $formvars['inputEmail3'] = $this->Sanitize($_POST['inputEmail3']);
        $formvars['password']    = $this->Sanitize($_POST['password']);    
    }
    
	function CollectupdateSubmission(&$formvars)
    {
        $formvars['MidName']   = $this->Sanitize($_POST['MidName']);
        $formvars['Suffix']    = $this->Sanitize($_POST['Suffix']);
		$formvars['dateofbirth']  = $this->Sanitize($_POST['dateofbirth']) ;
        $formvars['Phone']     = $this->Sanitize($_POST['Phone']);
        $formvars['Address']   = $this->Sanitize($_POST['Address']);
		$formvars['Unit']      = $this->Sanitize($_POST['Unit']);
		$formvars['City']      = $this->Sanitize($_POST['City']);
		$formvars['State']     = $this->Sanitize($_POST['State']);
		$formvars['Zip']       = $this->Sanitize($_POST['Zip']);
		$formvars['Income']    = $this->Sanitize($_POST['Income']);
		$formvars['Rent']      = $this->Sanitize($_POST['Rent']);
		$formvars['DesireUnit']= $this->Sanitize($_POST['DesireUnit']);
		$formvars['Movedate']  = $this->Sanitize($_POST['Movedate']);
	    $formvars['Message']   = $this->Sanitize($_POST['Message']);
		$formvars['Freezpin']  = $this->Sanitize($_POST['Freezpin']);
    }
	
	function SaveToDatabase(&$formvars)
    {
        if(!$this->DBLogin())
        {
			$this->HandleError("Database login failed!");
            return false;
        }
        
                    
        if(!$this->InsertIntoDB($formvars))
        {
			error_log("step-4 : Resident Registration failed - Database insert failed!");
            $this->HandleError("Resident Registration failed - Database insert failed!");
			mysqli_close($this->connection);
            return false;
        }
		
		mysqli_close($this->connection);
        return true;
    }
	
	function updateToDatabase(&$formvars)
    {
        if(!$this->DBLogin())
        {
			error_log("Update Registration - Database login failed!");
			$this->HandleError("Database login failed!");
			return false;
        }
        
                    
        if(!$this->UpdateIntoDB($formvars))
        {
			error_log("step-4 : Update Registration failed - Database update failed!");
            $this->HandleError("Update Registration failed - Database update failed!");
            return false;
        }

        return true;
    }
	
	
	function InsertIntoDB(&$formvars)
    {
		if(!$this->DBLogin())
        {
			error_log("Update Registration - Database login failed!");
			$this->HandleError("Database login failed!");
			return false;
        }
		
        $status = 'I' ;
		
        $insert_query = 'insert into applicant (
		applicantemail,
        apppassword,
        appfirstname,
        applastname,
        appmemberid,
        appmemberno,
		apppropertyid,
		profilecreateddate,
        profilestatus
		)
        values
        (
 		"' . $this->SanitizeForSQL($this->connection,$formvars['inputEmail3']) . '",
        "' . md5($formvars['password']) . '",
        "' . $this->SanitizeForSQL($this->connection,$formvars['FirstName']) . '",
		"' . $this->SanitizeForSQL($this->connection,$formvars['LastName']) . '",
        "' . $_SESSION['s_MemberId']  .'" ,
		"' . $_SESSION['s_MemberNo']  .'" ,
		"' . $_SESSION['s_PropertyId']  .'" ,
		NOW(),
		"' . $status. '" 
		)' ;      
        if(!mysqli_query($this->connection,$insert_query ))
        {
			error_log("step-5: QUERY FAILED");
            $this->HandleDBError("Error inserting data to the table\nquery:$insert_query");
			mysqli_close($this->connection);
            return false;
        }
		
		// obtain the last insert id
		   $applicantID = mysqli_insert_id ($this->connection) ;
	    // record digits in session variable
           $_SESSION['applicantID'] = $applicantID;
		   $_SESSION['FirstName']   = $formvars['FirstName'] ;
		   $_SESSION['LastName']    = $formvars['LastName'] ;
		   $_SESSION['AppEmail']    = $formvars['inputEmail3'] ;
		   
		   mysqli_close($this->connection);
        return true;
    }
	
	function UpdateIntoDB(&$formvars)
    {
		if(!$this->DBLogin())
        {
			error_log("Update Registration - Database login failed!");
			$this->HandleError("Database login failed!");
			return false;
        }
		
        $applicantID = $_SESSION['applicantID'] ;
		$AppEmail    = $_SESSION['AppEmail'] ;
		
        $date = date('Y-m-d H:i:s');
        $status = 'I' ;
		$date = $formvars['Movedate'] ;
		$dobdate = $formvars['dateofbirth'] ;
        $move_date = date("Y-m-d", strtotime($date));			
		$dob_date  = date("Y-m-d", strtotime($dobdate));
		
		$update_query = 'update applicant set
		               appmidname     = "' . $this->SanitizeForSQL($this->connection,$formvars['MidName']) . '",
		               appsuffix      = "' . $this->SanitizeForSQL($this->connection,$formvars['Suffix']) . '",
		               appaddress     = "' . $this->SanitizeForSQL($this->connection,$formvars['Address']) . '",
		               appaddressunit = "' . $this->SanitizeForSQL($this->connection,$formvars['Unit']) . '",
				       appcity        = "' . $this->SanitizeForSQL($this->connection,$formvars['City']) . '",
				       appstate       = "' . $this->SanitizeForSQL($this->connection,$formvars['State']) . '",
				       appzip         = "' . $this->SanitizeForSQL($this->connection,$formvars['Zip']) . '",
					   appdob         = "' . $this->SanitizeForSQL($this->connection,$dob_date) . '",
				       appphone       = "' . $this->SanitizeForSQL($this->connection,$formvars['Phone']) . '",
				       appincome      = "' . $this->SanitizeForSQL($this->connection,$formvars['Income']) . '",
					   monthlyrent    = "' . $this->SanitizeForSQL($this->connection,$formvars['Rent']) . '",
					   appmoveindate  = "' . $this->SanitizeForSQL($this->connection,$move_date) . '",
					   desireunittype = "' . $this->SanitizeForSQL($this->connection,$formvars['DesireUnit']) . '",
					messagetoproperty = "' . $this->SanitizeForSQL($this->connection,$formvars['Message']) . '",
					   appsecuritypin = "' . $this->SanitizeForSQL($this->connection,$formvars['Freezpin']) . '"
				   where
				   idapplicant    = "' .$applicantID.'" and
				   applicantemail = "' .$AppEmail. '" ' ;
		
            
        if(!mysqli_query($this->connection,$update_query))
        {
			error_log("step-5: Update query failed");
            $this->HandleDBError("Error updating data to the table\nquery:$update_query");
			mysqli_close($this->connection);
            return false;
        }
		
		mysqli_close($this->connection);		
        return true;
    }
	
	/* --------------------------------------------------------------------------------------------------------------
	    RetrieveProperty Info using the Memberid, Memberno and property id
	    RetrieveProperty() ;
	    Rizwan: 05/125	   
	   --------------------------------------------------------------------------------------------------------------
    */
	function RetrieveProperty($MemberId, $MemberNo, $PropertyId)
	{
		if(!$this->DBLogin())
        	{
            	  $this->HandleError("Database login failed!");
            	  return false;
        	}
					
		$query = "SELECT * FROM prequal.property WHERE idproperty = '{$PropertyId}' AND PropertyMemberId = '{$MemberId}' AND PropertyMemberNo = '{$MemberNo}';";
		
		$result = mysqli_query($this->connection, $query);
		
		if(!$result || mysqli_num_rows($result) <= 0)
        {
            $this->HandleError("Authorization Error. Unable to match the provided key");
			mysqli_close($this->connection);
            return false;   
        }
		
		$propertyresult = mysqli_fetch_assoc($result);
		mysqli_close($this->connection);
		return $propertyresult  ;
				
	}
	
	/* --------------------------------------------------------------------------------------------------------------
	    Retrieve Application Info using the Application id and applicant email
	    RetrieveResident() ;
	    Rizwan: 05/12/2016	   
	   --------------------------------------------------------------------------------------------------------------
    */
	
	function RetrieveResident()
    {
				
        if(!$this->DBLogin())
        {
            $this->HandleError("Database login failed!");
            return false;
        }   
        
		$appID = $_SESSION['applicantID'] ;
		$email = $_SESSION['AppEmail'] ;
        
        $query = "Select * from applicant where idapplicant ='" . $appID . "' and applicantemail = '" . $email . "' " ;
		
		$result = mysqli_query($this->connection,$query);

        if(!$result || mysqli_num_rows($result) <= 0)
        {
            $this->HandleError("There is no applicantion associated with email: $email");
			mysqli_close($this->connection);
            return false;
        }
		
        $approw = mysqli_fetch_assoc($result) ;
		
        mysqli_close($this->connection);
        return $approw;
    }
  	
	function RetrieveApplicant($appID)
    {
				
        if(!$this->DBLogin())
        {
            $this->HandleError("Database login failed!");
            return false;
        }   
        
		       
        $query = "Select * from applicant where idapplicant ='" . $appID . "' " ;
		
		$result = mysqli_query($this->connection,$query);

        if(!$result || mysqli_num_rows($result) <= 0)
        {
            $this->HandleError("There is no applicantion associated with applicant id: $appID");
			mysqli_close($this->connection);
            return false;
        }
		
        $approw = mysqli_fetch_assoc($result) ;
		
        mysqli_close($this->connection);
        return $approw;
    }
	
	
	function UpdateApplicationStatus($AppId,$AppEmail,$Status,$runcriminal) 
    {
		if(!$this->DBLogin())
        {
            $this->HandleError("Database login failed!");
            return false;
        }
		
		$update_query = 'update applicant set
		                 profilestatus  = "' . $Status . '" ,
					     submitdate     = now() ,
					     responsestatus = "" ,
						 runcriminal    = "' . $runcriminal . '"
				   where
				   idapplicant    = "' .$AppId.'" and
				   applicantemail = "' .$AppEmail. '" ' ;
		           
        if(!mysqli_query($this->connection,$update_query))
        {
			error_log("Function UpdateApplicationStatus: Update query failed");
            $this->HandleDBError("Error updating data to the table\nquery:$update_query");
			mysqli_close($this->connection);
            return false;
        }
		
		mysqli_close($this->connection);		
        return true;
    }
	function UpdateApplicationErrorStatus($AppId,$AppEmail,$Status,$Errormsg) 
    {
		if(!$this->DBLogin())
        {
            $this->HandleError("Database login failed!");
            return false;
        }
		
		$update_query = 'update applicant set
		                 profilestatus  = "' . $Status . '" ,
					     responsedate   = now() ,
					     responsestatus = "' . $Errormsg .'" 						  
				   where
				   idapplicant    = "' .$AppId.'" and
				   applicantemail = "' .$AppEmail. '" ' ;
		           
        if(!mysqli_query($this->connection,$update_query))
        {
			error_log(print_r($update_query,true));
			error_log("Function UpdateApplicationErrorStatus: Update query failed");
            $this->HandleDBError("Error updating data to the table\nquery:$update_query");
			mysqli_close($this->connection);
            return false;
        }
		
		mysqli_close($this->connection);		
        return true;
    }
	
	function UpdateApplicationResult($AppId,$AppEmail,$Status,$respMessage,$respInqno,$respOverall,$respNocredit,$respIncome,$landlordcollection,$pastdueutilities,$totaleviction,$apprentqualify,$respFico,$respFicoscore,$respMaxrent,$respObligations,$offer1,$offer2,$offer3,$offer4,$offer5,$offer6,$offer7,$respRentratio,$deprequirement) 
    {
			
		if(!$this->DBLogin())
        {
            $this->HandleError("Database login failed!") ;
            return false;
        }
		
		$update_query = 'update applicant set
		                 profilestatus  = "' . $Status . '" ,
					     responsedate   = now() ,
						 responseid     = "' . $respInqno . '" ,
						 appresult      = "' . $respOverall . '" ,
						 responsestatus = "' . $respMessage . '" ,
						 appresult      = "' . $respOverall . '" ,
						 landlordcollection = "' . $landlordcollection . '" , 
						 pastdueutilities = "' . $pastdueutilities . '" ,
						 totaleviction    = "' . $totaleviction . '" , 
						 apprentqualify   = "' . $apprentqualify . '" ,
						 appscore         = "' . $respFicoscore . '" , 
						 maxrentqualify   = "' . $respMaxrent . '" , 
						 monthlyobligation= "' . $respObligations . '" ,
						 offer1 = "' . $offer1 . '" ,
						 offer2 = "' . $offer2 . '" ,
						 offer3 = "' . $offer3 . '" ,
						 offer4 = "' . $offer4 . '" ,
						 offer5 = "' . $offer5 . '" ,
						 offer6 = "' . $offer6 . '" ,
						 offer7 = "' . $offer7 . '" ,
						 resprentratio = "' . $respRentratio . '" ,
						 depositrequirement = "' . $deprequirement . '" ,
						 scoredecision = "' . $respFico . '" ,
						 nocreditreport= "' . $respNocredit . '" ,
						 rentratiodecision= "' . $respIncome . '" 
				   where
				   idapplicant    = "' .$AppId.'" and
				   applicantemail = "' .$AppEmail. '" ' ;
		           
        if(!mysqli_query($this->connection,$update_query))
        {
			error_log(print_r($update_query,true));
			error_log("Function UpdateApplicationResult: Update query failed");
            $this->HandleDBError("Error updating data to the table\nquery:$update_query");
			mysqli_close($this->connection);
            return false;
        }
		
		mysqli_close($this->connection);		
        return true;
    }
	
	// Function UpdateCriminalStatus added Rizwan on 7/31/2016
	function UpdateCriminalStatus($AppId,$userid,$CrimInqno,$Status) 
    {
		if(!$this->DBLogin())
        {
            $this->HandleError("Database login failed!");
            return false;
        }
		
		$update_query = 'update applicant set
		                 criminqtransno = "' . $CrimInqno . '" ,
		                 criminalresult = "' . $Status . '" ,
					     crimrundate    = now() ,
					     crimrunuser    = "' . $userid . '"
				   where
				   idapplicant    = "' .$AppId.'" ' ;  
		           
        if(!mysqli_query($this->connection,$update_query))
        {
			error_log(print_r($update_query,true));
			error_log("Function UpdateCriminalStatus: Update query failed");
            $this->HandleDBError("Error updating data to the table\nquery:$update_query");
			mysqli_close($this->connection);
            return false;
        }
		
		mysqli_close($this->connection);		
        return true;
    }
	
	// Function UpdateCriminalResult added Rizwan on 8/4/2016
	function UpdateCriminalResult($AppId,$responseId,$crimInqno,$Status,$Resultpdf) 
    {
		if(!$this->DBLogin())
        {
            $this->HandleError("Database login failed!");
            return false;
        }
		
		$update_query = 'update applicant set
		                 criminalresult = "' . $Status . '",
						 crimresultpdf  = "' . $Resultpdf. '"
				   where
				   idapplicant   = "' .$AppId.'"  and
				   responseid    = "' .$responseId.'" and
				   criminqtransno= "' .$crimInqno.'"' ;
				   
        if(!mysqli_query($this->connection,$update_query))
        {
			error_log(print_r($update_query,true));
			error_log("Function UpdateCriminalStatus: Update query failed");
            $this->HandleDBError("Error updating data to the table\nquery:$update_query");
			mysqli_close($this->connection);
            return false;
        }
		
		mysqli_close($this->connection);		
        return true;
    }
	
	// Add Criminal notification - Rizwan added on 8/8/2016
	function AddNotification($memberid,$memberno,$notifytype,$notifytitle,$notifymessage) 
    {
		if(!$this->DBLogin())
        {
            $this->HandleError("Database login failed!");
            return false;
        }
		
	    $qry = "insert into prequal.notifications (notifymemberid, notifymemberno, notifydate, notifytype,
		notifytitle, notifydetails, notifyreadflag)
		VALUES ('$memberid', '$memberno',
		NOW(), '$notifytype', '$notifytitle', '$notifymessage', '');";
		
		$result = mysqli_query($this->connection,$qry);
	}
	/* ================================================================================== */
	
	/* Generic email sender function */

	function SendEmail($sendtoemailaddress,$emailsubject,$messageToSend)
    { 
	
	$mailer = new PHPMailer();
                
	$mailer->IsSMTP();  
	$mailer->CharSet = 'utf-8';                       	  // Set mailer to use SMTP
	$mailer->Host = 'localhost';   	 // Specify main and backup server
	//$mailer->Host = 'smtpout.secureserver.net';   	 // Specify main and backup server
	//$mailer->Host = '10.1.4.17';
	//$mailer->SMTPAuth = true;                       	// Enable SMTP authentication
	//$mailer->Username = 'support@residentprequal.com';  // SMTP username
	//$mailer->Password = '$pSecofr23';                	      // SMTP password
	//$mailer->SMTPSecure = 'tls';
	$mailer->Port =25;                          	     // Enable encryption, 'ssl' also accepted

	
	$mailer->From = 'support@residentprequal.com'; 
	$mailer->FromName = 'Prequalifier Support Team';
 	$mailer->AddAddress($sendtoemailaddress);                      		// Add a recipient

	$mailer->AddReplyTo('support@residentprequal.com', 'Information');
	$mailer->Subject = $emailsubject;

	$mailer->WordWrap = 50;                                    // Set word wrap to 50 characters
	//$mailer->AddAttachment('/var/tmp/file.tar.gz');         // Add attachments
	//$mailer->AddAttachment('/tmp/image.jpg', 'new.jpg');   // Optional name
	$mailer->IsHTML(true);                                  // Set email format to HTML
         
         
        $mailer->Body = $messageToSend ;
         

        if(!$mailer->Send())
        {
            return false;
        }
        return true;
    }
	
	//email confirm to user 05/18/2016 AO
    function SendUserConfirmationEmail($email, $verifycode)
    {
        
        $EmailSubject      = "Your Verification Code from ".$this->sitename;
		$sendtoemailaddress= $email ;  
        $emailTitle        = "Confirm Your Identity to Get Started";
 	
        $confirmcode   = $verifycode;
        $confirm_url   = '';
 
        $Emailmessage  = "<p>Hello, </p>";
		$Emailmessage .= "<p>Thanks for using ".$this->sitename."</p>";
		$Emailmessage .= "Please use the code below and enter it to access your account on " . $this->sitename . ".<br /><br/ >";
		$Emailmessage .= "<b style='font-size:14px;'>" . $verifycode."</b><br />";
		$Emailmessage .= "<p>Note: Code will expire in 20 minutes.</p><br /><br />";
		$Emailmessage .= "<p>Regards,</p>";
		$Emailmessage .= "<p>Support Team<br />";
		$Emailmessage .= $this->sitename."</p>";

		include 'email_generator.php'; 
 
        if(!$this->SendEmail($sendtoemailaddress,$EmailSubject,$messageToSend))
        {
            $this->HandleError("Failed sending registration confirmation email.");
            return false;
        }
        return true;
    }
	
	//email temporary password 09/09/2016 AO
    function NewUserTmpPassword($email, $userid, $tmppassword)
    {
		//---!Email that send user info--------------!
		$EmailSubject      = "Resident Prequal Account";
		$sendtoemailaddress= $email ;  
        $emailTitle        = "An account has been created for you";

        //$confirmcode   = $verifycode;
        $confirm_url   = '';
 
        $Emailmessage  = "<p>Hello, </p>";
		//$Emailmessage .= "<p>Thanks for using ".$this->sitename."</p>";
		$Emailmessage .= "<p>An account has been created for you.</p>";
		$Emailmessage .= "Please use the User ID below and enter it to access your account on " . $this->sitename . ".<br /><br/ >";
		$Emailmessage .= "<b style='font-size:14px;'>" . $userid ."</b><br />";
		$Emailmessage .= "<p>Note: Another email has been sent with a Temporary Password.</p>";
		$Emailmessage .= "<p>Regards,</p>";
		$Emailmessage .= "<p>Support Team<br />";
		$Emailmessage .= $this->sitename."</p>";

		include 'email_generator.php'; 
 
        if(!$this->SendEmail($sendtoemailaddress,$EmailSubject,$messageToSend))
        {
            $this->HandleError("Failed sending registration confirmation email.");
            return false;
        } 
		//---!--------------------------------!
		
        //----$ Email that sends the temporary password----$
        $EmailSubject      = "Resident Prequal Temporary Password";
		$sendtoemailaddress= $email ;  
        $emailTitle        = "Your Temporary Password";

        //$confirmcode   = $verifycode;
        $confirm_url   = '';
 
        $Emailmessage  = "<p>Hello, </p>";
		//$Emailmessage .= "<p>Thanks for using ".$this->sitename."</p>";
		//$Emailmessage .= "<p>Here is your temporary password</p>";
		$Emailmessage .= "Please use the temporary password below and enter it to access your account on " . $this->sitename . ".<br /><br/ >";
		$Emailmessage .= "<b style='font-size:14px;'>" . $tmppassword ."</b><br />";
		$Emailmessage .= "<p>Note: Another email has been sent containing your User ID.</p>";
		$Emailmessage .= "<p>Regards,</p>";
		$Emailmessage .= "<p>Support Team<br />";
		$Emailmessage .= $this->sitename."</p>";

		include 'email_generator.php'; 
 
        if(!$this->SendEmail($sendtoemailaddress,$EmailSubject,$messageToSend))
        {
            $this->HandleError("Failed sending registration confirmation email.");
            return false;
        }
		//----$----------------------------$
		
        return true;
    }
	
	//email confirm to user 05/31/2016 rizwan alam
    function SendMemberConfirmationEmail($email)
    {
        
        $EmailSubject      = "Your Membership for " .$this->sitename. "has been approved." ;
		$sendtoemailaddress= $email ;  
        $emailTitle        = "Congratulations!";
 	
        $confirmcode   = $verifycode;
        $confirm_url   = '';
 
        $Emailmessage  = "<p>Hello, </p>";
		$Emailmessage .= "<p>Your membership has been approved and activated for ".$this->sitename."</p>";
		$Emailmessage .= "Please login to your account on " . $this->sitename . " to complete the inititial setup.<br /><br/ >";
		$Emailmessage .= "<p></p><br />";
		$Emailmessage .= "<p>Regards,</p>";
		$Emailmessage .= "<p>Support Team<br />";
		$Emailmessage .= $this->sitename."</p>";

		include 'email_generator.php'; 
 
        if(!$this->SendEmail($sendtoemailaddress,$EmailSubject,$messageToSend))
        {
            $this->HandleError("Failed sending registration confirmation email.");
            return false;
        }
        return true;
    }
	
	//email from index.php contanct info
	function SendContactEmail($name, $company, $numberunits, $email, $phone, $current_tsp)
    {
        
        $EmailSubject      = "Email from ".$this->sitename;
		$sendtoemailaddress= "info@residentprequal.com";
        $emailTitle        = "Contact Us";
 	
        $confirmcode   = '';
        $confirm_url   = '';
 
        $Emailmessage  = "<p>Name: " . $name . "<br>";
		$Emailmessage  .= "Company: " . $company . "<br>";
		$Emailmessage  .= "Number of Units: " . $numberunits . "<br>";
		$Emailmessage  .= "Email: " . $email . "<br>";
		$Emailmessage  .= "Phone: " . $phone . "<br>";
		$Emailmessage  .= "TSP Client: " . $current_tsp . "<br>";
		$Emailmessage .= "</p>";
		$Emailmessage .= "<p>Thank you </p>";

		include 'email_generator.php'; 
 
        if(!$this->SendEmail($sendtoemailaddress,$EmailSubject,$messageToSend))
        {
            $this->HandleError("Failed sending contact us email.");
            return false;
        }
		
        return true;
    }

    function GetAbsoluteURLFolder()
    {
        $scriptFolder = (isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] == 'on')) ? 'https://' : 'http://';
        $scriptFolder .= $_SERVER['HTTP_HOST'] . dirname($_SERVER['REQUEST_URI']);
        return $scriptFolder;
    }
    
    function SendAdminIntimationEmail(&$formvars)
    {
        if(empty($this->admin_email))
        {
            return false;
        }
        $mailer = new PHPMailer();
        
        $mailer->CharSet = 'utf-8';
        
        $mailer->AddAddress($this->admin_email);
        
        $mailer->Subject = "New registration: ".$formvars['name'];

        $mailer->From = $this->GetFromAddress();         
        
        $mailer->Body ="A new user registered at ".$this->sitename."\r\n".
        "Name: ".$formvars['name']."\r\n".
        "Email address: ".$formvars['email']."\r\n".
        "UserName: ".$formvars['username'];
        
        if(!$mailer->Send())
        {
            return false;
        }
        return true;
    }
    
    
    
    function IsFieldUnique($formvars,$fieldname,$tablefield)
    {
        $field_val = $this->SanitizeForSQL($formvars[$fieldname]);
        $qry = "select account_username from $this->tablename where $tablefield='".$field_val."'";
        $result = mysql_query($qry,$this->connection);   
        if($result && mysql_num_rows($result) > 0)
        {
            return false;
        }
        return true;
    }
	
	//--------- End of Resident Registration Functions --------
	
	function LogOut()
    {
        if (session_status() == PHP_SESSION_ACTIVE)
		{      
		  $sessionvar = $this->GetLoginSessionVar();       
		  $_SESSION[$sessionvar]=NULL;       
		  unset($_SESSION[$sessionvar]);
		  session_destroy();
		}
    }
    
	/*=============================================================================*/
	/* Access the user using the email and verify that account was verified earlier */
	/*=============================================================================*/
	function GetUserFromEmail($email)
    {
        if(!$this->DBLogin())
        {
            $this->HandleError("Database login failed!");
            return false;
        }
		
        $email = $this->SanitizeForSQL($this->connection,$email);
        
        $result = mysqli_query($this->connection,"Select * from prequal.emailverify where loginemail='$email'");  

        if(!$result || mysqli_num_rows($result) <= 0)
        {
         		   
			$newcode=mt_rand(1000,10000) ;
			// create a verification record			
			$insert_query = 'insert into prequal.emailverify (
			loginemail,
			generatedcode,
			generationtime,
			verified
			)
			values
			(
			"' . $this->SanitizeForSQL($this->connection,$email) . '",
			"' . $newcode . '",
			NOW(),
			"")';      
			if(!mysqli_query($this->connection,$insert_query ))
			{
				$this->HandleDBError("Error inserting data to the table\nquery:" . $insert_query);
				return false;
			}
			else
			{
				// send the email to user with generated code
				$this->SendUserConfirmationEmail($email, $newcode);
				
				return ['VERIFICATION_REQUIRED',$newcode] ;
			}  			 
		  			 
        }
		else // there is a verification record exists, check if verification is done or not
		{ 
			$email_rec = mysqli_fetch_assoc($result);
			
			if($email_rec['verified'] != 'Y')
			{
				$newcode=mt_rand(1000,10000) ;
				
				$update_query = "update prequal.emailverify set
		               generatedcode = '" . $newcode . "' ,
					   generationtime = NOW(),
					   verified = ''
				       where
				       loginemail   = '" .$email. "' " ;
				    		           
                if(!mysqli_query($this->connection,$update_query))
				{
			 
				 $this->HandleDBError("Error updating data to the table\nquery:$update_query");
					return false;
				}
				
				// send email to user with code
				$this->SendUserConfirmationEmail($email, $newcode);
				
				return ['VERIFICATION_REQUIRED',$newcode] ;
				
			}
			
			
		}
           
        return ['VERIFICATION_NOT_REQUIRED',''] ; 
    }
	
	
	
    //------- End of Prequal Functions ----------
    
    
    //-------Public Helper functions -------------
    function GetSelfScript()
    {
        return htmlentities($_SERVER['PHP_SELF']);
    }    
    
    function SafeDisplay($value_name)
    {
        if(empty($_POST[$value_name]))
        {
            return'';
        }
        return htmlentities($_POST[$value_name]);
    }
    
    
    
    function GetSpamTrapInputName()
    {
        return 'sp'.md5('KHGdnbvsgst'.$this->rand_key);
    }
    
    function GetErrorMessage()
    {
        if(empty($this->error_message))
        {
            return '';
        }
        $errormsg = nl2br(htmlentities($this->error_message));
        return $errormsg;
    }    
    //-------Private Helper functions-----------
    
    function HandleError($err)
    {
        $this->error_message .= $err."\r\n";
    }
    
    function HandleDBError($err)
    {
        $this->HandleError($err."\r\n mysqlerror:".mysqli_error());
    }
    
    function GetFromAddress()
    {
        if(!empty($this->from_address))
        {
            return $this->from_address;
        }

        $host = $_SERVER['SERVER_NAME'];

        $from ="nobody@$host";
        return $from;
    } 
    
    function GetLoginSessionVar()
    {
        $retvar = md5('LOffRqroBEQ3PN7');
        $retvar = 'usr_'.substr($retvar,0,10);
        return $retvar;
    }
    
    function CheckLoginInDB($username,$password)
    {
        if(!$this->DBLogin())
        {
            $this->HandleError("Database login failed!");
            return false;
        }          
        $username = $this->SanitizeForSQL($username);
        $pwdmd5   = md5($password);
        $qry = "Select * from $this->tablename where account_username='$username' and account_password='$pwdmd5' and confirmcode='y'";
        
        $result = mysql_query($qry,$this->connection);
        
        if(!$result || mysql_num_rows($result) <= 0)
        {
            $this->HandleError("Error logging in. The username or password does not match");
            return false;
        }
        
        $row = mysql_fetch_assoc($result);
        
        
        $_SESSION['name_of_user']   = $row['account_name'];
        $_SESSION['email_of_user']  = $row['account_email'];
	$_SESSION['userid_of_user'] = $row['account_user_id'];
	$_SESSION['user_loginid']   = $row['account_username'];

	//-- Set admin id for adminstrator login

	if ($row['account_masterid'] <= 0)

	{  
	   $_SESSION['masterid_of_user'] = $row['account_user_id'];
	
	   //-- set the main admin property address - will use this for Rent Estimator - Zillow Tool
	

	   $_SESSION['property_address'] = trim($row['account_address']) . ', ' . trim($row['account_city']) . ', ' . trim($row['account_state']) . ' ' . trim($row['account_zip']);
    	   $_SESSION['property_city']    = trim($row['account_city']);
           $_SESSION['property_state']   = trim($row['account_state']);
    

	
        } else
	
	{
	   $_SESSION['masterid_of_user'] = $row['account_masterid'];
         
	   $_SESSION['property_address'] = ' ';
	   $_SESSION['property_city']    = ' ';
           $_SESSION['property_state']   = ' ';
    

	   	
        }


	$_SESSION[$this->GetLoginSessionVar()] = $username;

        
        return true;
    }
    
    function UpdateDBRecForConfirmation(&$user_rec)
    {
        if(!$this->DBLogin())
        {
            $this->HandleError("Database login failed!");
            return false;
        }   
        $confirmcode = $this->SanitizeForSQL($_GET['code']);
        
        $result = mysql_query("Select account_name, account_username, account_email from $this->tablename where confirmcode='$confirmcode'",$this->connection);   
        if(!$result || mysql_num_rows($result) <= 0)
        {
            $this->HandleError("Invalid confirmation code provided.");
            return false;
        }
        $row = mysql_fetch_assoc($result);
        $user_rec['name'] = $row['account_name'];
	$user_rec['user'] = $row['account_username'];
        $user_rec['email']= $row['account_email'];
        
        $qry = "Update $this->tablename Set confirmcode='y' Where  confirmcode='$confirmcode'";
        
        if(!mysql_query( $qry ,$this->connection))
        {
            $this->HandleDBError("Error inserting data to the table\nquery:$qry");
            return false;
        }      
        return true;
    }
    
    function ResetUserPasswordInDB($user_rec)
    {
        $new_password = substr(md5(uniqid()),0,10);
        
        if(false == $this->ChangePasswordInDB($user_rec,$new_password))
        {
            return false;
        }
        return $new_password;
    }
    
    function ChangePasswordInDB($user_rec, $newpwd)
    {
        $newpwd = $this->SanitizeForSQL($newpwd);
        
        $qry = "Update $this->tablename Set account_password='".md5($newpwd)."' Where  account_user_id=".$user_rec['account_user_id']."";
        
        if(!mysql_query( $qry ,$this->connection))
        {
            $this->HandleDBError("Error updating the password \nquery:$qry");
            return false;
        }     
        return true;
    }
    
    

    function GetUserFromUserid($userid,&$user_rec)
    {
        if(!$this->DBLogin())
        {
            $this->HandleError("Database login failed!");
            return false;
        }   
        $userid = $this->SanitizeForSQL($userid);
        
        $result = mysql_query("Select * from $this->tablename where account_username='$userid'",$this->connection);  

        if(!$result || mysql_num_rows($result) <= 0)
        {
            $this->HandleError("Userid: $userid does not exists!");
            return false;
        }
        $user_rec = mysql_fetch_assoc($result);

        
        return true;
    }
 
    function SendUserWelcomeEmail(&$user_rec)
    {
        
	$EmailSubject      = "Welcome to ".$this->sitename;
	$sendtoemailaddress= $user_rec['email'] ;  
        $emailTitle        = "Congratulation!";
 
        $Emailmessage  = "<p>Hello ".$user_rec['name'].",</p>";
	$Emailmessage .= "<p>Your registration with ".$this->sitename." is completed, which means you are already a step closer to screen your prospects and get the best tenant for your property.</p>";
	$Emailmessage .= "Please login to your Online Tenant Screening account using your user id <b> ".$user_rec['user']." </b> and order tenant credit checks and criminal background reports today.<br />";
	
	$Emailmessage .= "<p>Regards,</p>";
	$Emailmessage .= "<p>Support Team<br />";
	$Emailmessage .= $this->sitename."</p>";

	include 'sa_emailgenerator.php'; 

        if(!$this->SendEmail($sendtoemailaddress,$EmailSubject,$messageToSend))

        {
            $this->HandleError("Failed sending user welcome email.");
            return false;
        }
        return true;        
        
    }
    
    function SendAdminIntimationOnRegComplete(&$user_rec)
    {
        if(empty($this->admin_email))
        {
            return false;
        }
        $mailer = new PHPMailer();
        
        $mailer->CharSet = 'utf-8';
        
        $mailer->AddAddress($this->admin_email);
        
        $mailer->Subject = "New Registration Completed: ".$user_rec['name'];

        $mailer->From = $this->GetFromAddress();         
        
        $mailer->Body ="A new user registered at ".$this->sitename."\r\n".
        "User id: ".$user_rec['user']."\r\n";
        "Name...: ".$user_rec['name']."\r\n".
        "Email..: ".$user_rec['email']."\r\n";
        
        if(!$mailer->Send())
        {
            return false;
        }
        return true;
    }
    
    function GetResetPasswordCode($email)
    {
       return substr(md5($email.$this->sitename.$this->rand_key),0,10);
    }
    
    function SendResetPasswordLink($user_rec)
    {
	$email = $user_rec['account_email'];

        
	$EmailSubject      = "Password reset request at ".$this->sitename;
	$sendtoemailaddress= $email;  
        $emailTitle        = "Password Reset Request";
 	
        $link = $this->GetAbsoluteURLFolder().
                '/sa_resetpwd.php?email='.
                urlencode($user_rec['account_email']).'&code='.
                urlencode($this->GetResetPasswordCode($email));

         
        $Emailmessage  = "<p>Hello ".$user_rec['account_name'].",</p>";
	$Emailmessage .= "<p>We received a request to change your password on ".$this->sitename."</p>";
	$Emailmessage .= "Please click the link below to set a new password: <br />";
	$Emailmessage .= $link."<br />";
	$Emailmessage .= "<p>If you dont want to change your password, you can ignore this email.</p>";
	$Emailmessage .= "<p>Thank you,</p>";
	$Emailmessage .= "<p>Support Team<br />";
	$Emailmessage .= $this->sitename."</p>";

	include 'sa_emailgenerator.php'; 
 
        if(!$this->SendEmail($sendtoemailaddress,$EmailSubject,$messageToSend))

        {
            $this->HandleError("Failed sending password reset link email.");
            return false;
        }
        return true;

    }
    
    function SendNewPassword($user_rec, $new_password)
    {
        
	$email = $user_rec['account_email'];

        
	$EmailSubject      = "Your new password for ScreeningAdvantage account";
	$sendtoemailaddress= $email;  
        $emailTitle        = "New Password";
 	
                 
        $Emailmessage  = "<p>Hello ".$user_rec['account_name'].",</p>";
	$Emailmessage .= "<p>Your password is reset successfully.</p>";
	$Emailmessage .= "Following is your updated login id and password.<br />";

	$Emailmessage .= "Login Id : " .$user_rec['account_username']."<br />";
        $Emailmessage .= "Password : " .$new_password;

	$Emailmessage .= "<p>Thank you,</p>";
	$Emailmessage .= "<p>Support Team<br />";
	$Emailmessage .= $this->sitename."</p>";

	include 'sa_emailgenerator.php'; 
 
        if(!$this->SendEmail($sendtoemailaddress,$EmailSubject,$messageToSend))

        {
            $this->HandleError("Failed sending new password email.");
            return false;
        }
        return true;

	
    }    
    
    
    
      
    
    function Ensuretable()
    {
        $result = mysql_query("SHOW COLUMNS FROM $this->tablename");   
        if(!$result || mysql_num_rows($result) <= 0)
        {
            return $this->CreateTable();
        }
        return true;
    }
    
    function CreateTable()
    {
        $qry = "Create Table $this->tablename (".
                "id_user INT NOT NULL AUTO_INCREMENT ,".
                "name VARCHAR( 128 ) NOT NULL ,".
                "email VARCHAR( 64 ) NOT NULL ,".
                "phone_number VARCHAR( 16 ) NOT NULL ,".
                "username VARCHAR( 16 ) NOT NULL ,".
                "password VARCHAR( 32 ) NOT NULL ,".
                "confirmcode VARCHAR(32) ,".
                "PRIMARY KEY ( id_user )".
                ")";
                
        if(!mysql_query($qry,$this->connection))
        {
            $this->HandleDBError("Error creating the table \nquery was\n $qry");
            return false;
        }
        return true;
    }
    
    
    function MakeConfirmationMd5($email)
    {
        $randno1 = rand();
        $randno2 = rand();
        return md5($email.$this->rand_key.$randno1.''.$randno2);
    }


    function getUserIP()
    {
    	
	
        $client  = @$_SERVER['HTTP_CLIENT_IP'];
    	$forward = @$_SERVER['HTTP_X_FORWARDED_FOR'];
    	$remote  = $_SERVER['REMOTE_ADDR'];

    	if(filter_var($client, FILTER_VALIDATE_IP))
    	{
        	$ip = $client;
    	}
    	elseif(filter_var($forward, FILTER_VALIDATE_IP))
    	{
        $ip = $forward;
    	}
    	else
    	{
        $ip = $remote;
    	}  

    	return $ip;
    }
/* ********************************** */
/* ****  Add property Locations  **** */
/* ********************************** */

    function AddProperty()
    {
        if(!isset($_POST['submitted']))
        {
           return false;
        }
        
        $formvars = array();
        
        if(!$this->ValidatePropertySubmission())
        {
            return false;
        }
        
        $this->CollectPropertySubmission($formvars);
        
        if(!$this->SavePropertyToDatabase($formvars))
        {
            return false;
        }
                        
        return true;
    }

    function CollectPropertySubmission(&$formvars)
    {
	$formvars['adminid']   = $this->Sanitize($_POST['adminid']);
        $formvars['name']      = $this->Sanitize($_POST['name']);
        $formvars['type']      = $this->Sanitize($_POST['type']);
        $formvars['rent']      = $this->Sanitize($_POST['rent']);
        $formvars['address']   = $this->Sanitize($_POST['address']);
	$formvars['city']      = $this->Sanitize($_POST['city']);
	$formvars['state']     = $this->Sanitize($_POST['state']);
	$formvars['zip']       = $this->Sanitize($_POST['zip']);
	$formvars['adminuser'] = $this->Sanitize($_POST['adminuser']);
    }

    function ValidatePropertySubmission()
    {
        //This is a hidden input field. Humans won't fill this field.
        if(!empty($_POST[$this->GetSpamTrapInputName()]) )
        {
            //The proper error is not given intentionally
            $this->HandleError("Automated submission prevention: case 2 failed");
            return false;
        }
        
        $validator = new FormValidator();
	$validator->addValidation("name","req","Please provide property name");
        $validator->addValidation("type","req","please select property type");
        $validator->addValidation("rent","req","Please enter property expected market rent");
 	$validator->addValidation("address","req","Please provide property Stret address");
	$validator->addValidation("city","req","Please provide City");
	$validator->addValidation("state","req","Please provide State");        
	$validator->addValidation("zip","req","Please provide Zip code");

        if(!$validator->ValidateForm())
        {
            $error='';
            $error_hash = $validator->GetErrors();
            foreach($error_hash as $inpname => $inp_err)
            {
                $error .= $inpname.':'.$inp_err."\n";
            }
            $this->HandleError($error);
            return false;
        }        
        return true;
    }


    function SavePropertyToDatabase(&$formvars)
    {
        if(!$this->DBLogin())
        {
            $this->HandleError("Database login failed!");
            return false;
        }
        
                
        if(!$this->InsertIntoPropertyDB($formvars))
        {
            $this->HandleError("Inserting to Property Database failed!");
            return false;
        }
        return true;
    }
    
    function InsertIntoPropertyDB(&$formvars)
    {
    
        $date = date('Y-m-d H:i:s');
        $status='A';
       
        $insert_query = 'insert into propertylocation (
		account_user_id,
                property_name,
		property_address,
                property_city,
		property_state,
		property_zip,
                property_status,
		property_type,
		property_marketRent,
		property_updateuser,
		property_update
		 
                )
                values
                (
 		"' . $this->SanitizeForSQL($formvars['adminid']) .'",
                "' . $this->SanitizeForSQL($formvars['name']) . '",
		"' . $this->SanitizeForSQL($formvars['address']) . '",
		"' . $this->SanitizeForSQL($formvars['city']) . '",
		"' . $this->SanitizeForSQL($formvars['state']) . '",
		"' . $this->SanitizeForSQL($formvars['zip']) . '",
                "' . $status .'",
		"' . $this->SanitizeForSQL($formvars['type']) . '",
		"' . $this->SanitizeForSQL($formvars['rent']) . '",
		"' . $this->SanitizeForSQL($formvars['adminuser']) . '",
		"' . $date . '"
		)'; 
     
        if(!mysql_query( $insert_query ,$this->connection))
        {
            $this->HandleDBError("Error inserting data to the table\nquery:$insert_query");
            return false;
        }        
        return true;
    }

/* ********************************** */
/* *******   Add New Users    ******* */
/* ********************************** */

    function AddNewuser()
    {
        if(!isset($_POST['submitted']))
        {
           return false;
        }
        
        $formvars = array();
        
        if(!$this->ValidateNewUserSubmission())
        {
            return false;
        }
        
        $this->CollectNewUserSubmission($formvars);
        
        if(!$this->SaveNewUserToDB($formvars))
        {
            return false;
        }
        
        if(!$this->SendUserConfirmationEmail($formvars))
        {
            return false;
        }

                
        return true;
    }

    function ValidateNewUserSubmission()
    {
        //This is a hidden input field. Humans won't fill this field.
        if(!empty($_POST[$this->GetSpamTrapInputName()]) )
        {
            //The proper error is not given intentionally
            $this->HandleError("Automated submission prevention: case 2 failed");
            return false;
        }
        
        $validator = new FormValidator();
		$validator->addValidation("username","req","Please provide User login");
        $validator->addValidation("password","req","Please provide Password");
        $validator->addValidation("name","req","Please provide user Name");
        $validator->addValidation("email","email","The input for Email should be a valid email value");
        $validator->addValidation("email","req","Please provide Email");
         

        if(!$validator->ValidateForm())
        {
            $error='';
            $error_hash = $validator->GetErrors();
            foreach($error_hash as $inpname => $inp_err)
            {
                $error .= $inpname.':'.$inp_err."\n";
            }
            $this->HandleError($error);
            return false;
        }        
        return true;
    }
    
    function CollectNewUserSubmission(&$formvars)
    {
	$formvars['adminid']   = $this->Sanitize($_POST['adminid']);
	$formvars['name']      = $this->Sanitize($_POST['name']);
        $formvars['email']     = $this->Sanitize($_POST['email']);
        $formvars['username']  = $this->Sanitize($_POST['username']);
        $formvars['password']  = $this->Sanitize($_POST['password']);
	 
    }

    function SaveNewUserToDB(&$formvars)
    {
        if(!$this->DBLogin())
        {
            $this->HandleError("Database login failed!");
            return false;
        }
        
        if(!$this->IsFieldUnique($formvars,'email','account_email'))
        {
            $this->HandleError("This email is already assosciated with another user. Please use different email.");
            return false;
        }
        
        if(!$this->IsFieldUnique($formvars,'username','account_username'))
        {
            $this->HandleError("Login Id is already in used. Please try different login.");
            return false;
        }        
        if(!$this->InsertNewUserToDB($formvars))
        {
            $this->HandleError("Inserting of User to Database failed!");
            return false;
        }
        return true;
    }

    function InsertNewUserToDB(&$formvars)
    {
    
        $confirmcode='y'; 
        $date = date('Y-m-d H:i:s');
                
        $insert_query = 'insert into '.$this->tablename.'(
		account_username,
                account_password,
                account_name,
		account_email,
                confirmcode,
		account_masterid,
		account_update
		 
                )
                values
                (
 		"' . $this->SanitizeForSQL($formvars['username']) . '",
                "' . md5($formvars['password']) . '",
                "' . $this->SanitizeForSQL($formvars['name']) . '",
		"' . $this->SanitizeForSQL($formvars['email']) . '",
                "' . $confirmcode . '",
		"' . $this->SanitizeForSQL($formvars['adminid']) . '",
		"' . $date . '"
		)';      
        if(!mysql_query( $insert_query ,$this->connection))
        {
            $this->HandleDBError("Error inserting data to the table\nquery:$insert_query");
            return false;
        }        
        return true;
    }


    function getbgcolor($trcount)
    {

	$blue="\"background-color: #EEFAF6;\"";
	$green="\"background-color: #D4F7EB;\"";
	$odd=$trcount%2;
    	if($odd==1){return $blue;}
    	else{return $green;}    

    }

    function RetrieveAddress($userId)
    {
        if(!$this->DBLogin())
        {
            $this->HandleError("Database login failed!");
            return false;
        }          
         
        $qry = "Select account_address, account_city, account_state, account_zip from $this->tablename where account_user_id='$userId' ";
        
        $result = mysql_query($qry,$this->connection);
        
        if(!$result || mysql_num_rows($result) <= 0)
        { 
            return false;
        }
        
        $row = mysql_fetch_assoc($result);
        
                
	{  	
	   // set the main admin property address - will use this for Rent Estimator - Zillow Tool

	   $_SESSION['property_address'] = trim($row['account_address']) . ', ' . trim($row['account_city']) . ', ' . trim($row['account_state']) . ' ' . trim($row['account_zip']) ;
    	   $_SESSION['property_city']    = trim($row['account_city']);
           $_SESSION['property_state']   = trim($row['account_state']);
    

 	}

	   return true;

    }


/* === End of All Functions === */
/*


$qry = "SELECT 
			d.defrpfeedid, 
			d.defrpcategory, 
			d.defrpfieldtype, 
			d.defrpfieldname,
			d.defrpfieldtype, 

			A1.apprptempid,
			A1.apprpselect,
			A1.apprpoptlabel,
			A1.apprpdefaultreq,
			A1.apprpreq,
			A1.apprphide,
			A1.apprpedit,

			F1.finrptempid,
			F1.finrpselect,
			F1.finrpoptlabel,
			F1.finrpdefaultreq,
			F1.finrpreq,
			F1.finrphide,
			F1.finrpedit,

			L1.lstrmrptempid, 
			L1.lstrmrpselect,
			L1.lstrmrpoptlabel,
			L1.lstrmrpdefaultreq,
			L1.lstrmrpreq,
			L1.lstrmrphide,
			L1.lstrmrpedit,

			L2.limitrptempid, 
			L2.limitrpselect,
			L2.limitrpoptlabel,
			L2.limitrpdefaultreq,
			L2.limitrpreq,
			L2.limitrphide,
			L2.limitrpedit,

			M1.miscrptempid, 
			M1.miscrpselect,
			M1.miscrpoptlabel,
			M1.miscrpdefaultreq,
			M1.miscrpreq,
			M1.miscrphide,       
			M1.miscrpedit,
					 
			P1.petrptempid, 
			P1.petrpselect,
			P1.petrpoptlabel,
			P1.petrpdefaultreq,
			P1.petrpreq,
			P1.petrphide,
			P1.petrpedit,
					
			P2.proprptempid, 
			P2.proprpselect,
			P2.proprpoptlabel,
			P2.proprpdefaultreq,
			P2.proprpreq,
			P2.proprphide,
			P2.proprpedit,

			R1.resrptempid, 
			R1.resrpselect,
			R1.resrpoptlabel,
			R1.resrpdefaultreq,
			R1.resrpreq,
			R1.resrphide,
			R1.resrpedit,
					
			U1.unitrptempid, 
			U1.unitrpselect,
			U1.unitrpoptlabel,
			U1.unitrpdefaultreq,
			U1.unitrpreq,
			U1.unitrphide,
			U1.unitrpedit,
					 
			U2.utilityrptempid, 
			U2.utilityrpselect,
			U2.utilityrpoptlabel,
			U2.utilityrpdefaultreq,
			U2.utilityrpreq, 
			U2.utilityrphide,       
			U2.utilityrpedit,
						
			V1.vehrptempid, 
			V1.vehrpselect,
			V1.vehrpoptlabel,
			V1.vehrpdefaultreq,
			V1.vehrpreq,
			V1.vehrphide,
			V1.vehrpedit
			
			from defaultrp as d 
			 LEFT JOIN applianceinforp   as A1 ON 	A1.apprpfeedid   		= d.defrpfeedid   AND  A1.apprptempid      		 =61
			 LEFT JOIN financialinforp   as F1 ON 	F1.finrpfeedid   		= d.defrpfeedid   AND  F1.finrptempid            =61
			 LEFT JOIN leasetermrp       as L1 ON 	L1.lstrmrpfeedid 		= d.defrpfeedid   AND  L1.lstrmrptempid          =61
			 LEFT JOIN limitinforp       as L2 ON 	L2.limitrpfeedid   		= d.defrpfeedid   AND  L2.limitrptempid          =61
			 LEFT JOIN miscinforp        as M1 ON 	M1.miscrpfeedid   		= d.defrpfeedid   AND  M1.miscrptempid           =61
			 LEFT JOIN petinforp         as P1 ON 	P1.petrpfeedid   		= d.defrpfeedid   AND  P1.petrptempid            =61
			 LEFT JOIN propertyinforp    as P2 ON 	P2.proprpfeedid   		= d.defrpfeedid   AND  P2.proprptempid           =61
			 LEFT JOIN residentinforp    as R1 ON 	R1.resrpfeedid   		= d.defrpfeedid   AND  R1.resrptempid            =61
			 LEFT JOIN unitaddressrp     as U1 ON 	U1.unitrpfeedid   		= d.defrpfeedid   AND  U1.unitrptempid           =61
			 LEFT JOIN utilityinforp     as U2 ON 	U2.utilityrpfeedid   	= d.defrpfeedid   AND  U2.utilityrptempid        =61
			 LEFT JOIN vehicleparkrp     as V1 ON 	V1.vehrpfeedid   		= d.defrpfeedid   AND  V1.vehrptempid            =61
			order BY d.defrpfeedid";
			

*/
		
		##==============================================MOHAN PRACTICE================================================================
		
	
     function selectmembers($selectedmember)
	{
		if(!$this->DBLogin())
        {
            $this->HandleError("Database login failed!");
            return false;
        }
		$qry = "select * from applianceinforp where apprpfieldtype='{$selectedmember}'";
		error_log($qry);
		$result = mysqli_query($this->connection,$qry);
		if(!$result || mysqli_num_rows($result) <= 0)
		{
			return false;
		}
		$ar= [];
		while($row = mysqli_fetch_assoc($result))
		{
			error_log("hiii");
			array_push($ar, $row);  
		}
		mysqli_close($this->connection);
		return $ar;
	}
	
	
	
	/* storing information of all details form to DB */
	
	function inserttotable(	$main_cat,
							$sub_cat,
							$vehicle,
							$email,
							$pwd,
							$mnumber,
							$comment)
	{
				if(!$this->DBLogin())
			{
			  $this->HandleError("Database login failed!");
			  return false;
			}
			
			$insert = "insert into Persons(category,
											subcategory,
											vehicles,
											Email,
											pass1,
											mobilenumber,
											comment) 
											Values(
											'{$main_cat}',
											'{$sub_cat}',
											'{$vehicle}',
											'{$email}',
											'{$pwd}',
											'{$mnumber}',
											'{$comment}'
											)";
								
		$insertResult = mysqli_query($this->connection,$insert);		
		if(!$insertResult)
		{
		  error_log("Error in Inserting the records");	
		  mysqli_close($this->connection);
		  return false;
		}
		else
		{
			 
			mysqli_close($this->connection);
			return "inserted";
		}	
	}
	
	
	/* <!--practice package_new.php--> */
	
	function selectedpackage($selectedpackage)

{
		if(!$this->DBLogin())
        {
            $this->HandleError("Database login failed!");
            return false;
        }
		$qry = "select * from persons";
		error_log($qry);
		$result = mysqli_query($this->connection,$qry);
		if(!$result || mysqli_num_rows($result) <= 0)
		{
			return false;
		}
		$ar= [];
		while($row = mysqli_fetch_assoc($result))
		{
			error_log("hiii");
			array_push($ar, $row);  
		}
		mysqli_close($this->connection);
		return $ar;
	}
	
	
	
		
	/* storing information of all details form to DB */
	
	function insertintotable($first_name,$last_name,$ssn_occ,$dob_occ,$email_occ,$occupant_type)
	{
				if(!$this->DBLogin())
			{
			  $this->HandleError("Database login failed!");
			  return false;
			}
			
			$insertoccupant1 = "insert into addoccupant(firstname,
												lastname,
												ssn,
											    dob,
												email,
											    occupanttype) 
											Values(
											'{$first_name}',
											'{$last_name}',
											'{$ssn_occ}',
											'{$dob_occ}',
											'{$email_occ}',
											'{$occupant_type}')";
								
								
		$Result1 = mysqli_query($this->connection,$insertoccupant1);		
		if(!$Result1)
		{
		  error_log("Error in Inserting the records");	
		  mysqli_close($this->connection);
		  return false;
		}
		else
		{
			 
			mysqli_close($this->connection);
			return "inserted";
		}	
	}
	
	
	
	
	
	
	
	
	
	function addoccupant()

{
		if(!$this->DBLogin())
        {
            $this->HandleError("Database login failed!");
            return false;
        }
		$qry = "select * from addoccupant";
		
		$result = mysqli_query($this->connection,$qry);
		if(!$result || mysqli_num_rows($result) <= 0)
		{
			return false;
		}
		$ar= [];
		while($row = mysqli_fetch_assoc($result))
		{
			
			array_push($ar, $row);  
		}
		mysqli_close($this->connection);
		return $ar;
	}
	
	
		function updateintotable($slno,$fname,$lname,$ssn,$dob,$email,$occtype)

{
		if(!$this->DBLogin())
        {
            $this->HandleError("Database login failed!");
            return false;
        }
		$update_query = "update addoccupant set
		               firstname = '{$fname}',
		               	lastname = '{$lname}',						
		               	ssn = '{$ssn}',						
		               	dob = '{$dob}',						
		               email = '{$email}',						
		               occupanttype = '{$occtype}'						
				       where
				       	id = '{$slno}'";
		
		$result = mysqli_query($this->connection,$update_query);
		if(!$result)
		{
			return false;
		}
		else{
			mysqli_close($this->connection);
		return "updated";
		}
		
	}
	
	
	function deleteoccupant($sl_no)

{
		if(!$this->DBLogin())
        {
            $this->HandleError("Database login failed!");
            return false;
        }
		$delete_query = "DELETE FROM addoccupant						
				       where
				       	id = '{$sl_no}'";
		
		$result = mysqli_query($this->connection,$delete_query);
		if(!$result)
		{
			return false;
		}
		else{
			mysqli_close($this->connection);
		return "deleted";
		}
		
	}
	
	
	
	
	
	
	/* storing signAture setup data */
	
	function insertintonewtable (   $select_member_name,
									$membername,
									$select_esign_days,
									$reminder_frequency,
									$Email_massage,
									$name_1,$email_1,
									$name_2,$email_2,
									$name_3,$email_3,
									$name_4,$email_4,
									$name_5,$email_5,
									$name_6,$email_6,
									$ccname_1,$ccemail_1,
									$ccname_2,$ccemail_2,
									$ccname_3,$ccemail_3,
									$countername)
	{
				if(!$this->DBLogin())
			{
			  $this->HandleError("Database login failed!");
			  return false;
			}
			$selectqry = "select * from membersignature where Membername='{$membername}' and memberno='{$select_member_name}'";
			
			$insertomembersignature = "insert into  membersignature(memberno,
																	Membername,
																	TimelimitForesignindays,
																	ReminderFrequency,
																	EmailMassage,
																	MainName1,
																	MainEmail1,
																	MainName2,
																	MainEmail2,
																	MainName3,
																	MainEmail3,
																	MainName4,
																	MainEmail4,
																	MainName5,
																	MainEmail5,
																	MainName6,
																	MainEmail6,
																	ccName1,
																	ccEmail1,
																	ccName2,
																	ccEmail2,
																	ccName3,
																	ccEmail3,
																	AutoCounterSign) 
																	
																	
																	Values('{$select_member_name}',
																	        '{$membername}',
																			'{$select_esign_days}',
																			'{$reminder_frequency}',
																			'{$Email_massage}',
																			'{$name_1}',
																			'{$email_1}',
																			'{$name_2}',
																			'{$email_2}',
																			'{$name_3}',
																			'{$email_3}',
																			'{$name_4}',
																			'{$email_4}',
																			'{$name_5}',
																			'{$email_5}',
																			'{$name_6}',
																			'{$email_6}',
																			'{$ccname_1}',
																			'{$ccemail_1}',
																			'{$ccname_2}',
																			'{$ccemail_2}',
																			'{$ccname_3}',
																			'{$ccemail_3}',
																			'{$countername}')";
								
		//error_log($selectqry);						
		$Result1 = mysqli_query($this->connection,$insertomembersignature);		
		if(!$Result1)
		{
		  error_log("Error in Inserting the records");	
		  mysqli_close($this->connection);
		  return false;
		}
		else
		{
			 
			mysqli_close($this->connection);
			return "inserted";
		}	
	}
	
	
	
	
	
	
/* 	fetching information of member signature setup */


  function membersigned($select_member_name,$membername)
	{
		if(!$this->DBLogin())
        {
            $this->HandleError("Database login failed!");
            return false;
        }
		$qry = "select * from membersignature where Membername='{$membername}' and memberno='{$select_member_name}'";
		error_log($qry);
		$result = mysqli_query($this->connection,$qry);
		if(!$result || mysqli_num_rows($result) <= 0)
		{
			return false;
		}
		$ar= [];
		while($row = mysqli_fetch_assoc($result))
		{
			error_log("hiii");
			array_push($ar, $row);  
		}
		mysqli_close($this->connection);
		return $ar;
	}
	
	
	
	
	
}






	
	
?>