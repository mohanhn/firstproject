 /*
=======================================================
			LeaseInput Landing Page
=======================================================
*/
 /*
=======================================================
			LeaseInput Lease Type and 
			     Package Selection
=======================================================
*/	
$(document).on('change', '#select-leaseInput-type, #select-leaseInput-package', function(e){
  
	if($(this).attr('id')=="select-leaseInput-type")
	{	
		/* if selected Value is the first option....do nothing */
		if($(this).prop('selectedIndex')!=0)
		{		var selected_lease = $(this).val();
				var lease_type = '&lease_type='+ selected_lease;
				var default_values = $('#leaseInput-documentsetup-select').serialize()+lease_type;
				jQuery.ajax(
				{	type: "POST",
					url : "ajax-leaseinput-landing.php",
					//dataType:"text",
					data:default_values,
					beforeSend:function(){
						$('#leaseInput-pakg-loader').fadeIn();
					},
					success:function(response)
					{	$('#select-leaseInput-package').attr('disabled',false);
						$('#select-leaseInput-package').html(response);
						$('#select-leaseInput-package option:eq(0)').prop('selected', true);
						$('#leaseInput-pakg-loader').fadeOut();
						 
					},
					error: function(jqXHR, textStatus, errorThrown) 
					{
						 
					swal("Error", "unknown error occured, Please try again", "error");	 
						   
					}
				})
			
			
			 
		}
	}
	else if($(this).attr('id')=="select-leaseInput-package")
	{
		if(($(this).prop('selectedIndex')!=0) && ($('#select-leaseInput-type').prop('selectedIndex')!=0))
		{
			//call to ajax
			var leaseInput_type = $('#select-leaseInput-type option:selected').val();
			var leaseInput_package = $('#select-leaseInput-package option:selected').val();
			var formData = '&leaseInput_type='+ leaseInput_type + '&leaseInput_package=' + leaseInput_package;
			var default_values = $('#leaseInput-documentsetup-select').serialize()+formData; 
			jQuery.ajax(
			{	type: "POST",
				url : "ajax-leaseinput-landing.php",
				//dataType:"text",
				data:default_values,
				success:function(response)
				{	 
					$('#leaseInput-content-area').html('');
					$('#leaseInput-content-area').html(response);
					documentSelectedCount("#leaseInput_save_documents input:checkbox:checked",".btn-leaseInput.leaseInput-continue");
					$('#leaseInput-content-area').fadeIn();
					
					 
				},
				error: function(jqXHR, textStatus, errorThrown) 
				{
					 
				swal("Error", "unknown error occured, Please try again", "error");	 
					   
				}
			})
			 
			 
			
			 
		}
	}
});	
	
 /*
=======================================================
			LeaseInput Landing Page Cancel Button
=======================================================
*/	

$(document).on('click','.leaseInput-cancel',function(e){
	 
	swal(
	{
		title: "Are you sure?",
		text: "All changes will be lost!",
		type: "warning",
		showCancelButton: true,
		confirmButtonColor: "#DD6B55",
		confirmButtonText: "Yes, Continue!",
		animation: "slide-from-top",
		showLoaderOnConfirm: false,
		closeOnConfirm: false
	},
		function()
		{
			$('#select-leaseInput-type option:eq(0)').prop('selected', true);
			$('#select-leaseInput-package').prop('disabled', true);
			$('#select-leaseInput-package option:eq(0)').prop('selected', true);
			$('#leaseInput-content-area').fadeOut();
			swal("Canceled!", "Changes not saved, Click 'Ok' to continue", "error");
		}
	);
});

 /*
=======================================================
			LeaseInput Landing Page Continue DISABLED
=======================================================
*/

