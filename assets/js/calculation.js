 /*
	================================================================
	   show Fields on calculation setup page for selected template
    ================================================================
	*/
	$(document).on('click', '.saveCalculationSetup', function(e){
	 
		var check  = 1;
		$('select').each(function fn(){
			
			var id = '#'+$(this).attr('id');
			if($(this).val() == '')
			{	 
				check =0;
				 
			}
			
			if(id = '#inputprorent')
			{
				var selectedVal = $(id).val();
				if(selectedVal=='totalRentExcludedRen')
				{
					var count = $("#excluded_rent input[type='checkbox']:checked").length;
					if(count>0)
					{
						$('#excludedNotChecked').fadeOut();
					}	
					else
					{
						check =0;
						$('#excludedNotChecked').fadeIn();
						$(id).parents(".panel-default").find('.active_menu.collapsed').trigger('click');
					}	
				}	
			}		
		})
		/* check if atleast one field is selected under Total Rent */
		
		var totalRentChecked = $("#total_rent input[type='checkbox']:checked").length;
		if(totalRentChecked<=0)
		{
			check =0;
			$('#total_rent').parents(".panel-default").find('.active_menu.collapsed').trigger('click');
			$('#totalNotChecked').fadeIn();
		}
		else
		{
			$('#totalNotChecked').fadeOut();
		}		
		
		validateSelect("#caluculationform select");
		  
		if(check)
		{	
			var form = $('#caluculationform');
			var data_value = form.serialize();
			
			jQuery.ajax(
			{	type: "POST",
				url : "ajax-calculation.php",
				dataType:"text",
				data:data_value,
				beforeSend:function(){
					$('.pre_loader_block').fadeIn();
					//$(".template-sec").fadeOut();
					//$('#template_content_area').html('');
					
				},
				success:function(response, data){
					
					var result = response.trim();
					if(result == 'updateSuccess')
					{	
						$('.pre_loader_block').fadeOut();
					}
					else if(result == 'updateError')
					{
						swal("Oops...", "Something went wrong! Refresh and try again.", "error");
						$('.pre_loader_block').fadeOut(); 
					}	
							
				},
				error: function(jqXHR, textStatus, errorThrown) 
				{
					//if fails   
				 
				}
			})
			
			e.preventDefault(); //STOP default action    
		}
	});
	
	
	function validateSelect($targetId)
	{
		$("#caluculationform select").each(function() 
		{
			var id = "#"+$(this).attr("id");
			//console.log(id);
			jQuery(function()
			{
				jQuery(id).validate(
				{	 
					expression: "if ((VAL != '') && (VAL != undefined)){ console.log(VAL); return true;} else {console.log(VAL); return false;}",
					message: "Please make a valid selection"
				
				});
			});
		})	
	}
	
	
	
	$(document).on('click','.previousPage', function() { 
		
		$('#calculation_back').trigger('submit');
	});

 /*
	================================================================
	  Toggle Error Msg Total Rent
    ================================================================
*/
	
$(document).on('click','#total_rent input[type="checkbox"]', function(){
		
	var totalRentChecked = $("#total_rent input[type='checkbox']:checked").length;
	if(totalRentChecked<=0)
	{	
		$('#total_rent').parents(".panel-default").find('.active_menu.collapsed').trigger('click');
		$('#totalNotChecked').fadeIn();
	}
	else
	{	
		$('#totalNotChecked').fadeOut();
	}	
})	

/*
	================================================================
	  Toggle Error Msg Excluded Rent
    ================================================================
*/
	
$(document).on('click','.excluded-block #excluded_rent input[type="checkbox"]', function(){
		
	var totalRentChecked = $("#excluded_rent input[type='checkbox']:checked").length;
	if(totalRentChecked<=0)
	{	
		$('#total_rent').parents(".panel-default").find('.active_menu.collapsed').trigger('click');
		$('#excludedNotChecked').fadeIn();
	}
	else
	{	
		$('#excludedNotChecked').fadeOut();
	}	
})	
	
$(document).ready(function fn(){
	
	validateSelect("#caluculationform select");
})	
$(document).on('change','#inputprorent',function(){
	var value = $("select#inputprorent option:selected").val();
	if(value == 'totalRentExcludedRen')
	{
		$('.excluded-block').fadeIn();
	}	
	else
	{
		$('.excluded-block').fadeOut();
	}	
	
})	