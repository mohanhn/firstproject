/*
=======================================================
	Assign Template  'Assign Template' toggle
=======================================================
*/

$(document).on('change','input.assignNewTemp:checkbox', function(){
	documentSelectedCount("input.assignNewTemp:checkbox:checked","#assignNewTemplate");
});

function documentSelectedCount(selector,targetButton)
{
	if ($(selector).length > 0)
	{
		$(targetButton).prop("disabled", false);
		 
	}
	else
	{
	   $(targetButton).prop("disabled", true);
	}
}



//ajax-assign-templates-member.php
/*
===========================================================
	  For Loading Assigned and UnAssigned Templates List
===========================================================
*/	

$(document).on('change', '#default_member', function(e){
	
		if($(this).val() !="")
		{	
			var selval = $(this).val();
			 
			var sel_inner_html = $("#default_member option:selected").text();
			var selval = selval.trim();
			var optionval = 'option_val='+ selval;
			
			jQuery.ajax(
			{	type: "POST",
				url : "ajax-assign-templates-member.php",
				dataType:"text",
				data:optionval,
				beforeSend:function(){
					$('.assignTemplatePreloader').fadeIn();
					$(".template-sec").fadeOut();
					$('.template-sec').html('');
					
				},
				success:function(response, data){
					
					$('.template-sec').html(response);
					//setTemplateLabel(selval, sel_inner_html);
					$(".template-sec").fadeIn();
					$('.assignTemplatePreloader').fadeOut();
				},
				error: function(jqXHR, textStatus, errorThrown) 
				{
					//if fails   
				 
				}
			})
			
			e.preventDefault(); //STOP default action    
		}
	});
	

$(document).on('click', '.deleteCurrentTemplate', function(e){
	
		var targetedBlock = $(this).attr('data-targetedformclass');
		targetedBlock = "."+targetedBlock+" :input";
		var data_value = $(targetedBlock).serialize();
		
		/* Show Preloader in table column */
		
		swal({
		   title: 'Are you sure?',
		   text: "You won't be able to revert this, all member level data will be deleted!",
		   type: 'warning',
		  showCancelButton: true,
		  confirmButtonColor: "#DD6B55",
		  confirmButtonText: "Yes, Unassign!",
		  closeOnConfirm: false,
		  showLoaderOnConfirm: true,
		  
		},
			function(){
				
			 jQuery.ajax(
			 {	type: "POST",
				url : "ajax-assign-templates-member.php",
				dataType:"text",
				data:data_value,
				beforeSend:function(){
					 
					$(".template-sec").fadeOut();
					$('.template-sec').html('');
					
				},
				success:function(response, data){
					
					$('.template-sec').html(response);
					$(".template-sec").fadeIn();
					swal("SUCCESS","Template is successfuly unassigned", "success");
				},
				error: function(jqXHR, textStatus, errorThrown) 
				{
					swal("ERROR","Operation failed, Refresh the page and try again", "error");	
				 
				}
			})
		
		
		}
		);
		return true;
		//e.preventDefault(); //STOP default action    
		 
	});
	
	
//	