$(document).on('change','#leaseInput_save_documents input:checkbox', function(){
	documentSelectedCount("#leaseInput_save_documents input:checkbox:checked",".btn-leaseInput.leaseInput-continue");
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

$(document).on('click','.leaseInput-continue',function(e){
	 
	swal(
	{
		title: "Are you sure?",
		text: "You are about to start a new lease",
		type: "info",
		showCancelButton: true,
		confirmButtonColor: "#DD6B55",
		confirmButtonText: "Yes, Continue!",
		animation: "slide-from-top",
		showLoaderOnConfirm: true,
		closeOnConfirm: false
	},
		function()
		{
			
				var leaseInput_type = $('#select-leaseInput-type option:selected').val();
				var leaseInput_package = $('#select-leaseInput-package option:selected').val();
				var leaseInput_package_name = $('#select-leaseInput-package option:selected').text();
				var leaseInput_inquiry_no = $('#leaseInput_inquiry_no').val();
				var leaseInput_co_qualifier_id = $('#leaseInput_co_qualifier_id').val();
				var formData = '&leaseInput_type='+ leaseInput_type + '&leaseInput_package=' + leaseInput_package + '&leaseInput_doc_submit=' + 'submit'+ '&leaseInput_package_name='+leaseInput_package_name+'&leaseInput_inquiry_no='+leaseInput_inquiry_no+ '&leaseInput_co_qualifier_id='+leaseInput_co_qualifier_id;
				$('.leaseInput-disabled').each(function(){
					$(this).attr('disabled',false);
				})
				
				 
				var form_documentseleted = $('#leaseInput_save_documents').serialize()+formData;  
				jQuery.ajax(
				{	type: "POST",
					url : "ajax-leaseinput-landing.php",
					//dataType:"text",
					data:form_documentseleted,
					success:function(response)
					{	 
						//$('#leaseInput-content-area').html('');
						//$('#leaseInput-content-area').html(response);
						
						//swal("Success!", "New Lease ID Generated  Successfuly, click Continue to go to Lease Page", "success");
						response = response.trim();
						var arr = response.split('_');
						response = arr[0]; 
						if(response=='leaseIdGenerateSuccess')
						{
						 window.location.replace("lease-input.php");
						}
						else if(response=='noTemplateAssigned'){
							swal("Error", "No template assigned to selected Member, Lease ID not generated, please assign a template to the member", "error");	
						}
						else if(response=='noPropertyFound')
						{
							swal("Error", "Property details are incorrect, Lease ID not generated", "error");	
						}
						else if(response=='noLeaseGenerated')
						{
							swal("Error", "Unknown error occured!, close the window and try again.", "error");	
						}
						else if(response=='unknnownError')
						{
							swal("Error", "MasterleaseId error, Masterlease not generated", "error");	
						}
						else if(response=='DefaultNotSet')
						{
							swal("Error", "All defaults are not set, please set the defaults", "error");	
						}
						else if(response=='noCalculationSetup')
						{
							swal("Error", "Calculation Setup is incomplete, Template with ID "+arr[1]+" has no calculation setup.", "error");	
						}
						
						else
						{
							swal("Error", "Unknown error occured, Lease ID not generated, please try again", "error");	
						}		
					},
					error: function(jqXHR, textStatus, errorThrown) 
					{
						swal("Error", "Unknown error occured, Please try again", "error");	 
					}
				})
			
			
			
		}
	);
});




 /*
=======================================================
			LeaseInput Main Page 
=======================================================
*/
$(document).on('click','#category-list .label-accordion',function(e){
	var target = '#'+$(this).attr('data-target');
	$("#category-list .label-accordion").removeClass("active");
	$(this).addClass('active');
	$('.form-block').hide();
	$(target).fadeIn();
	showRightCalulationBox();
})

/* --------------------------------------------------------
	Cancel, exit without saving Build Template Page
----------------------------------------------------------- */
/* $('body').on('click','.cancel-update',function () 
{ 	
	swal({
	  title: "Are you sure?",
	  text: "You want to close without saving!",
	  type: "warning",
	  showCancelButton: true,
	  confirmButtonColor: "#DD6B55",
	  confirmButtonText: "Yes, close!",
	  closeOnConfirm: false
	},
	function(){
	  
	  swal("Canceled!", "Your data was not saved.", "error");
	  
	});
});	
	 */  
/*
=======================================================
				Save changes 
=======================================================
*/

$('body').on('click','.save-continue',function (e) { 
	resultset = 1;
	 
	var current = $('#category-list .label-accordion.active');
	var currentInput = current.prev('input');
	 
	
	var currentID = '#'+$(current).attr('data-target');
	var next = current.parent().next().find('label.label-accordion');
	var nextt = next.attr('data-target');
	var nextID = '#'+next.attr('data-target');
	var nextTable = next.attr('data-input-tab');
	var previous = current.parent().prev().find('label.label-accordion');
	var previousID = '#'+previous.attr('data-target');
	unmaskCurrency(currentID+' input[data-type=Dollars');
	validate_leaseInputs(currentID);
	
	console.log("currentID "+currentID);
	console.log("nextID "+nextID);
	console.log("nextTable "+nextTable);
	console.log("previous "+previousID);
	
	var id = $('#masterID').val();
	var memberdata = '&masterleaseid='+id+'&mastertable='+nextTable+'&nextID='+nextt;
	var form =$(currentID); 
	var formdata = form.serialize();
	 
	
	if(resultset){
		jQuery.ajax(
		{	type: "POST",
					url : "ajax-leaseinput-main.php",
					data:formdata,
					beforeSend:function(){
						$('#leaseInputMain-loader').fadeIn();
					},
					success:function(response)
					{	
						if($(nextID).length==0){
							//$(currentID).after(response);
							$('#leaseInputMain-loader').fadeOut();
						}
						else
						{
						 //$(nextID).remove();
						// $(currentID).after(response);
						}
						currentInput.prop('checked', true);	
						currentInput.disabled= true;						
						$(current).removeClass("active");
						
						$(currentID).removeClass("active");
						$(next).addClass('active');
						$(nextID).addClass('active');
						$(currentID).hide();
						$(nextID).fadeIn();
						maskCurrency(currentID+' input[data-type=Dollars');
						$('#leaseInputMain-loader').fadeOut();
						showRightCalulationBox();
					},
					error: function(jqXHR, textStatus, errorThrown) 
					{
					$('#leaseInputMain-loader').fadeOut();	 
					swal("Error", "unknown error occured, Please try again", "error");	 
						   
					}
		});
	
	}
	else
	{
		showRightCalulationBox();
	}	
	 
});  


$('body').on('click','.cancel-update',function (e) { 
	resultset = 1;
	var current = $('#category-list .label-accordion.active');
	var currentInput = current.prev('input');
	console.log(currentInput);
	
	var currentID = '#'+$(current).attr('data-target');
	var next = current.parent().next().find('label.label-accordion');
	var nextt = next.attr('data-target');
	var nextID = '#'+next.attr('data-target');
	var nextTable = next.attr('data-input-tab');
	var previous = current.parent().prev().find('label.label-accordion');
	var previousID = '#'+previous.attr('data-target');
 
	 				
	$(current).removeClass("active");
	
	$(currentID).removeClass("active");
	$(next).addClass('active');
	$(nextID).addClass('active');
	$(currentID).hide();
	$(nextID).fadeIn();
	
	
	 
});  

/*
	=======================================================
		Do you Have Pets, Resident, Vech.....Input Radio type
    =======================================================
*/	
$(document).on('click','input',function() {
	
		var currentForm = $(this).parent().closest('form'); 
        if (this.value == 'yes') {
			currentForm.find('.switch-field').addClass('active');
			currentForm.find('.leaseInputWrapper.hidden-block').fadeIn();
			currentForm.find('.leaseInputWrapper input[type=text] , .leaseInputWrapper select').each(function (){
			   $(this).prop('disabled',false);
			   validate_leaseInputs(currentForm);
		  });
        }
        else if (this.value == 'no') {
		  currentForm.find('.leaseInputWrapper.hidden-block').fadeOut('slow');  		
          currentForm.find('.switch-field').removeClass('active');
          currentForm.find('.leaseInputWrapper input[type=text] , .leaseInputWrapper select').each(function (){
			   $(this).prop('disabled',true);
			    validate_leaseInputs(currentForm);
		  });
		  
		  
		  
        }
     
});



/*
	=======================================================
		ADD MORE RESIDENTS, PETS, VECH. etc
    =======================================================
*/

$(document).on('click','.add-new-row',function(){
	 
	var currentForm = $(this).parent().closest('form').attr('id');
	var formSelected = $(this).parent().closest('form').clone();
	formSelected.attr('id','addNewInformation');
	formSelected.attr('data-previousID',currentForm);
	formSelected.filter('form').find('.addMoreButtonRow').remove(); //Update formID
	formSelected.filter('form').find('.save-cancel-btn').remove(); //Update formID
	formSelected.filter('form').find('.switch-field').remove(); //Update formID
	formSelected.filter('form').find('.addMoreContactRow').hide(); //Update formID
	var title = formSelected.filter('form').find('.panel-title'); //Update formID
	var currentCount = formSelected.filter('form').find('.autoCounter:last').text();
	 console.log(formSelected);
	formSelected.find('.emergencyRow').not(':last').remove();
	formSelected.find('.emergencyColumn').not(':first').remove();
	
	formSelected.filter('form').find('.panel.panel-default.lease-default-panel').remove(); //Update formID
	var defaultRow = formSelected.filter('form').find('.group_counter:last').find('input[name*="field_grouplabel[]"]');
	
	var groupLabelCount = $(defaultRow.slice(-1)[0]).val();
	 
	 	
	formSelected.filter('form').find('.group_counter').not(':last').remove(); 
	/* first time duplication */
	$(formSelected).find('input[name*="field_grouplabel[]"]').each(function()
		{	var value =groupLabelCount;
			var arr = value.split('_'); 
			console.log(arr[1]);
			var newIndex = parseInt(arr[1])+1;
			var new_label = arr[0]+'_'+newIndex;
			$(this).val(new_label);
		})
		 
	
	 
	/* Add New IDs to the Cloned Fileds */
	$(formSelected).find("input[type=text]").each(function()
	{
		var id = $(this).attr('id');
		var name = $(this).attr('name');
		 
		if(name!==undefined)
		{	var check = name.search('added');
			if(check!=-1)
			{
				$(this).attr('name',name);
				
			}
			else
			{
				$(this).attr('name',"added_"+name);
				
			}
		}		
		$(this).attr('id',"lease_"+id);
		
		if(!$(this).is(':disabled'))
		{
			if(!$(this).attr('data-default'))
			{
				$(this).val('');	
			}
			else
			{
				
				$(this).val($(this).attr('data-default'));
			}
			
		}
		
		else
		{
		  
		}		
	})
	/* Hide Delete Button */
	$(formSelected).find(".delete_row").css('display','none');
	$(formSelected).find(".emergencyColumn .delete_row").css('display','block');
	/* Add New IDs to the Cloned Fileds (select)*/
	$(formSelected).find("select").each(function()
	{	 
		var id = $(this).attr('id');
		var name = $(this).attr('name');
		 
		if(name!==undefined)
		{	var check = name.search('added');
			if(check!=-1)
			{
				$(this).attr('name',name);
				
			}
			else
			{
				$(this).attr('name',"added_"+name);
				
			}
		}		
		$(this).attr('id',"lease_"+id);
		
		if(!$(this).is(':disabled'))
		{
			if(!$(this).attr('data-default'))
			{
				$(this).find('option[selected="selected"]').each(
					function() {
						$(this).removeAttr('selected');
					}
				);

			}
			 
			
		}
		
		else
		{
		  
		}		
	})
	
		
	 
	/* Update the hidden filed name, value remain same */
	$(formSelected).find("input[type=hidden]").each(function()
	{	var name = $(this).attr('name');
		if(name!==undefined)
		{	var findInCurrent = name.search('added');
			if(findInCurrent!=(-1))
			{
				$(this).attr('name',name); 
			}
			else
			{
				$(this).attr('name',"added_"+name); 
			}		
		}
		
		
	})
	/* Update the Title Count */
 
	var newCount = parseInt(currentCount);
	formSelected.filter('form').find('.autoCounter').html('');
	formSelected.filter('form').find('.autoCounter').text(++newCount);
	$('#addmoreModel h4').html(title);
	//$('#'+currentForm).after(formSelected);
	var tar = '#'+currentForm+' .addMoreButtonRow'
	//$(tar).prepend(formSelected);
	$('#addNewForm').html(formSelected);
	datepicker('#addNewInformation');
	validate_leaseInputs('#addNewInformation');
	$('#addmoreModel').modal();
	
})

/*
	=======================================================
		Add New PET, RESIDENT, VECHICLE and Save and Continue
    =======================================================
*/
	$(document).on('click','#modelAddNewSave', function(e){
		resultset = 1;
		var formSelected = $('#addNewInformation');
		var targetId = $('#addNewInformation').attr('data-previousid');
		var targetForm = '#'+targetId+' .group_counter';
		var tt = '#'+targetId;
		var fullValues = $('#addNewInformation .leaseInputWrapper .group_counter');
		//validate_leaseInputs(tt);
		//$('#addNewInformation').trigger("click");
		/* var resi = $('#addNewInformation').trigger('submit',function(e){
			e.preventDefault();	
		});
		console.log(resi); */
		/* prevent from submitt */
		/* $(document).on("submit","#addNewInformation", function (e) {
			e.preventDefault();	
		})	 */	
		/* if No errors(resultset is a global variable declared in leaseinput.php page)*/
		
		/* Counter */
		var currentCount = formSelected.filter('form').find('.autoCounter:last').text();
		var newCount = parseInt(currentCount);
		 
		 
		
		if(resultset)
		{	
	
			    var formdata = $('#addNewInformation').serialize()+' &autocounter='+newCount;
				jQuery.ajax(
				{	type: "POST",
					url : "ajax-leaseinput-main.php",
					data:formdata,
					beforeSend:function(){
						$('#leaseInputMain-loader').fadeIn();
					},
					success:function(response)
					{	var result = response.trim();
					
						if(result=="NewRecordError")
						{	$('#addmoreModel').modal('hide');
							$('#addNewForm').html(''); 
							swal("Error", "unknown error occured, Please try again", "error");
							$('#leaseInputMain-loader').fadeOut();	
						}	
						else
						{	
							var check = $(targetForm).attr('data-leasetable');
							if(check!='RESIDENT')
							{	
								$(targetForm).last().after(response);
							}
							else if(check=='RESIDENT')
							{
								$('#'+targetId+' .emergencyRow').last().after(response);
							}		
							$('#addmoreModel').modal('hide');
							$('#addNewForm').html(''); 
							$('#'+targetId+' .delete_row').css('display','block');	
							$('#leaseInputMain-loader').fadeOut();	
						}
					},
					error: function(jqXHR, textStatus, errorThrown) 
					{
					//$('#leaseInputMain-loader').fadeOut();	 
					swal("Error", "unknown error occured, Please try again", "error");	 
						   
					}
				});
			/* 		
			  $(targetForm).last().after(fullValues);
			$('#'+targetId+' .delete_row').css('display','block');
			$('#addmoreModel').modal('hide');
			$('#addNewForm').html('');   */
		}
		else
		{
			e.preventDefault();
		}
		
	})
/*
	=======================================================
		Delete PET, RESIDENT, VECHICLE and Save and Continue
    =======================================================
*/

    $(document).on('click','.delete_row', function(){
		var current = $(this);
		var leng = $(this).parent().closest('form').find('.group_counter').length;
		var currentForm = $(this).parent().closest('form').attr('id');
		var currentGroupLabel =  $(this).parent().closest('.group_counter').attr('data-grouplabel');
		var currentLeaseTable =$(this).parent().closest('.group_counter').attr('data-leaseTable');
		var groupLabel = 'content_groupLabel='+ currentGroupLabel + '&currentLeaseTable=' + currentLeaseTable;
		 
		var counter = 1; 
		var idform = '#'+currentForm;
		if(--leng>1)
		{
			// Do nothing
		}
		else
		{
			$(this).parent().closest('form').find('.delete_row').css('display','none');
		}
		var block = $(this).parent().closest('.group_counter');
		var emergencyBlock = $(this).parent().closest('.group_counter').attr('data-grouplabel');
		console.log(emergencyBlock);
		
		jQuery.ajax(
			{
				type:"POST",
				url:"ajax-leaseinput-main.php",
				data:groupLabel,
				beforeSend:function(){
					
				},
				success:function(response)
				{
					var result = response.trim();
					if(result=="deleteSuccess")
					{
					    
						$(current).parent().closest('.group_counter').fadeOut(500,function()
						{ 
							$(idform).find("[data-egroupID='" + emergencyBlock + "']").remove();
							$(block).remove(); 
							$(idform+' .autoCounter').each(function(e)
							{
								$(this).html(counter++);
							})
						})	
						$('#leaseInputMain-loader').fadeOut();
						
					}
					
				},
				error:function(jqXHR,textStatus,errorThrown)
				{
					$('#leaseInputMain-loader').fadeOut();
					swal("Error", "unknown error occured, Please try again", "error");	 
					
				}
				
			})
		 
		 
		/* $(this).parent().closest('.group_counter').fadeOut(500,function()
		{
			$(this).remove(); 
			//update the Index  
			 
			$(idform+' .autoCounter').each(function(e){
				 console.log(this);
				$(this).html(counter++);
			}) 
		}); */
		 
	})	
	

/*
	=======================================================
		Add More Emergency Contact Details -- Model
    =======================================================
*/	
	$(document).on('click','.addNewContact',function(){
		 $('#addNewEmergencyCon')[0].reset();
		var currentForm = $(this).parent().closest('form').attr('id');
		var tetb = $(this).parents('.group_counter').closest('.subTitle').text();
		var formSelected = $(this).parents('.emergencyRow').find('.emergencyColumn:last');
		
		var groupLabel = $(this).parents('.emergencyRow:last').attr('data-egroupid');
		$('#previousContactLabel').val(groupLabel);
		var counter = $(formSelected).find('.eAutoCounter:last').text();
		var newCounter = Number(++counter);
		$('#previousContactCounter').val(newCounter);
		$('#addEmergencyContactModel').find('.eAutoCounter').html(newCounter);
		$('#addEmergencyContactModel').modal();
	});
	 
/*
	=======================================================
		Add More Emergency Contact Details -- Save
    =======================================================
*/	
	$(document).on('click','#modelAddNewEcontactSave',function(){
		var targetDivVal = $('#previousContactLabel').val();
		var targetDiv  = "[data-egroupid='"+targetDivVal+"'";
		var formData = $('#addNewEmergencyCon').serialize();
		jQuery.ajax(
			{
				type:"POST",
				url:"ajax-leaseinput-main.php",
				data:formData,
				beforeSend:function(){
						$('#leaseInputMain-loader').fadeIn();
						$('#addEmergencyContactModel').modal('hide');
						
				},
				success:function(response)
				{
					var result = response.trim();
					if(result!="NewRecordError")
					{
					    $(targetDiv).find('.addMoreContactRow').prepend(response);
						$('#leaseInputMain-loader').fadeOut();
						toggleEmgDelete();
						
					}
					
				},
				error:function(jqXHR,textStatus,errorThrown)
				{
					$('#leaseInputMain-loader').fadeOut();
					swal("Error", "unknown error occured, Please try again", "error");	 
					
				}
				
			})
		 
	});
	 

/*
	=======================================================
		Delete Only Emergency Contact Details -- 
    =======================================================
*/	
	$(document).on('click','.emergencyColumn  .contact_delete_row',function()
	{
		
		 
		var currentForm = $(this).parent().closest('form').attr('id');
		if(currentForm=='addNewInformation'){
			
			
		}
		
		var masterGroupLab = $(this).attr('data-egroupmasterlabel');
		var current = $(this).closest('.emergencyColumn');
		var groupLab = $(this).attr('data-egrouplabel');
		var groupLabel = 'masterGroupLab='+ masterGroupLab + '&groupLab=' + groupLab; 
		 
		
		jQuery.ajax(
			{
				type:"POST",
				url:"ajax-leaseinput-main.php",
				data:groupLabel,
				beforeSend:function(){
						$('#leaseInputMain-loader').fadeIn();
						 
						
				},
				success:function(response)
				{
					$(current).remove();
					$('#leaseInputMain-loader').fadeOut(); 
					toggleEmgDelete();
				},
				error:function(jqXHR,textStatus,errorThrown)
				{
					$('#leaseInputMain-loader').fadeOut();
					swal("Error", "unknown error occured, Please try again", "error");	 
					
				}
				
			})		
			
	})	
	 
/*
	=======================================================
		Hide and Show Right Information Bar
    =======================================================
*/
$(document).on('click','.rightInfoBar', function(){
	
	$('.slide-menu').addClass('hidden-menu');
	var windowWidth=window.outerWidth;
	if(windowWidth>1000)
	{
		$('#leaseContentArea').removeClass();
		$('#leaseContentArea').addClass('col-xs-10 col-sm-8 col-md-9');		
	}
	else
	{
		$('#leaseContentArea').removeClass();
		$('#leaseContentArea').addClass('col-xs-10 col-sm-8 col-md-9');		
	}		
	
	$('.leftInfoBar').fadeIn();
	$('.slide-menu').fadeOut();
	hiddenRightCalulationBox();
})
$(document).on('click','.leftInfoBar', function(){
	$('.leftInfoBar').css('display','none');
	$('.slide-menu').removeClass('hidden-menu');
	var windowWidth=window.outerWidth;
	if(windowWidth>1000)
	{
			 
			$('#leaseContentArea').removeClass();
			$('#leaseContentArea').addClass('col-xs-10 col-sm-4 col-md-6');
	}	
	else
	{
		$('#leaseContentArea').removeClass();
		$('#leaseContentArea').addClass('col-xs-10 col-sm-8 col-md-9');	
	}	
	
	$('.slide-menu').fadeIn();
	hiddenRightCalulationBox();
})

	/*
	Function to toggle right  side menu 
	*/
$(window).resize(function () {
	
var windowWidth=window.outerWidth;
 console.log(windowWidth);
 if(windowWidth<=1000)
 {
	if($('.slide-menu').hasClass('hidden-menu'))
	{
		// do nothing
	}	
	else
	{
		$(".slide-menu .rightInfoBar").click();
	}	
 }	
	
})	
 

	/*
	function to check the window size on window load 
	*/
	
function windowLoad(){
	 var windowWidth=window.outerWidth;
	 if(windowWidth<=1000)
	 {
		if($('.slide-menu').hasClass('hidden-menu'))
		{
			// do nothing
		}	
		else
		{
			$(".slide-menu .rightInfoBar").click();
		}	
	 }
}	
	
/*
	=======================================================
		Date Picker
    =======================================================
*/
 function datepicker(id)
 {
	$(id).each(function(){
		$(this).find('input[data-type="Date"]').each(function() {
			$(this).attr("data-date-format","mm/dd/yyyy");
			var date_input = $(this);
			date_input.datepicker({
            format: 'mm/dd/yyyy',
			todayHighlight: true,
			autoclose:false,
			onClose: function(){
			dateCalculations($(this).val());
				}
            })
		});
	});
}


/*
	=======================================================
		Set Date Picker
    =======================================================
*/
$(document).ready(function(){
	datepicker('form');
	maskCurrency('input[data-type=Dollars');
	var it =0;
	validate_leaseInputs('form');
	toggleEmgDelete();
	updateLeftNav();
	
})

/* $(document).on('click', '#category-list input', function(e) {
    if($(this).is(':checked')==true)
	{	e.preventDefault();
		return false;
	}
        //e.preventDefault();
}); */
/*
	=======================================================
		Validation for Main Lease Input Page
    =======================================================
*/

function validate_leaseInputs(target_accordion_id){
		
	$(target_accordion_id+" input[type=text]").each(function() 
	{
            var id = "#"+$(this).attr("id");
			 
			var type = $(this).attr("data-type");
			var dataRequired = 0;
			if($(this).attr("data-required"))
			{
				 dataRequired = 1;
			} 
			var currentVal = $(this).val();
		   jQuery(function()
		   {
			   
			   if(type=="Text" || type=="text")
			   {
					if(dataRequired)
					{   
						jQuery(id).validate({
							  
							expression: "if (VAL  &&  $.trim(VAL)!='') return true; else return false;",
							message: "Value Required ",
							 
							
						});
					}
					else
					{
						jQuery(id).validate({
							  
							expression: "if ($.trim(VAL)) return true; else return true;",
							message: "Value Required ",
							 
							
						});
					}	
			   }
			  else if(type=="Number")
			  {		 
					if(dataRequired)
					{
						jQuery(id).validate({
							 
							//expression: "if (VAL.match(/^[0-9]*$/) && VAL) return true; else return false;",
							expression: "if (VAL.match(/^[0-9]*$/) && VAL) return true; else return false;",
							message: "Please enter a valid integer"
						});
					}
					else
					{
						jQuery(id).validate({
							 
							expression: "if (VAL.match(/^[0-9]*$/) || '') return true; else return false;",
							message: "Please enter a valid integer"
						});
					}	
			  }	
			  else if(type=="Phone")
			  {		 
					if(dataRequired)
					{
						jQuery(id).validate({
							 
							//expression: "if (VAL.match(/^[0-9]*$/) && VAL) return true; else return false;",
							expression: "if ((VAL.match(/^[0-9]*$/)) && (VAL.length == 10) && VAL) return true; else return false;",
							message: "Please enter a valid integer"
						});
					}
					else
					{
						jQuery(id).validate({
							 
							expression: "if ((VAL.match(/^[0-9]*$/)  && (VAL.length == 10)) || '') return true; else return false;",
							message: "Please enter a valid integer"
						});
					}	
			  }
			  else if(type=="Zip")
			  {		 
					if(dataRequired)
					{
						jQuery(id).validate({
							 
							//expression: "if (VAL.match(/^[0-9]*$/) && VAL) return true; else return false;",
							expression: "if ((VAL.match(/^[0-9]*$/)) && (VAL.length == 5) && VAL) return true; else return false;",
							message: "Please enter a valid integer"
						});
					}
					else
					{
						jQuery(id).validate({
							 
							expression: "if ((VAL.match(/^[0-9]*$/) && (VAL.length == 5))|| '') return true; else return false;",
							message: "Please enter a valid integer"
						});
					}	
			  }
			  else if(type=="Year")
			  {		 
					if(dataRequired)
					{	
						jQuery(id).validate({
							 
							//expression: "if (VAL.match(/^[0-9]*$/) && VAL) return true; else return false;",
							expression: "if ((VAL.match(/^[0-9]*$/)) && (VAL.length == 4) && VAL) return true; else return false;",
							message: "Please enter a valid integer"
						});
					}
					else
					{	 
						jQuery(id).validate({
							 
							expression: "if (VAL.match(/^[0-9]{4}$/) || VAL=='') return true; else return false;",
							message: "Please enter a valid integer"
						});
					}	
			  }
			  else if(type=="%")
			  {		 
					if(dataRequired)
					{
						jQuery(id).validate({
							 
							//expression: "if (VAL.match(/^[0-9]*$/) && VAL) return true; else return false;",
							expression: "if ((VAL.match(/^[0-9.]*$/))  && VAL) return true; else return false;",
							message: "Please enter a valid integer"
						});
					}
					else
					{
						jQuery(id).validate({
							 
							expression: "if (VAL.match(/^[0-9.]*$/) || '') return true; else return false;",
							message: "Please enter a valid integer"
						});
					}	
			  }
			  else if(type=="Dollars")
			  {
				if(dataRequired)
				{
					 
					jQuery(id).validate({
						expression: "if ((/^[0-9.,$\b]+$/) && VAL) return true; else return false;",
						message: "Please enter a valid Amount"
					}); 
					 
				}
				else
				{
					 
					jQuery(id).validate({
					expression: "if ((/^[0-9.,$\b]+$/) || '') return true; else return false;",
					message: "Please enter a valid Amount"
					});
					 
				}	
				
			  }
			else if(type=="Date")
			{
				if(dataRequired)
				{
					jQuery(id).validate(
					{	 
						expression: "if (!isValidDate(parseInt(VAL.split('/')[2]), parseInt(VAL.split('/')[0]), parseInt(VAL.split('/')[1]))) return false; else return true;",
						message: "Please enter a valid Date"
					});
				}
				else
				{	 
						jQuery(id).validate(
						{	 
							expression: "if ((isValidDateOptional(VAL))) return false; else return true;",
							message: "Please enter a valid Date"
						});
					 	
				}	
				
			}
			else if(type=="Email")
			{
				if(dataRequired)
				{
					jQuery(id).validate(
					{	 
						expression: "if (VAL.match(/^[^\\W][a-zA-Z0-9\\_\\-\\.]+([a-zA-Z0-9\\_\\-\\.]+)*\\@[a-zA-Z0-9_]+(\\.[a-zA-Z0-9_]+)*\\.[a-zA-Z]{2,4}$/)) return true; else return false;",
						message: "Please enter a valid Email ID"
					});
				}
				else
				{
					jQuery(id).validate(
					{	 
						expression: "if (VAL.match(/^[^\\W][a-zA-Z0-9\\_\\-\\.]+([a-zA-Z0-9\\_\\-\\.]+)*\\@[a-zA-Z0-9_]+(\\.[a-zA-Z0-9_]+)*\\.[a-zA-Z]{2,4}$/) || '') return true; else return false;",
						message: "Please enter a valid Email ID"
					});
				}	
				
			}
			
			
					
					
			  
		   })		
        });
		
		
		 
		$(target_accordion_id+" select").each(function() 
		{
			 	
				var id = "#"+$(this).attr("id");
				var type = $(this).attr("data-type");
				var dataRequired = 0;
				if($(this).attr("data-required"))
				{
					 dataRequired = 1;
				} 
				jQuery(function()
				{
					
					if(type=="Dropdown")
					{
						if(dataRequired)
						{

							jQuery(id).validate(
							{	 
								expression: "if (VAL != '0') return true; else return false;",
								message: "Please make a valid selection"
							});
						}
						else
						{
							jQuery(id).validate(
							{	 
								expression: "if (VAL) return true; else return false;",
								message: "Please make a valid selection"
							});
						}	
						
					}
				   
				});  
			 	
			 
		});	
		
		
 }
 
 
  /*
	=======================================================
		Upate Left Navigation
    =======================================================
*/
 $('input').focusout(function(e)
 {
	 updateLeftNav();
 })
 
 
 
 /*
	=======================================================
		Date Calculation
    =======================================================
*/
 
/* --------------------------------------------------------
	Date Terms in Month Allow Only +ve Numbers
----------------------------------------------------------- */ 
$('input[data-fieldname=termMonth]').keyup(function(e)
                                {
  if (/\D/g.test(this.value))
  {
    // Filter non-digits from input value.
    this.value = this.value.replace(/\D/g, '');
  }
});

 
 /* --------------------------------------------------------
	Date Terms - Terms field value Chnaged
----------------------------------------------------------- */ 
 $(document).on('change', 'input[data-fieldname=termMonth]', function(){
	if (this.value.length > 3) {
        this.value = this.value.slice(0,4); 
    }
	var formId = $(this).closest('form').attr('id');
	var monthVal =  $(this).val();
	var addMonths = parseInt(monthVal,10)
  
	if(!isNaN(addMonths))
	{
		var temp = $('#'+formId).find('input[data-fieldname=startDate]').datepicker('getDate');
		if(temp == '' || temp == undefined)
		{
			 // do Nothing 
		}	
		else
		{	
		var date = new Date(temp);
		date.setMonth(date.getMonth() + addMonths);
		$('#'+formId).find('input[data-fieldname=endDate]').datepicker("setDate", date) 
		}
	} 
	 
 })
 
/* --------------------------------------------------------
	Date Terms - StartDate value Chnaged
----------------------------------------------------------- */ 
$('input[data-fieldname=startDate]').datepicker().on('changeDate', function() {
  var formId = $(this).closest('form').attr('id');
  var monthVal =  $('#'+formId).find('input[data-fieldname=termMonth]').val();
  var addMonths = parseInt(monthVal,10)
  
  if(!isNaN(addMonths))
  {
	var temp = $(this).datepicker('getDate');
	var date = new Date(temp);
	date.setMonth(date.getMonth() + addMonths);
	$('#'+formId).find('input[data-fieldname=endDate]').datepicker("setDate", date) 
  }	  
	  
  
  //$("#LEASETERMS_2").datepicker("setDate", date);
  
});
 
 
 
 
 
/* --------------------------------------------------------
	Masking Function for currency
----------------------------------------------------------- */
function maskCurrency(inputlist){
		$(inputlist).each(function()
		{
			$(this).inputmask("currency", {
				digits: 2,
				//placeholder: "",
				//clearIncomplete: true,
				colorMask: false,
				rightAlign: false
				//prefix: ''
				
			});
		})
	} 
/* --------------------------------------------------------
	UnMasking  Function for currency
----------------------------------------------------------- */	
function unmaskCurrency(inputlist){
	$(inputlist).each(function()
	{
		var currentVal = $(this).inputmask('unmaskedvalue');
		$(this).inputmask('remove');
		$(this).val(currentVal);
	})
} 
 
 
 
  
/* --------------------------------------------------------
	Allow Only Numbers (NUMBER Fields)
----------------------------------------------------------- */ 
$('input[data-type="Zip"], input[data-type="Phone"], input[data-type="Year"]').keyup(function(e)
                                {
  if (/\D/g.test(this.value))
  {
    // Filter non-digits from input value.
    this.value = this.value.replace(/\D/g, '');
  }
});

 
 
 
 
/* --------------------------------------------------------
	Hide/Show Delete Button for emergency contacts
----------------------------------------------------------- */	
function toggleEmgDelete(){ 
 $('.resident_block.group_counter[data-grouplabel]').each(function()
	{
		var label = $(this).attr('data-grouplabel');
		var contactCount = $('.resident_block.emergencyRow[data-egroupid='+label+'] .emergencyColumn').length;
		
		if(contactCount<=1)
		{
			$('.resident_block.emergencyRow[data-egroupid='+label+'] .emergencyColumn .leaseEmergency .contact_delete_row').each(function(e){
				$(this).hide();
			});	
		}
		else
		{
			$('.resident_block.emergencyRow[data-egroupid='+label+'] .emergencyColumn .leaseEmergency .contact_delete_row').each(function(e){
				$(this).show();
			});
		}
		var counter = 1;
		/* Update the Counter */
		$('.resident_block.emergencyRow[data-egroupid='+label+'] .emergencyColumn .leaseEmergency .eAutoCounter').each(function(e){
			
			$(this).text(counter++);
		})
		
		
	})
}	
 
 
/* --------------------------------------------------------
	Update Left Navigation - Check/Uncheck
----------------------------------------------------------- */	 
function updateLeftNav()
	{
		 
		$("form.form-block").each(function()
		{
			var current = $(this);
			var id = current.attr('id');
			var checkDiv = $('#category-list'); 
			var check = 1;
			$(current).find('input[data-required=required]').each(function()
			{
				if($(this).val()!=''){
					//do nothing
				}
				else
				{
				   check =0;
				}
			
			}) 
			
			if(check==1)
			{
				$("#category-list").find('input[data-target='+id+']').each(function()
				{
					$(this).prop('checked', true);
					$(this).prop('disabled', true);
					 
					
				})
			}
			else
			{
				$("#category-list").find('input[data-target='+id+']').each(function()
				{
					$(this).prop('checked', false);
					$(this).prop('disabled', true);
					 
				})
			}		
			
		})
	} 
 
 
 /* --------------------------------------------------------
	Show Right Caluation box
----------------------------------------------------------- */	
 
 function  showRightCalulationBox()
 {
	var id = $('.label-accordion.active').attr('data-target'); 	
	if(id == 'lease-input-FINANCIALS')
	 {
		 $('.fin_accumulated').fadeIn();
	 }
	else
	{
		$('.fin_accumulated').fadeOut();
	}		
 }
 
 function hiddenRightCalulationBox(){
	var sidenavRight = $('.slide-menu.hidden-menu').length;
	if(sidenavRight)
	{
		$('#rightCalBox .fin_accumulated').appendTo( "#bottomCalBox" );
	}	
	else
	{
		$('#bottomCalBox .fin_accumulated').appendTo( "#rightCalBox" );
	}	
 }
 
 /* --------------------------------------------------------
	Scroll Fixed Right Calulation div
----------------------------------------------------------- */	 
 
 
 function sticky_relocate() {
    
	
	var window_top = $(window).scrollTop();
	var topNavHeight = $('.navbar.navbar-default').outerHeight();
    
	/* for the Calulatyion Block on Right Nav Bar */
 	var div_top = $('#sticky-anchor').offset().top-topNavHeight;
    if (window_top > div_top) {
        $('#rightCalBox .fin_accumulated').addClass('stickyBox');
        $('#rightCalBox .fin_accumulated').css('top',topNavHeight+'px');
		$('#sticky-anchor').css('height','auto');
    } else {
        $('#rightCalBox .fin_accumulated').removeClass('stickyBox');
		 $('#rightCalBox .fin_accumulated').css('top','auto');
        $('#sticky-anchor').height(0);
    }
	
 
	
	
 
}

$(function() {
    $(window).scroll(sticky_relocate);
    sticky_relocate();
});
 
 
 
 
 
 
 