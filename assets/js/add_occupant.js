$('body').on('click', '#add_occ', function()
 {
	$('#myModal').show();
 })
 $('body').on('click', '#cancel_occ', function()
 {
	$('#myModal').hide();
		location.reload(true);
 }) 
 
 
 
 
 
 
 
 
 $('body').on('click', '#save_occ', function()
 {
	 var first_name=$('#first_name').val().trim();
	 var last_name=$('#last_name').val().trim();
	  var ssn_occ=$('#ssn_occ').val().trim();
	  var dob_occ=$('#dob_occ').val().trim();
	  var email_occ=$('#email_occ').val().trim();
	  var occupant_type=$('#occupant_type').val().trim();
	 if(first_name=='')
	 {
		 $(".fname_span").show();
		 return false;
	 }else if(last_name=='')
	 {
		 $(".lname_span").show();
		 $(".fname_span").hide();
		 return false;
	 }else if(ssn_occ=='')
	 {
		 $(".lname_span").hide();
		 $(".ssn_span").show();
		 return false;
	 }else if(dob_occ=='')
	 {
		 $(".ssn_span").hide();
		 $(".dob_span").show();
		  
		  return false;
	 }else if(email_occ=='')
	 {
		$(".email_span").show();
		$(".dob_span").hide();
		return false
	 }else if(occupant_type=='')
	 {
		 $(".occtype_span").show();
		 $(".email_span").hide();
		 return false
	 }else
	 {
		 $('#saveall').val('save');    // setting value save to saveall hidden field
		 var formdetails=$('#form_details').serialize();
		 jQuery.ajax(
				{	type: "POST",
					url : "ajax-occupant.php",
					data:formdetails,
					success:function(response)
					{	
						if(response.trim()=='inserted')
						{
							swal("SUCCESS","values inserted.", "success");
							//$('#form_details')[0].reset();
							//$('#cancel_occ').click();
							 setTimeout(function() {location.reload();},2000);
						
							
						}else
						{
							swal("Error", "Not inserted, Please try again", "error");
						}
					},
					error: function(jqXHR, textStatus, errorThrown) 
					{
						 $('#cancel_occ').click();
						swal("Error", "unknown error occured, Please try again", "error");	 
						   
					}
				}) 
		 
	 }
	 
	
	 
 });
 
 
 
 
 $('body').on('click', '#edit_row', function()
 {
	$(this).closest('tr').find("input").removeAttr('disabled');
	$('.save_button').show("slow");
	$('.edit_button').hide();
	 
 });
 
 
 $('body').on('click', '.save_button1', function()
 {
	 $('#update').val('UPDATE');
	var formdetails=$(this).closest('tr').find("input").serialize();
	 jQuery.ajax(
				{	type: "POST",
					url : "ajax-occupant.php",
					data:formdetails,
					success:function(response)
					{	
						if(response.trim()=='updated')
						{
							swal("SUCCESS","values Updated.", "success");
							
							$('.save_button').hide();
							$('.edit_button').show("slow");
							setTimeout(function() {location.reload();},2000);
							
						}else
						{
							swal("Error","Not inserted, Please try again", "error");
						}
					},
					error: function(jqXHR, textStatus, errorThrown) 
					{
						 $('#cancel_occ').click();
						swal("Error", "unknown error occured, Please try again", "error");	 
						   
					}
				
				}) 
					
		
 });
 
 
 
  $('body').on('click', '.del_button', function()
 {
	
	var ID = $(this).attr("id");
	var serial='col_id='+ID;
	jQuery.ajax(
				{	type: "POST",
					url : "ajax-occupant.php",
					data:serial,
					success:function(response)
					{	
						if(response.trim()=='deleted')
						{
							swal("SUCCESS","Row deleted successsfully", "success");
							
							$('.save_button').hide();
							$('.edit_button').show("slow");
							 setTimeout(function() {location.reload();},1000);
							
						}else
						{
							swal("Error", "Not Deleted, Please try again", "error");
						}
					},
					error: function(jqXHR, textStatus, errorThrown) 
					{
						 $('#cancel_occ').click();
						swal("Error", "unknown error occured, Please try again", "error");	 
						   
					}
				
				}) 
					
		
 });
 
 
 
 
 
 
 
 
 
