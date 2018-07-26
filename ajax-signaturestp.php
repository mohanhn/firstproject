<?php
session_start();
require_once("assets/include/membersite_config.php");

if(isset($_POST['saveall'])=="save")
{
	$save=$_POST['saveall'];
	
	if(isset($_POST['select_member_name']))
		{
					$select_member_name=$_POST['select_member_name'];
					
				}else
				{
					$select_member_name='';
				}
				
	if(isset($_POST['membername']))
		{
					$membername=$_POST['membername'];
					
				}else
				{
					$membername='';
				}
				
	if(isset($_POST['select_esign_days']))
		{
					$select_esign_days=$_POST['select_esign_days'];
					
				}else
				{
					$select_esign_days='';
				}
				
	if(isset($_POST['reminder_frequency']))
		{
					$reminder_frequency=$_POST['reminder_frequency'];
					
				}else
				{
					$reminder_frequency='';
				}
				
	if(isset($_POST['Email_massage']))
		{
					$Email_massage=$_POST['Email_massage'];
					
				}else
				{
					$Email_massage='';
				}
    
	if(isset($_POST['name_1']))
		{
					$name_1=$_POST['name_1'];
					
				}else
				{
					$name_1='';
				}
	
	if(isset($_POST['email_1']))
		{
					$email_1=$_POST['email_1'];
					
				}else
				{
					$email_1='';
				}
				
				
	if(isset($_POST['name_2']))
		{
					$name_2=$_POST['name_2'];
					
				}else
				{
					$name_2='';
				}
	
	if(isset($_POST['email_2']))
		{
					$email_2=$_POST['email_2'];
					
				}else
				{
					$email_2='';
				}			
				
				
	if(isset($_POST['name_3']))
		{
					$name_3=$_POST['name_3'];
					
				}else
				{
					$name_3='';
				}
	
	if(isset($_POST['email_3']))
		{
					$email_3=$_POST['email_3'];
					
				}else
				{
					$email_3='';
				}			

				
	if(isset($_POST['name_4']))
		{
					$name_4=$_POST['name_4'];
					
				}else
				{
					$name_4='';
				}
	
	if(isset($_POST['email_4']))
		{
					$email_4=$_POST['email_4'];
					
				}else
				{
					$email_4='';
				}	
		
				
	if(isset($_POST['name_5']))
		{
					$name_5=$_POST['name_5'];
					
				}else
				{
					$name_5='';
				}
	
	if(isset($_POST['email_5']))
		{
					$email_5=$_POST['email_5'];
					
				}else
				{
					$email_5='';
				}	
	if(isset($_POST['name_6']))
		{
					$name_6=$_POST['name_6'];
					
				}else
				{
					$name_6='';
				}
	
	if(isset($_POST['email_6']))
		{
					$email_6=$_POST['email_6'];
					
				}else
				{
					$email_6='';
				}	
	
	if(isset($_POST['ccname_1']))
		{
					$ccname_1=$_POST['ccname_1'];
					
				}else
				{
					$ccname_1='';
				}
	if(isset($_POST['ccemail_1']))
		{
					$ccemail_1=$_POST['ccemail_1'];
					
				}else
				{
					$ccemail_1='';
				}
				
	if(isset($_POST['ccname_2']))
		{
					$ccname_2=$_POST['ccname_2'];
					
				}else
				{
					$ccname_2='';
				}
	if(isset($_POST['ccemail_2']))
		{
					$ccemail_2=$_POST['ccemail_2'];
					
				}else
				{
					$ccemail_2='';
				}
	if(isset($_POST['ccname_3']))
		{
					$ccname_3=$_POST['ccname_3'];
					
				}else
				{
					$ccname_3='';
				}
	if(isset($_POST['ccemail_3']))
		{
					$ccemail_3=$_POST['ccemail_3'];
					
				}else
				{
					$ccemail_3='';
				}
				
				
	if(isset($_POST['countername']))
		{
					$countername=$_POST['countername'];
					
				}else
				{
					$countername='';
				}
				
				$insertomembersignature=$fgmembersite->insertintonewtable($select_member_name,
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
																		  $countername);
			echo $insertomembersignature;
}



            
			
						if(($_POST['onchanged']=="onchangeevent"))
	
						{
							$select_member_name1=$_POST['select_member_name'];
							$select_member_name1=trim($select_member_name1);
							
							$membername1=$_POST['membername'];
							$membername1=trim($membername1);
							$resultsofall=$fgmembersite->membersigned($select_member_name1,$membername1);
							if(!empty($resultsofall))
							{
								$emailmsg=$resultsofall[0]['EmailMassage'];
								$emailname1=$resultsofall[0]['MainName1'];
								$emailid1=$resultsofall[0]['MainEmail1'];
								$ccemailname1=$resultsofall[0]['ccName1'];
								$ccemailid1=$resultsofall[0]['ccEmail1'];
								$autosignname=$resultsofall[0]['AutoCounterSign'];
							}else
							{
								$emailmsg='';
								$emailname1='';
								$emailid1='';
								$ccemailname1='';
								$ccemailid1='';
								$autosignname='';
							}
							
							error_log(print_r($resultsofall,true));
							echo '<div class="row" >
									<div class="col-xs-12">
										<div class="form-inline" id="time_limit_sign">
											<div class="form-group" style="margin-left:280px;">
												<div style="text-align:left;">Time limit For e-sign in days</div>
												<div class="input-group option_width">
													<div class="input-group-addon"><i class="fa fa-newspaper-o" ></i></div>
													<select class="form-control" name="select_esign_days" id="select_esign_days" value="">
															<option selected="" disabled="">Time limit For e-sign in days</option>
															<option>1</option>
															<option>2</option>
															<option>3</option>
															<option>4</option>
															<option>5</option>
															<option>6</option>
															<option>7</option>
															<option>8</option>
															<option>9</option>
															<option>10</option>
															<option>11</option>
															<option>12</option>
															<option>13</option>
															<option>14</option>
															<option>15</option>
															<option>16</option>
															<option>17</option>
															<option>18</option>
															<option>19</option>
															<option>20</option>
															<option>21</option>
															<option>22</option>
															<option>23</option>
															<option>24</option>
															<option>25</option>
															<option>26</option>
															<option>27</option>
															<option>28</option>
															<option>29</option>
															<option>30</option>
															<option>31</option>
													</select>
												</div>
											</div>
											<div class="form-group" style="margin-left:200px;" >
												<div style="text-align:left;">Reminder Frequency</div>
												<div class="input-group option_width">
													<div class="input-group-addon"><i class="fa fa-newspaper-o" ></i></div>
														<select class="form-control" name="reminder_frequency" id="reminder_frequency" value="" disabled>
															<option selected="" disabled="">Reminder Frequency</option>
															<option>Daily</option>
															<option>Weekly</option>
														</select>
												</div>
											</div>
											
											<div class="col-xs-12">
												<div class="form-group" style="margin-left:380px;">
													<div style="text-align:left;font-size:10px;"><label style="font-size:15px;" for="Email">Email Massage</label>(1000 words allowed)</div>
													<textarea class="form-control" rows="5" name="Email_massage" value="'.$emailmsg.'" id="Email_massage" style="width:550px;"></textarea>
												</div>
											</div>
											<div class="col-xs-12" style="margin-top:30px">
														<div class="col-xs-6">
															<div class="col-xs-12">
																<div class="row" style="background-color:#d3d3d34f;">
																	<div class="col-xs-12">
																		<label style="padding-top:10px; padding-left:80px;">Names and Email Addresses of Leasing Agents/Landlord (Lease Signers)</label>
																	</div>
																</div>
															</div>
														
																<div class="col-xs-12">
																	<table class="table-responsive col-xs-12" >
																		<tr class="table_tr">
																			<th class="col-xs-2">Sl No</th>
																			<th class="col-xs-3">Name</th>
																			<th class="col-xs-4">Email</th>
																			<th class="col-xs-3">Action</th>
																		</tr> 
																		<tr class="table_tr">
																			<td class="col-xs-2">1</td>
																			<td class="col-xs-3"><input type="text" class="form-control" name="name_1" id="name_1" value="'.$emailname1.'"></td>
																			<td class="col-xs-4"><input type="text" class="form-control" name="email_1" id="email_1" value="'.$ccemailid1.'"></td>
																			<td class="col-xs-3"><i id="del_1_1" ></i></td>
																		</tr>
																		<tr class="table_tr" id="tr_1_2"  style="display:none;">
																			<td class="col-xs-2">2</td>
																			<td class="col-xs-3"><input type="text" class="form-control" name="name_2" id="name_2" value=""></td>
																			<td class="col-xs-4"><input type="text" class="form-control" name="email_2" id="email_2" value=""></td>
																			<td class="col-xs-3"><i class="fa fa-trash-o deleteBtn" aria-hidden="true" id="del_1_2" style="color:red;font-size:26px"></i></td>
																			
																		</tr>
																		<tr class="table_tr" id="tr_1_3" style="display:none;">
																			<td class="col-xs-2">3</td>
																			<td class="col-xs-3"><input type="text" class="form-control" name="name_3" id="name_3" value=""></td>
																			<td class="col-xs-4"><input type="text" class="form-control" name="email_3" id="email_3" value=""></td>
																			<td class="col-xs-3"><i class="fa fa-trash-o deleteBtn" aria-hidden="true" id="del_1_3" style="color:red;font-size:26px"></i></td>
																		</tr>
																		<tr class="table_tr" id="tr_1_4" style="display:none;">
																			<td class="col-xs-2">4</td>
																			<td class="col-xs-3"><input type="text" class="form-control" name="name_4" id="name_4" value=""></td>
																			<td class="col-xs-4"><input type="text" class="form-control" name="email_4" id="email_4" value=""></td>
																			<td class="col-xs-3"><i class="fa fa-trash-o deleteBtn" aria-hidden="true" id="del_1_4" style="color:red;font-size:26px"></i></td>
																		</tr>
																		<tr class="table_tr" id="tr_1_5" style="display:none;">
																			<td class="col-xs-2">5</td>
																			<td class="col-xs-3"><input type="text" class="form-control" name="name_5" id="name_5" value=""></td>
																			<td class="col-xs-4"><input type="text" class="form-control" name="email_5" id="email_5" value=""></td>
																			<td class="col-xs-3"><i class="fa fa-trash-o deleteBtn" aria-hidden="true" id="del_1_5" style="color:red;font-size:26px"></i></td>
																		</tr>
																		<tr class="table_tr" id="tr_1_6" style="display:none;">
																			<td class="col-xs-2">6</td>
																			<td class="col-xs-3"><input type="text" class="form-control" name="name_6" id="name_6" value=""></td>
																			<td class="col-xs-4"><input type="text" class="form-control" name="email_6" id="email_6" value=""></td>
																			<td class="col-xs-3"><i class="fa fa-trash-o deleteBtn" aria-hidden="true" id="del_1_6"style="color:red; font-size:26px"></i></td>
																				
																		</tr>
																	
																		
																	</table>
																</div>
																	<center><button type="button" class="btn btn-primary"  id="btn_addmore_1"  data-target="1">ADD MORE</button></center>
																	<input type="hidden" name="count" id="count" value="1">
															</div>
												
												<div class="col-xs-5">
													<div class="col-xs-12">
															<div class="row" style="background-color:#d3d3d34f; ">
																<div class="col-xs-12">
																	<label style="padding-top:10px; padding-left:80px;">Email Addresses Of Possible CC Recipients (Non-Signers)</label>
																</div>
															</div>
													<table class="table-responsive col-xs-12" >
														<tr class="table_tr">
															<th class="col-xs-2">Sl No</th>
															<th class="col-xs-3">Name</th>
															<th class="col-xs-4">Email</th>
															<th class="col-xs-3">Action</th>
														</tr> 
														<tr class="table_tr" id="tr_2_1">
															<td class="col-xs-2">1</td>
															<td class="col-xs-3"><input type="text" class="form-control" name="ccname_1" id="ccname_1" value="'.$ccemailname1.'"></td>
															<td class="col-xs-4"><input type="text" class="form-control" name="ccemail_1" id="ccemail_1" value="'.$emailid1.'"></td>
															<td class="col-xs-3"><i class="fa fa-trash-o deleteBtn1" aria-hidden="true" id="del_2_1" style="color:red;font-size:26px"></></i></td>
														</tr>
														<tr class="table_tr"id="tr_2_2" style="display:none;">
															<td class="col-xs-2">2</td>
															<td class="col-xs-3"><input type="text" class="form-control" name="ccname_2" id="ccname_2" value=""></td>
															<td class="col-xs-4"><input type="text" class="form-control" name="ccemail_2" id="ccemail_2" value=""></td>
															<td class="col-xs-3"><i class="fa fa-trash-o deleteBtn1" aria-hidden="true" id="del_2_2" style="color:red;font-size:26px"></></i></td>
														</tr>
														<tr class="table_tr" id="tr_2_3" style="display:none;">
															<td class="col-xs-2">3</td>
															<td class="col-xs-3"><input type="text" class="form-control" name="ccname_3" id="ccname_3" value=""></td>
															<td class="col-xs-4"><input type="text" class="form-control" name="ccemail_3" id="ccemail_3" value=""></td>
															<td class="col-xs-3"><i class="fa fa-trash-o deleteBtn1" aria-hidden="true" id="del_2_3" style="color:red;font-size:26px"></></i></td>
														</tr>
													</table>
												 </div>
													<center><button type="button" class="btn btn-primary" id="btn_addmore_2">ADD MORE</button></center>
													<input type="hidden" name="count1" id="count1" value="1">
												</div>
											</div>
										
										<div class="col-xs-12" >
											<div class="col-xs-12 content_window">
												<div class="col-xs-12 counter_sign" style="padding:10px" >
													<div class="col-xs-12">
														<label><b>Auto Counter Sign</b></label>
													</div>
													<div class="col-xs-12">
														<div><input type="checkbox" name="check_autocounter" id="check_autocounter"> Activate Auto Counter Sign</div>
													</div>
													<div class="col-xs-12" style="background-color: #eeeeee;width: 280px;padding: 14px;">
														<div class="block">Use the following Name to Counter Sign </div>
														<input type="text" class="form-control" id="countername" name="countername" value="'.$autosignname.'" disabled >
													</div>
												</div>
											</div>
										</div>
										</div>
										</form>
										<div class="col-xs-12">
											<div class="col-xs-6 signature_buttons" >
													<button type="button" class="btn btn-primary" name="save_sign" id="save_sign">SAVE</button>
													<button type="button" class="btn btn-primary" name="saveapply_sign" id="saveapply_sign">SAVE&APPLY</button>
											</div>
										</div>
							</div>
						</div>';
						
						}



?>