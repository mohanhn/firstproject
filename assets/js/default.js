    
	/*
	=======================================================
	
					BUILD YOUR TEMPLATE
	
	=======================================================
					Active Tab  Setup CSS
    =======================================================
	*/
	$('.nav-tabs .active a').css({'color':'#f58220'}); 
	
	$(document).on('click','.nav-tabs a',function(e){ 
	
		$('.nav-tabs a').tab('show').css('color','#555555');  
		$(this).tab('show').css({'color':'#f58220'}); 
	});
	$(".nav-tabs li a").css({'border':'2px solid transparent'});	
	
	
	/*
	=======================================================
					Input changes 
    =======================================================
	*/
	
	
	/* $("form").dirrty().on("dirty", function(){
			$("#status").html("dirty");
			$(".save-changes").removeAttr("disabled");
		}).on("clean", function(){
			$("#status").html("clean");
			$(".save-changes").attr("disabled", "disabled");
		});
	  */
	/*
	=======================================================
					New template Name 
    =======================================================
	*/
	$('body').on('click','.add_new_template',function () 
	{ 
		swal({
		  title: "Template Name",
		  type: "input",
		  showCancelButton: true,
		  closeOnConfirm: false,
		  animation: "slide-from-top",
		  showLoaderOnConfirm: true,
		  inputPlaceholder: "Enter new template name"
		},
		function(inputValue)
		{
			if (inputValue === false) return false;
			
			else if (inputValue === "")
			{
				swal.showInputError("Please enter template name");
				return false
			}
			else if(checkMaxLength(inputValue,40))
			{
				swal.showInputError("Template name is too long, allowed 40 characters only");
				return false 
			}
			else if (checkifname_taken(inputValue)=="exists") 
			{   
				swal.showInputError("Name exists, Please enter a new template name");
				return false
			}
		 
			else
			{
				document.getElementById("setTemplateName").value = inputValue;
				var myData = 'content_txt='+ inputValue;
				var formURL = $(this).attr("action");
				jQuery.ajax(
				{	type: "POST",
					url : "ajax/ajax-template.php",
					//dataType:"text",
					data:myData,
					success:function(response)
					{	 
						var result = check_result(response);
						if(result==true){
							$('#template_name_list').load(document.URL + ' #template_name_list',function(){
								$(".template-sec").fadeOut( "slow" );
							});		
						}
						else{
							swal("Error", "New template was not created successfuly, please try again", "error");	
						}
					},
					error: function(jqXHR, textStatus, errorThrown) 
					{
						 
					swal("Error", "unknown error occured, Please try again", "error");	 
						   
					}
				})
			}
		});
	});
	

	/*
	=======================================================
					Save changes 
    =======================================================
	*/
	 
	$('body').on('click','.save-changes',function (e) { 
		$("input").removeAttr("disabled");
	    var formId=$(this).attr("data-formId");
		formId = "#"+formId+" form";
		 
		swal({
			  title: "Save Changes",
			  text: "Continue to save changes",
			  type: "info",
			  showCancelButton: true,
			  closeOnConfirm: false,
			  showLoaderOnConfirm: true,
			},
			function(){
			  //e.preventDefault(); //STOP default action		
				var form = $(formId);
				var formdata = form.serialize();	
				jQuery.ajax(
				{	type: "POST",
					url : "ajax/ajax-template.php",
					dataType:"text",
					data:formdata,
					success:function(response, data){
						//check_result(response);
						var red = response.trim();
						 
						swal("SUCCESS","Changes updated.", "success");
						active_tab();
					},
					error: function(jqXHR, textStatus, errorThrown) 
					{
						swal("Error", "unknown error.", "error");   
						 
					}
				})
				e.preventDefault(); //STOP default action  
			  
		});	
		return true;
	});  
	
	
	
	
	/*
	=======================================================
		show template on selection of template name
    =======================================================
	*/
	$(document).on('change', '#select_template', function(e){
	
		if($(this).val() !="")
		{	
			var selval = $(this).val();
			 
			var sel_inner_html = $("#select_template option:selected").text();
			
			//console.log(sel_inner_html);
			var selval = selval.trim();
			var optionval = 'option_val='+ selval;
			
			jQuery.ajax(
			{	type: "POST",
				url : "ajax/ajax-template.php",
				dataType:"text",
				data:optionval,
				beforeSend:function(){
					$('.pre_loader_block').fadeIn();
					$(".template-sec").fadeOut();
					$('#template_content_area').html('');
					
				},
				success:function(response, data){
					
					$('#template_content_area').html(response);
					setTemplateLabel(selval, sel_inner_html);
					$(".template-sec").fadeIn();
					$('.pre_loader_block').fadeOut();
					active_tab();
					$('.nav-tabs .active a').css({'color':'#f58220'}); 
					  $('[data-tooltip="tooltip"]').tooltip(); 
					  $('[data-tooltip="tooltip"]').hover(function(){
							$('.tooltip-inner').css('background', 'grey');
							$('.tooltip-arrow').css('border-top-color', 'grey');
					   });
					navigationBarCat();  
					calculation_setup();
					
					  
				},
				error: function(jqXHR, textStatus, errorThrown) 
				{
					//if fails   
				 
				}
			})
			
			e.preventDefault(); //STOP default action    
		}
	});
	
	/*
	=======================================================
		Delete selected template
    =======================================================
	*/
	$(document).on('click', '.delete_template', function(e)
	{
		var formId=$(this).attr("data-templte-id");
		 
		swal({
		   title: 'Are you sure?',
		   text: "You won't be able to revert this!",
		   type: 'warning',
		  showCancelButton: true,
		  confirmButtonColor: "#DD6B55",
		  confirmButtonText: "Yes, Delete!",
		  closeOnConfirm: false,
		  showLoaderOnConfirm: true,
		  
		},
		function(){
			  //e.preventDefault(); //STOP default action		
				var delete_template_id = 'delete_template_id='+ formId;
				jQuery.ajax(
				{	type: "POST",
					url : "ajax/ajax-template.php",
					dataType:"text",
					data:delete_template_id,
					success:function(response, data){
						//check_result(response);
						//active_tab();
						swal("SUCCESS","Template Deleted", "success");
						$('#template_name_list').load(document.URL + ' #template_name_list');
						$(".template-sec").fadeOut( "slow" );
						 
					},
					error: function(jqXHR, textStatus, errorThrown) 
					{
						swal("Error", "unknown error.", "error");   
						 
					}
				})
				e.preventDefault(); //STOP default action  
			  
		});	
		return true;
	
	});
	
	/*
	=======================================================
		Copy Template
    =======================================================
	*/
	$(document).on('click', '#model-select-template-button2', function(e)
	{
		var selected = $('#select_template option:selected').val();
		var selected_template = 'copy_selected_template='+ selected;
		jQuery.ajax({
			type:"POST",
			url:"ajax/ajax-template.php",
			dataType:"text",
			data:selected_template,
			beforeSend:function(){
				$('.fixed_loader_block').fadeIn();
				$('#template_copy').modal('hide')
			},
			success:function(response){
				var result = response.trim();
				if(result == 'templateCopySuccess')
				{	 
					$('.fixed_loader_block').fadeOut(); 
					$('#select_template').trigger('change'); 
					
					return true;	
					
				}
				else
				{
				  swal("Error", "unknown error.", "error");   
				  $('.fixed_loader_block').fadeOut();
				  return false;
				}				
				
				
				 
			},
		})
	})



$('body').on('click','.copy_template',function () 
	{ 
		swal({
		  title: "Duplicate Template",
		  type: "input",
		  showCancelButton: true,
		  closeOnConfirm: false,
		  animation: "slide-from-top",
		  showLoaderOnConfirm: true,
		  inputPlaceholder: "Enter new template name"
		},
		function(inputValue)
		{
			if (inputValue === false) return false;
			
			else if (inputValue === "")
			{
				swal.showInputError("Please enter template name");
				return false
			}
			else if(checkMaxLength(inputValue,40))
			{
				swal.showInputError("Template name is too long, allowed 40 characters only");
				return false 
			}
			else if (checkifname_taken(inputValue)=="exists") 
			{   
				swal.showInputError("Name exists, Please enter a new template name");
				return false
			}
		 
			else
			{
				document.getElementById("setTemplateName").value = inputValue;
				var selected = $('#select_template option:selected').val();
				var myData = 'duplicatecontent_txt='+ inputValue+'&copy_selected_template='+ selected;;
				var formURL = $(this).attr("action");
				jQuery.ajax(
				{	type: "POST",
					url : "ajax/ajax-template.php",
					//dataType:"text",
					data:myData,
					success:function(response)
					{	 
						var result = response.trim();
						if(result=='templateCopySuccess'){
							$('#template_name_list').load(document.URL + ' #template_name_list',function(){
								$(".template-sec").fadeOut( "slow" );
								swal("Success", "Template Duplicated successfuly, Please select the template from dropdown", "success");
							});		
						}

						else if(result=='templateCopyError'){
							swal("Error", "New template was created successfuly but failed to duplicate values, please try again", "error");	
						}
						else{
							swal("Error", "New template was not Duplicated successfuly, please try again", "error");	
						}
					},
					error: function(jqXHR, textStatus, errorThrown) 
					{
						 
					swal("Error", "unknown error occured, Please try again", "error");	 
						   
					}
				})
			}
		});
	});


	/*
	=======================================================
		Update the row based on selection 
    =======================================================
	*/
	$(document).on('change', 'input[type="checkbox"]', function()
	{
		 /*
		$ar[0].....select
		$ar[1].....optlabel
		$ar[2].....defaultreq
		$ar[3].....req
		$ar[4].....hide
		$ar[5].....edit
		*/
		var select = this.name;
		var arr = select.split("["); 
		var field_name = arr[0];
		
		/* if 'select' is not checked */
		if($( "input[name='"+field_name+"[0]"+"']" ).is(':checked'))
		{	
			enable_edit(field_name);
			
			/* if reuired is checked */
			if((this.name == field_name+"[3]") || (this.name == field_name+"[5]") || (this.name == field_name+"[4]") || (this.name == field_name+"[2]"))
			{	 
				check_required(field_name);
			}
			/* if edit is checked */
			/* else if(this.name == field_name+"[5]")
			{
				check_required(field_name);
			} */
			/* if hide is checked */
			/* else if(this.name == field_name+"[4]")
			{
				check_required(field_name);
			} */
			/* else if(this.name == field_name+"[2]")
			{
				check_required(field_name);
			}	 */
			
		} 
		/* if not selected */
		else
		{
			disable_edit(field_name);
		}
		 
	})	



  
	
	
	
	/* --------------------------------------------------------
		Server side response, function to check if template 
		created successfuly or not
	----------------------------------------------------------- */
	
	/*
		"success"		 -- Successfuly Created
		"exists"  		 -- Template name  exists
		"upateSuccess"   --
		"upateError"	 --	
	*/
	
	function check_result(response)
	{	
	    var response = response.trim();
		if(response == "exists")
		{	swal.showInputError("Name already exists");
			return false;
		}
		else if(response == "success")
		{	 
			swal("Success!", "New template created.", "success");
			return true;
		}
		else if(response == "upateSuccess")
		{
			swal("SUCCESS","Changes updated.", "success");
			return true;
		}
		else if(response=="upateError")
		{
			swal("Error", "unknown error.", "error");
			return false;
		}
		else{
			swal.showInputError("Unknown error Occured");
			return false;
		}
	}
	
	
	/* --------------------------------------------------------
		function to check max length of template name
	----------------------------------------------------------- */
	function checkMaxLength (text, max) 
	{
		return (text.length >= max);
	}
	
	/* --------------------------------------------------------
		Cleint side check for the new profile name existance 
	----------------------------------------------------------- */
	function checkifname_taken(inputValue)
	{
		var result;
		$("#select_template option").each(function(){
			var input = $(this).text(); 
			if(input == inputValue)
			{
				result ="exists";
			}
		})
		return(result); 
	}
	
	/* --------------------------------------------------------
		Cancel, exit without saving Build Template Page
	----------------------------------------------------------- */
	$('body').on('click','.cancel-changes',function () 
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
		  $(".template-sec").fadeOut();
		  $('#template_content_area').html('');
		  $('#template_name_list').load(document.URL + ' #template_name_list');
		  swal("Canceled!", "Your data was not saved.", "error");
		  
		});
    });	
	 
	  
	/* --------------------------------------------------------
		Active Tab 
	----------------------------------------------------------- */ 
	function active_tab(){
		 var taget_id = $("#pnProductNavContents a[aria-selected='true']").attr("href");
		 $("#panels .tab-pane.active").removeClass("active");
		 $(taget_id).addClass("active");
		
	 }
	
	/* --------------------------------------------------------
		Show selected Template name above tabs
	----------------------------------------------------------- */ 
    function setTemplateLabel(selval, sel_inner_html)
	{
		$(".delete_template").attr("data-templte-id",selval);
		$(".template_name_show").html(sel_inner_html);
	}
  	
	
	/* --------------------------------------------------------
		Function to update show active tab
	----------------------------------------------------------- */ 	
	$(document).ready(function () {
		 $(document).ajaxComplete(function () {
			 active_tab();
			 checkbox();
			 calculation_setup();
			   
		 });
		 
		if(($('#loadTemplateId').attr('data-val'))!=='')
		{	var val = $('#loadTemplateId').attr('data-val');
			$('#select_template').val(val).trigger('change');
			$('#loadTemplateId').attr('data-val','');
		}	
		$('#loadTemplateId').attr('data-val','');
	});
	 
	 
	/* $('input[type="checkbox"]').change(function() {
         if($(this).is(":checked")) {
			 
            var returnVal = confirm("Are you sure?");
            $(this).attr("checked", returnVal);
        } 
           
    }); */	 
	 
	/* --------------------------------------------------------
		Initally disable all the fileds if select is not checked
	----------------------------------------------------------- */ 	 
 
	function checkbox()
	{
		var substr = $(".select_check");
		var i;
		for (i = 0; i < substr.length; ++i)
		{
			var select = substr[i].name;
			var arr = select.split("["); 
			var field_name = arr[0];
			
			if($( "input[name='"+field_name+"[0]"+"']" ).is(':checked'))
			{	
				enable_edit(field_name);
				
			}
			else
			{
				disable_edit(field_name);
			}
		}
		
		
	}

	/* --------------------------------------------------------
		Function - Enable edit options
	----------------------------------------------------------- */
	function enable_edit(field_name)
	{
		$( "input[name='"+field_name+"[1]"+"']" ).prop('disabled', false);
		$( "input[name='"+field_name+"[2]"+"']" ).prop('disabled', false);
		$( "input[name='"+field_name+"[3]"+"']" ).prop('disabled', false);
		$( "input[name='"+field_name+"[4]"+"']" ).prop('disabled', false);
		$( "input[name='"+field_name+"[5]"+"']" ).prop('disabled', false); 
		
		/* if($( "input[name='"+field_name+"[3]"+"']" ).is(':checked'))
		{
			if($( "input[name='"+field_name+"[2]"+"']" ).is(':checked'))
			{
				$( "input[name='"+field_name+"[2]"+"']" ).prop('disabled', true);	
			}
			else
			{	$( "input[name='"+field_name+"[2]"+"']" ).prop('checked', true);
				$( "input[name='"+field_name+"[2]"+"']" ).prop('disabled', true);	
			}
		}

		if($( "input[name='"+field_name+"[4]"+"']" ).is(':checked'))
		{
			if($( "input[name='"+field_name+"[2]"+"']" ).is(':checked'))
			{
				$( "input[name='"+field_name+"[2]"+"']" ).prop('disabled', true);	
			}
			else
			{	$( "input[name='"+field_name+"[2]"+"']" ).prop('checked', true);
				$( "input[name='"+field_name+"[2]"+"']" ).prop('disabled', true);	
			}
		} */	
	}	
 
 
	/* --------------------------------------------------------
		Function - Disable edit options
	----------------------------------------------------------- */
	function disable_edit(field_name)
	{
		$( "input[name='"+field_name+"[1]"+"']" ).prop('disabled', true);
		$( "input[name='"+field_name+"[2]"+"']" ).prop('disabled', true);
		$( "input[name='"+field_name+"[3]"+"']" ).prop('disabled', true);
		$( "input[name='"+field_name+"[4]"+"']" ).prop('disabled', true);
		$( "input[name='"+field_name+"[5]"+"']" ).prop('disabled', true);
	}	 

   /* --------------------------------------------------------
		Function - Disable edit options
	----------------------------------------------------------- */

	function check_required(field_name)
	{	 
		/* required selected  */
		if($( "input[name='"+field_name+"[3]"+"']" ).is(':checked'))
		{
			
			if(($( "input[name='"+field_name+"[2]"+"']" ).prop('checked') == false)||($( "input[name='"+field_name+"[2]"+"']" ).prop('checked') == true))
			{	
				if(($( "input[name='"+field_name+"[5]"+"']" ).prop('checked') == false)&&($( "input[name='"+field_name+"[4]"+"']" ).prop('checked') == false))
				{
					$( "input[name='"+field_name+"[2]"+"']" ).prop('checked', true);
					$( "input[name='"+field_name+"[2]"+"']" ).prop('disabled', true);	
					$( "input[name='"+field_name+"[5]"+"']" ).prop('disabled', false);	
				}
				if(($( "input[name='"+field_name+"[5]"+"']" ).prop('checked') == false)&&($( "input[name='"+field_name+"[4]"+"']" ).prop('checked') == true))
				{
					$( "input[name='"+field_name+"[2]"+"']" ).prop('checked', true);
					$( "input[name='"+field_name+"[2]"+"']" ).prop('disabled', true);	
					$( "input[name='"+field_name+"[5]"+"']" ).prop('disabled', false);	
					$( "input[name='"+field_name+"[5]"+"']" ).prop('checked', false);
				}
				if(($( "input[name='"+field_name+"[5]"+"']" ).prop('checked') == true)&&($( "input[name='"+field_name+"[4]"+"']" ).prop('checked') == true))
				{
					$( "input[name='"+field_name+"[2]"+"']" ).prop('checked', true);
					$( "input[name='"+field_name+"[2]"+"']" ).prop('disabled', true);	
					$( "input[name='"+field_name+"[5]"+"']" ).prop('disabled', false);	
					$( "input[name='"+field_name+"[5]"+"']" ).prop('checked', false);
				}
				if(($( "input[name='"+field_name+"[5]"+"']" ).prop('checked') == true)&&($( "input[name='"+field_name+"[4]"+"']" ).prop('checked') == false))
				{
					$( "input[name='"+field_name+"[2]"+"']" ).prop('checked', false);
					$( "input[name='"+field_name+"[2]"+"']" ).prop('disabled', false);	
					$( "input[name='"+field_name+"[5]"+"']" ).prop('disabled', false);	
					//$( "input[name='"+field_name+"[5]"+"']" ).prop('checked', false);
				}
				
				
			}
			 
		}
		if($( "input[name='"+field_name+"[3]"+"']" ).prop('checked') == false)
		{
			
			if(($( "input[name='"+field_name+"[2]"+"']" ).prop('checked') == false)||($( "input[name='"+field_name+"[2]"+"']" ).prop('checked') == true))
			{	
				if(($( "input[name='"+field_name+"[5]"+"']" ).prop('checked') == false)&&($( "input[name='"+field_name+"[4]"+"']" ).prop('checked') == false))
				{
					//$( "input[name='"+field_name+"[2]"+"']" ).prop('checked', true);
					$( "input[name='"+field_name+"[2]"+"']" ).prop('disabled', false);	
					$( "input[name='"+field_name+"[5]"+"']" ).prop('disabled', false);	
				}
				else if(($( "input[name='"+field_name+"[5]"+"']" ).prop('checked') == false)&&($( "input[name='"+field_name+"[4]"+"']" ).prop('checked') == true))
				{
					$( "input[name='"+field_name+"[2]"+"']" ).prop('checked', true);
					$( "input[name='"+field_name+"[2]"+"']" ).prop('disabled', true);	
					$( "input[name='"+field_name+"[5]"+"']" ).prop('disabled', false);	
					$( "input[name='"+field_name+"[5]"+"']" ).prop('checked', false);
				}
				else if(($( "input[name='"+field_name+"[5]"+"']" ).prop('checked') == true)&&($( "input[name='"+field_name+"[4]"+"']" ).prop('checked') == true))
				{
					$( "input[name='"+field_name+"[2]"+"']" ).prop('checked', true);
					$( "input[name='"+field_name+"[2]"+"']" ).prop('disabled', true);	
					$( "input[name='"+field_name+"[5]"+"']" ).prop('disabled', false);	
					$( "input[name='"+field_name+"[5]"+"']" ).prop('checked', false);
				}
				else if(($( "input[name='"+field_name+"[5]"+"']" ).prop('checked') == true)&&($( "input[name='"+field_name+"[4]"+"']" ).prop('checked') == false))
				{
					//$( "input[name='"+field_name+"[2]"+"']" ).prop('checked', false);
					$( "input[name='"+field_name+"[2]"+"']" ).prop('disabled', false);	
					$( "input[name='"+field_name+"[5]"+"']" ).prop('disabled', false);	
					//$( "input[name='"+field_name+"[5]"+"']" ).prop('checked', false);
				}
			}
		}
		
		
	}
	
 
 
 
 /* ===============================================================
 
		TEMPLATE ASSIGNMENT PAGE
 
 ===================================================================
 
 
 
 	 /*
	=======================================================
				enable Diable icon on select Change
    =======================================================
	*/
	$(document).on('change','.template_selected', function() {
		selected_templates(this,'.save');
	});
	$(document).ready(function(){
		$('.template_selected').each(function(){
			var select = $(this).index();
			console.log(select);
			if($(this)[0].selectedIndex > 0)
			{	
				selected_templates(this,'.edit');		
			}
		})
	})
	
	
	/*
	=======================================================
				function to allow options
    =======================================================
	*/
	function selected_templates(selected, target){
		var this_id = $(selected).attr('id');
		var row_split = this_id.split("_"); 
		var row_id = "#"+row_split[1];
		$(row_id +' .tab_operations'+target).addClass('active_icon'); 
	}
	/*
	=======================================================
				function to get  row id
    =======================================================
	*/
	 
 	function tableRow_id(id){
		
		var row_split = id.split("_"); 
		var row_id = row_split[1];
		return(row_id);
	}
	
	/*
	=======================================================
				function to unset input value
    =======================================================
	*/
	 
	function clear_input(target)
	{
		$(target).each(function(){
			$(this).attr("value", "");
		})
	}
	
	
	/*
	=======================================================
				function to display updated result
		     Update table row based on ajax response
    =======================================================
	*/
	function resultDisplay(json,select,id,member_no,member_id,member_name){
		if(json.response=="updated")
		{		 
				$(select).prop('disabled',true);
				$("#save_"+id).removeClass('active_icon');
				$("#edit_"+id).addClass('active_icon'); 
				
				var template_id  = $(select).val();
				var type = $('#edit_'+id).attr('data-submit-type');
				var property_id = ''; 
				if(type=="property_set"){
					$("#edit_"+id).attr('data-property-id',id);
					property_id  = id;
				}
				else{
					$("#edit_"+id).attr('data-property-id',-1);
					property_id  = -1;
				}
				// target div for accordion 
				var target_accordion_id = "#collapse_"+id;
				swal({
					  title: "Success",
					  text: "Template Successfuly assigned, please setup the Defaults for the newly assigned member",
					  type: "success",
					  confirmButtonColor: "#DD6B55",
					  confirmButtonText: "OK",
					  closeOnConfirm: false
				});
				
				/* show the default setup Container */
				$('#default_edit input').val("");
				
				document.getElementById('display_val').value = id; 
				document.getElementById('sel_selectedTemp_name').value = template_id; 
				document.getElementById('sel_memberid').value = member_id; 
				document.getElementById('sel_memberno').value = member_no; 
				document.getElementById('sel_membername').value = member_name; 
				
				document.getElementById('tar_memberid').value = member_id; 
				document.getElementById('tar_memberno').value = member_no; 
				document.getElementById('tar_tempid').value = template_id; 
				document.getElementById('tar_propertyid').value = property_id; 
				
				var target_form = "#default_edit";
				var form = $(target_form);
				validate_DefaultInputs(target_accordion_id);
				unmaskCurrency('input[data-type=Dollars');
				var data_value = form.serialize();
				var url = "ajax-default-setup.php";
				
				show_accordion(url,data_value,target_accordion_id);	
				 
				
		}
		else if(json.response=="updateError")
		{
			swal({
				  title: "Error",
				  text: "Chages Not Updated, Please refresh the page & try again!",
				  type: "error",
				  confirmButtonColor: "#DD6B55",
				  confirmButtonText: "OK",
				  closeOnConfirm: false
			});
			
			 
		}
		else if(json.response=="exists"){
			swal({
				  title: "Error",
				  text: "Selected Member is alreay assigned a template. Please delete the previously a assigned template!",
				  type: "error",
				  confirmButtonColor: "#DD6B55",
				  confirmButtonText: "OK",
				  closeOnConfirm: false
			});
		}
		else{
			swal("Error", "unknown error.", "error");
		}
		
	}
	
	
	
	/*
	=======================================================
				function to  replace the content 
    =======================================================
	*/
	 
	
	function show_accordion(url,data_value,target_accordion_id)
	{	 
		jQuery.ajax(
		{	type: "POST",
			url : url,
			dataType:"html",
			data:data_value,
			beforeSend: function()
			{
				$(target_accordion_id).collapse('show');
				
			},
			success:function(data)
			{	$(target_accordion_id).html(''); 
				$(target_accordion_id).html(data);
				$(target_accordion_id).parent().find('.pre_loader_block').fadeOut(); 
				datepicker(target_accordion_id);
				//validate_inputs(target_accordion_id);
				validate_DefaultInputs(target_accordion_id);
				maskCurrency('input[data-type=Dollars');				
				onkeyPress();
			}
		})
	}
	
	/*
	=======================================================
				Save and Apply the selected template 
    =======================================================
	*/
	$(document).on('click','.disabled_link.tab_operations.save.active_icon', function() {
		
		/* Details Collected 
			id 							................... Row ID
			member_no					................... Member Number for the selected Row
			member_id                   ................... Member ID for the selected Row
			member_name                 ................... Member Name for the selected Row
			select                      ................... Template ID(Drop Down ID for the selcted  Row)
			slected_template            ................... Selected Templated ID (Drop Down Value)
			target_form                 ................... Form that need will be posted through Ajax
			property_name				................... Property Name If any exist
		*/
		 
		var id = $(this).attr('data-target-id');
		var member_no=$(this).attr('data-member-no');
		var member_id=$(this).attr('data-member-id');
		var member_name = $(this).attr('data-member-name');
		var property_id = $(this).attr('data-property-id');
		 
		clear_input(".row_id");
		var select =  "#template_"+id;
		var slected_template = $(select).val();
		
		$('#form_save input').val("");
		var target_form = "#form_save";
		
		/* 
			Setting the hidden form Values 
			Form Id : form_save
		*/		 
		document.getElementById('row_changed').value = id; 
		document.getElementById('selectedTemp_name').value = slected_template; 
		document.getElementById('memberid').value = member_id; 
		document.getElementById('memberno').value = member_no; 
		document.getElementById('membername').value = member_name; 
		document.getElementById('selectedProp_id').value = property_id; 
		
		var form = $(target_form);
		var formdata = form.serialize();
		ajax_update(formdata,select,id,member_no,member_id,member_name);
		
		
		
	})
	
	/* ajax Call to save the data of a Row */
	 
	function ajax_update(formdata,select,id,member_no,member_id,member_name) 
	{
	  jQuery.ajax(
		{	type: "POST",
			url : "ajax-default-setup.php",
			dataType:"text",
			data:formdata,
			success:function(data){
			 var json = $.parseJSON(data);
			 resultDisplay(json,select,id,member_no,member_id,member_name);
			},
			error: function(jqXHR, textStatus, errorThrown) 
			{
				swal("Error", "unknown error.", "error");   
			}
		})
	} 
	  	
	
	
	  	
/*
	=======================================================
				function to Display Data
    =======================================================
	*/	
	
$(document).on('click','.disabled_link.tab_operations.edit.active_icon', function()
{
	/* Details Collected 
		id 							................... Row ID
		member_no					................... Member Number for the selected Row
		member_id                   ................... Member ID for the selected Row
		member_name                 ................... Member Name for the selected Row
		select                      ................... Template ID(Drop Down ID for the selcted  Row)
		template_id                 ................... Selected Templated ID (Drop Down Value)
		target_form                 ................... Form that need will be posted through Ajax
		target_accordion_id         ................... Accordion to display
		data_value                  ................... Form Data
	*/
	
	
	var id = $(this).attr('data-target-id');
	var member_no=$(this).attr('data-member-no');
	var member_id=$(this).attr('data-member-id');
	var member_name = $(this).attr('data-member-name');
	var property_id = $(this).attr('data-property-id');
	
	
 	clear_input(".row_id");
	var select =  "#template_"+id;
	var target_form = "#default_edit";
	var template_id  = $(select).val();
	var target_accordion_id = "#collapse_"+id;
	//var data_value = 'display_val='+ template_id ; 
	
	/* 
		Setting the hidden form Values 
		Form Id : default_edit
	*/
	$('#default_edit input').val("");
	validate_DefaultInputs(target_accordion_id);	 
	
	document.getElementById('display_val').value = id; 
	document.getElementById('sel_selectedTemp_name').value = template_id; 
	document.getElementById('sel_memberid').value = member_id; 
	document.getElementById('sel_memberno').value = member_no; 
	document.getElementById('sel_membername').value = member_name; 
	document.getElementById('sel_Temp_name').value = $(select+' :selected').text();
	document.getElementById('tar_propertyid').value = property_id; 
	document.getElementById('sel_Propertyid').value = property_id; 
	
	document.getElementById('tar_memberid').value = member_id; 
	document.getElementById('tar_memberno').value = member_no; 
	document.getElementById('tar_tempid').value = template_id; 
	
	
	
	/*  Check If Div is Previously Loaded Or not */
	if ($(target_accordion_id).children().length == 0){
		$(target_accordion_id).parent().find('.pre_loader_block').fadeIn(); 
		unmaskCurrency('input[data-type=Dollars');
		
		var form = $(target_form);
		var data_value = form.serialize();
		var url = "ajax-default-setup.php";
		
		show_accordion(url,data_value,target_accordion_id);	
	}	
	// else Do nothing
	else
	{
	   // already loaded
	}
		
})
 
 
	/*
	=======================================================
		function to update form data(Saving Defaults)
    =======================================================
	*/
$(document).on('click','.save_defaults', function()
{  
	resultset = 1;
	/* Details Collected 						
		id 							................... Row ID
		member_no					................... Member Number for the selected Row
		member_id                   ................... Member ID for the selected Row
		member_name                 ................... Member Name for the selected Row
		select                      ................... Template ID(Drop Down ID for the selcted  Row)
		template_id                 ................... Selected Templated ID (Drop Down Value)
		target_form                 ................... Form that need will be posted through Ajax
		target_accordion_id         ................... Accordion to display
		data_value                  ................... Form Data
	*/
	var seleted_button = $(this).closest('tr');
	var id = seleted_button.prev().attr('id');
	var target_accordion_id ="#collapse_"+id;
	 
				
	//console.log(res);		
			
	var target_form = $(this).attr('data-target-form'); 
	var member_no=$(this).attr('data-member-no');
	var member_id=$(this).attr('data-member-id');
	var member_name = $(this).attr('data-member-name');
	var template_id =$(this).attr('data-tem-id'); 
	var property_id = $(this).attr('data-property-id');
	 
	var button  = $(this);
	clear_input(".form_submit_check");
	 
	 
	$('#'+target_form+' #target_memberid').val(member_id); 
	$('#'+target_form+' #target_memberno').val(member_no); 
	$('#'+target_form+' #target_membername').val(member_name); 
	$('#'+target_form+' .form_submit_check').val(template_id); 
	$('#'+target_form+' #target_propertyId').val(property_id); 
	var form = $('#'+target_form);
	
	var url = "ajax-default-setup.php";
	
	validate_DefaultInputs(target_accordion_id);	
	unmaskCurrency('input[data-type=Dollars');
	
	var validate= true;
	/* $(target_accordion_id+' input[type="text"]').each(function(){
		 if($(this).val() != '' || $(this).attr('checked'))
		{
			
		}
		else{
			
			$(this).addClass('ErrorField');
			$(this).parents('.panel-collapse.collapse').addClass('in');
			$(this).parents('.panel-collapse.collapse').css('height','auto');
			$(this).parents('.panel-collapse').prev('.panel-heading').find('.active_menu').removeClass('collapsed');
			validate= false;
			
		} 
		
		
	}); */
	if(!resultset){
		
		
		return false;
	}
	else { 
	 var data_value = form.serialize();
	 ajax_insert(data_value,url,template_id,member_no,member_id,member_name,button); 
	}
		
	
}) 

/* ajax Call to save the  Defaults Data */
	 
	function ajax_insert(data_value,url,template_id,member_no,member_id,member_name,button) 
	{
	  jQuery.ajax(
		{	type: "POST",
			url :url,
			dataType:"text",
			data:data_value,
			beforeSend: function()
			{	 
				var seleted_button = $(button).closest('tr');
				var id = seleted_button.prev().attr('id');
				var h = $(button).closest('tr').height();
				$(button).closest('tr').find('#collapse_'+id).css('height',h);
				$(button).closest('tr').find('.pre_loader_block').css('z-index','20');
				$(button).closest('tr').find('.pre_loader_block').css('height','100%');
			},
			success:function(data){
			    //$(button).parent().find('.pre_loader_block').fadeOut(); 
				var seleted_button = $(button).closest('tr');
				var id = seleted_button.prev().attr('id');
				$(button).closest('tr').find('#collapse_'+id).html('');
				$("#edit_"+id).click(); 
			   $("#collapse_"+id).css('height','auto');	 
			    //datepicker(form);
				 
			},
			error: function(jqXHR, textStatus, errorThrown) 
			{
				swal("Error", "unknown error.", "error");   
			}
		})
	} 

	
/*
	=======================================================
		Delete selected template for a member only
    =======================================================
	*/
	$(document).on('click', '.delete_assigned_template', function(e)
	{	
		var button  = $(this);
		var id = $(this).attr('data-target-form');
		var member_no=$(this).attr('data-member-no');
		var member_id=$(this).attr('data-member-id');
		var member_name = $(this).attr('data-member-name');
		var template_id =$(this).attr('data-tem-id'); 
		var property_id = $(this).attr('data-property-id'); 
		$('#display_val').val('') 
		document.getElementById('delete_val').value =template_id; 
		console.log($('#delete_val').val());
		document.getElementById('sel_selectedTemp_name').value = template_id; 
		document.getElementById('sel_memberid').value = member_id; 
		document.getElementById('sel_memberno').value = member_no; 
		document.getElementById('sel_membername').value = member_name; 
		document.getElementById('sel_Propertyid').value = property_id; 
		
		 
		var target_form = $('#'+id);
		console.log(target_form);
		var form = $(target_form);
		var data_value = form.serialize();
		var url = "ajax-default-setup.php";
				 
				
	
		swal({
		   title: 'Are you sure?',
		   text: "You won't be able to revert this!",
		   type: 'warning',
		  showCancelButton: true,
		  confirmButtonColor: "#DD6B55",
		  confirmButtonText: "Yes, Delete!",
		  closeOnConfirm: false,
		  showLoaderOnConfirm: true,
		  
		},
		function(){
				//e.preventDefault(); //STOP default action		
				
				jQuery.ajax(
				{	type: "POST",
					url : url,
					dataType:"text",
					data:data_value,
					success:function(data){
						var result = data.trim();
						 
						if(result == "delete_success")
						{console.log(drop_down);
							
							/* Get the previous table row ID */
							var seleted_button = $(button).closest('tr');
							var id = seleted_button.prev().attr('id');
							
							/* Accoordion Data deletion */
							$(button).closest('tr').find('#collapse_'+id).html('');
							
							/* Template Drop down Reset */
							var drop_down = '#template_'+id;
							alert(drop_down);
							$(drop_down+' option').attr('selected', false);	
							$(drop_down).removeAttr('disabled');	
							$( drop_down+' option:first-child').attr('selected', true);
							
							/* Disable edit Button */
							$('#edit_'+id).removeClass('active_icon'); 
							 
							/* Success alert */
							swal("SUCCESS","Template Deleted", "success");
							
						}
						else if(result == "delete_error")
						{
							swal("ERROR","Template Not Deleted", "error");	
						}						
						
						 
					},
					error: function(jqXHR, textStatus, errorThrown) 
					{
						swal("Error", "unknown error.", "error");   
						 
					}
				})
				 
			  
		});	
		return true;
	
	});
	
		
/* Copy Defaults */


$(document).on('click','.full_copy_span', function(e){
	var target_form = $(this).attr('data-target-form'); 
	var member_no=$(this).attr('data-member-no');
	var member_id=$(this).attr('data-member-id');
	var member_name = $(this).attr('data-member-name');
	var template_id =$(this).attr('data-tem-id'); 
	var target_model = $(this).attr('data-target');
	var current_property_id = $(this).attr('data-property-id');
	console.log(current_property_id);
	var button  = $(this);
	var seleted_button = $(button).closest('tr');
	var id = seleted_button.prev().attr('id');
	clear_input("#default_copy_values");
	 
	$('#'+target_form+' #default_copy_set').val('SET'); 
	$('#'+target_form+' #copy_memberid').val(member_id); 
	$('#'+target_form+' #copy_memberno').val(member_no); 
	$('#'+target_form+' #copy_membername').val(member_name); 
	$('#'+target_form+' #copy_Temp_id').val(template_id); 
	$('#'+target_form+' #copy_property_id').val(current_property_id); 
	
	$('#'+target_form+' #current_row_id').val(id); 
		
	var form = $('#'+target_form);
	var data_value = form.serialize();
	var url = "ajax-default-setup.php";
	
	 jQuery.ajax
	 (
		{	type: "POST",
			url :url,
			dataType:"text",
			data:data_value,
			beforeSend: function()
			{
				$(target_model+ ' .pre_loader_block').css('height','100%').fadeIn();
				
			},
			success:function(data){
			   $('#default_copy_content').html(data);
			   $(target_model+ ' .pre_loader_block').fadeOut().css('height','auto');
			
			/* RELOADING THE ACCORION WITH THE DEFAULT DATA OF THE SELECTED TEMPLATE */
			  $('#'+target_form+' #default_copy_set').val(''); 
			/* RELOADING THE ACCORION WITH THE DEFAULT DATA OF THE SELECTED TEMPLATE */
				datepicker(target_model);
			},
			error: function(jqXHR, textStatus, errorThrown) 
			{
				swal("Error", "unknown error.", "error");   
			}
		}
	)
	


})
	
	
/*
	=======================================================
	  function to Setup the Calculations  
    =======================================================
	*/	
	$(document).on('click','.calculation_setup', function()
	{
		var financials =  $('#FINANCIALS input.select_check:checkbox:checked').length
		if(financials>0)
		{
			var tempId = $('#select_template option:selected').val();
			var tempname = $('#select_template option:selected').text();
			var currenttab  = $('#pnProductNavContents').find("a[aria-selected='true']").attr('href');
			$('#cal_templateName').val(tempname);
			$('#cal_templateId').val(tempId);
			$('#cal_currentTab').val(currenttab);
			$('#calculation_setup').trigger('submit');
		}
		 	
	})	
	
	$(document).on('click','#FINANCIALS .select_check', function() { 
		calculation_setup()
	});
	
	 
	
	/* function to disable/enable setup calculation button */
	
	
	
	function calculation_setup()
	{
		var financials =  $('#FINANCIALS input.select_check:checkbox:checked').length;
		if(financials>0)
		{
			$('.calculation_setup').removeClass('inactive');
			
		}
		else
		{
			$('.calculation_setup').addClass('inactive');
		}	
		
	}

/*
	=======================================================
	  function to COPY DEFAULTS FROM THE SELECTED MEMBER
    =======================================================
	*/	
	
$(document).on('click','#default_copy_content #model-select-template-button', function()
{	 
	var select_id = '#copy_defaults_from :selected';
	var member_no=$(select_id).attr('data-member-no');
	var member_id=$(select_id).attr('data-member-id');
	var template_id = $(select_id).attr('data-member-temp');
	var currrent_row_id = $(select_id).attr('data-row-id');
	var id = $(select_id).attr('data-row-id'); 
	var selected_property_id  = $(select_id).attr('data-property-id');
	var target_accordion_id = "#collapse_"+id; 
	 
	clear_input(".row_id");
	
	var current_member_id = $(this).attr('data-current-member-id');
    var current_member_no = $(this).attr('data-current-member-no');
	var current_temp_id = $(this).attr('data-current-member-temp');
	var current_property_id = $(this).attr('data-current-property');
	var target_membername =	$(this).attr('data-member-name');
	
 
	var target_form = "#default_edit";
	/* 
		Setting the hidden form Values 
		Form Id : default_edit
	*/
	/* unset all the fileds */
	$('#default_copy_values input').val("");
	
	document.getElementById('display_val').value = id; 
	document.getElementById('sel_selectedTemp_name').value = template_id; 
	document.getElementById('sel_memberid').value = member_id; 
	document.getElementById('sel_memberno').value = member_no; 
	document.getElementById('sel_Propertyid').value = selected_property_id; 
	
    document.getElementById('tar_memberid').value = current_member_id; 
	document.getElementById('tar_memberno').value = current_member_no; 
	document.getElementById('tar_tempid').value = current_temp_id; 
	document.getElementById('tar_propertyid').value = current_property_id; 
	
	document.getElementById('sel_membername').value = target_membername; 
	
	
	//document.getElementById('sel_membername').value = member_name; 
	//document.getElementById('sel_Temp_name').value = $(select+' :selected').text();
	
	var form = $(target_form);
	var data_value = form.serialize();
	var url = "ajax-default-setup.php";
	
	/*  Check If Div is Previously Loaded Or not */
	 jQuery.ajax(
		{	type: "POST",
			url : url,
			dataType:"html",
			data:data_value,
			beforeSend: function()
			{	$(target_accordion_id).closest('tr').find('.pre_loader_block').css('height','100%');
				$(target_accordion_id).closest('tr').find('.pre_loader_block').css('z-index','10');
				$(target_accordion_id).closest('tr').find('.pre_loader_block').fadeIn();
			},
			success:function(data)
			
			{	
				$('#default_copy').removeClass('in');
				$('.modal-backdrop.fade.in').remove();
				$(target_accordion_id).html(''); 
				$(target_accordion_id).html(data);
				$(target_accordion_id).closest('tr').find('.pre_loader_block').fadeOut();
				$(target_accordion_id).closest('tr').find('.pre_loader_block').css('height','auto'); 
				datepicker(target_accordion_id);
				
				 
			}
		})
		
})
 
/*
	=======================================================
		Date Picker
    =======================================================
*/
 function datepicker(target_accordion_id){
        var date_input=$(target_accordion_id+' input[data-type="Date"]'); //our date input has the name "date"
        
        date_input.datepicker({
            format: 'mm/dd/yyyy',
			todayHighlight: true,
             
        })
    }

	
/*
	=======================================================
		Function TO Validate Data
    =======================================================
*/	
 function validate_inputs(target_accordion_id){
	 
	$(target_accordion_id+" input[type=text]").each(function() 
	{
            var id = "#"+$(this).attr("id");
			var type = $(this).attr("data-type")
			 
		   jQuery(function()
		   {
			   
			   if(type=="Text"){
                jQuery(id).validate({
					  
                    expression: "if (VAL) return true; else return false;",
                    message: "Value Required ",
					 
					
                }); 
			   }
			  else if(type=="Number")
			  {		 
					jQuery(id).validate({
						 
						expression: "if (VAL.match(/^[0-9]*$/) && VAL) return true; else return false;",
						message: "Please enter a valid integer"
					});
			  }	
			  else if(type=="Dollars")
			  {
				 jQuery(id).validate({
						expression: "if (!isNaN(VAL) && VAL) return true; else return false;",
						message: "Please enter a valid Amount"
					}); 
			  }
			else if(type=="Date")
			{
				jQuery(id).validate(
				{	 
					expression: "if (!isValidDate(parseInt(VAL.split('/')[2]), parseInt(VAL.split('/')[0]), parseInt(VAL.split('/')[1]))) return false; else return true;",
					message: "Please enter a valid Date"
                });
			}
			else if(type=="Email")
			{
				jQuery(id).validate(
				{	 
					expression: "if (VAL.match(/^[^\\W][a-zA-Z0-9\\_\\-\\.]+([a-zA-Z0-9\\_\\-\\.]+)*\\@[a-zA-Z0-9_]+(\\.[a-zA-Z0-9_]+)*\\.[a-zA-Z]{2,4}$/)) return true; else return false;",
                    message: "Please enter a valid Email ID"
                });
			}
					
					
			  
		   })		
        });
  
 }
 /*
=======================================================
				Property Level Assignment
=======================================================
*/
/* Author :Pradeep 
 * Date : 17 May 2017 
 * Used in: package-setup.php
 * Description :  
 * This function is used to show documents
   and members on change  package for selected member*/
	
	$(document).on('change','#prop_select_member_block',function () 
	{ 
	    var text1=$("#prop_selected_member option:selected").text();
	    
		var mastermemberno = $("#prop_selected_member option:selected").attr('data-mastermemberid');
		var mastermemberid = $("#prop_selected_member option:selected").attr('data-mastermemberno');
		var memberid = $("#prop_selected_member option:selected").attr('data-memberid');
		var memberno = $("#prop_selected_member option:selected").attr('data-memberno');
		var feedid =  $("#prop_selected_member option:selected").attr('data-feed');
		//pckmasteid
		$('.property-template-sec').fadeIn();
		//var text1 = text1.trim();
		//var packagename = 'option_value='+ packageId;
				
				$.ajax(
						{	type: "POST",
							url : "ajax-default-setup.php",
							data:{
								$mastermemberid:mastermemberno,
								$mastermemberno:mastermemberno,
								memberno:memberno,
								memberid:memberid,
								feedid:feedid
							},
							 beforeSend: function(){
										/* $('.pre_loader_block').fadeIn();
                                       $("#documentlisttable").html("");    */
                                    },
                            success: function(data)
                                {
									/* var packageId=$("#select_package option:selected").attr('data-Id');
									$('#documentlisttable').html(data);
									$($.fn.dataTable.tables(true)).DataTable().columns.adjust();
									 
									document.getElementById("packageid").value=packageId;
									document.getElementById("packageid1").value=packageId;
									$('.pre_loader_block').fadeOut();
                                    $('.package-sec').fadeIn(); */
                                }
					
                        });
		
	});


	
	
/* ===========================START OF PRADEEP FUNCTIONS========================== */
							 /* Package Creation Page */
/*
=======================================================
				New package Name 
=======================================================
*/
/* Author :Pradeep 
 * Date : 17 May 2017 
 * Used in: package-setup.php
 * Description :  
 * This function is used to show documents
   and members on change  package for selected member*/
	
	$(document).on('change','#select_package',function () 
	{ 
	
		var defSelect = $("#select_package option")[0];
	    $(defSelect).prop("selected", false);
		
	    var text1=$("#select_package option:selected").text();
	    var text2=$("#select_package option:selected").val();
		var packageId=$("#select_package option:selected").attr('data-Id');
		//pckmasteid
		if(text2=="M"){text2="Move Out Package";}
		else if(text2=="L"){text2="Lease Package";}
		else if(text2=="R"){text2="Renewal Package";}
		else if(text2=="S"){text2="Stand Alone Package";}
		$("#tt").html(text2);
		$("#pn").html(text1);
		$('.package-sec').fadeIn();
		var text1 = text1.trim();
				var packagename = 'option_value='+ packageId;
				
				$.ajax(
						{	type: "POST",
							url : "ajax-package-setup.php",
							data:packagename,
							 beforeSend: function(){
									   $('.pre_loader_block').fadeIn();
                                       $("#documentlisttable").html("");   
                                    },
                            success: function(data)
                                {
									$('.pre_loader_block').fadeOut();
									//var packageId=$("#select_package option:selected").attr('data-Id');
									
									$('#documentlisttable').html(data);
									$($.fn.dataTable.tables(true)).DataTable().columns.adjust();
									document.getElementById("packageid").value=packageId;
									document.getElementById("packageid1").value=packageId;
									
                                    $('.package-sec').fadeIn("slow");
                                }
					
                        });
		
	});
	
/* Author :Pradeep 
 * Date : 17 May 2017 
 * Used in: package-setup.php
 * Description :  
 * This function is to create new 
	package to selected member*/
	
		$(document).on('click', '.confirm1', function(e)
		{
			var packagename=$('#packagename').val();
			var packagename=packagename.trim();
			var packagetype=$("#select_template option:selected").text();
			 if(packagename=='')
			{
				$('.err-msg').show();
				return false;
			}else if(packagetype=='Select Package Type')
			{
				$('.err-msg').hide();
				$('.err-msg2').show();
				return false;
			}
			else
			{ 	
			    
			     $('.err-msg2').hide();
				 $('.err-msg').hide();
				document.getElementById("insertpackage").value="insertpackage";
				var form=$('#packagemodal').serialize();
				 jQuery.ajax(
				{	type: "POST",
					url : "ajax-package-setup.php",
					data:form,
					beforeSend: function(){
					},
					success:function(data){
						
						$('#select_name_list').load(document.URL + ' #select_name_list');
						//$('#packagemodal').load(document.URL + ' #packagemodal');
						 $( ".cancel1" ).click();
						var result = data.trim();
						
						if(result=='created')
						{	document.getElementById("insertpackage").value="";
					
							 swal("Success!", "Package has been created successfully, Please select the created package from drop down for document selection and property assignment.", "success");
							 $('#packagemodal')[0].reset();
							 $('.package-sec').fadeOut();
							 $('#documentlisttable').html('');
						}
						else if(result=='not created')
						{
							swal("Sorry", "Couldnot create a package, Package name is alreday exist!", "error");
						}
						else 
						{
							swal("Sorry", "Unknown Error Occured, try again", "error");
						}
							
					},
					error: function(jqXHR, textStatus, errorThrown) 
					{
						//if fails   
					 
					}
				});
			}
			
		});

/* Author :Pradeep 
 * Date : 18 May 2017 
 * Used in: package-setup.php
 * Description :  
 * This function is to select and required
   document to selected member*/
	
	$(document).on('click', '.savedocs', function(e)
		{
			var total=$('#packahedoc_table').find('input[type="checkbox"]:checked').length;
			var text1=$("#select_package option:selected").text();
			var savedoc='';
				 if(total>0){
				  savedoc="active";
				 }
			
				if(savedoc!='')
				{
					var form=$('#doctable').serialize();
					jQuery.ajax(
						{	type: "POST",
							url : "ajax-package-setup.php",
							data:form,
							beforeSend: function(){
							},
							success:function(data){
								swal("Success!", "Selected documents are assigned to "+text1+" package successfully", "success");											
							},
							error: function(jqXHR, textStatus, errorThrown) 
							{
								//if fails   
							 
							}
						});
				}else
				{
					swal("Sorry,", "You have not selected any documents!", "error");
				}
		});
	
	
			
/* Author :Pradeep 
 * Date : 18 May 2017 
 * Used in: package-setup.php
 * Description :  
 * This function is to assign package
   to members of selected mastermember*/
	
	$(document).on('click', '.savememberpacakge', function(e)
		{
			 var text1=$("#select_package option:selected").text();
			document.getElementById('packassign').value="packageassigne";
			var form=$('#memberpackageassign').serialize();
			jQuery.ajax(
				{	type: "POST",
					url : "ajax-package-setup.php",
					data:form,
					beforeSend: function(){
					},
					success:function(data){
						swal("Success!", text1+" package is assigned to selected members successfully", "success");											
					},
					error: function(jqXHR, textStatus, errorThrown) 
					{
						//if fails   	 
					}
				});
		});
		
		
		
/* Author :Pradeep 
 * Date : 19 May 2017 
 * Used in: package-setup.php
 * Description :  
 * This function is to delete package from  package table and packageassign table 
   of selected mastermember*/	
		/* Deleteing a  package */
$(document).on('click', '.delete_package', function(e)
	{
		var text1=$("#select_package option:selected").text();
		var packageId=$("#select_package option:selected").attr('data-Id');
		var existcheck=$('#packageexist').val();
		//alert(packageId);
		if(existcheck=="not_exist"){var message="You won't be able to revert this!";}
		else{var message="This package is assigned to some members! still wants to delete?";}
		var text1 = text1.trim();
				var packagename = 'package_value='+ packageId;
				swal({
				   title: 'Are you sure?',
				   text: message ,
				   type: 'warning',
				  showCancelButton: true,
				  confirmButtonColor: "#DD6B55",
				  confirmButtonText: "Yes, Delete!",
				  closeOnConfirm: false,
				  showLoaderOnConfirm: false,
				},
		function(){
					
				$.ajax(
						{	
							type: "POST",
							url : "ajax-package-setup.php",
							data:packagename,
							beforeSend: function()
							 {
								$('.pre_loader_block').fadeIn();
                             },
                            success: function(data)
                             {
								 $('.pre_loader_block').fadeOut();	
								 $('#select_package option:selected').remove();
								 $("#select_package").val("00").change(function fn(){
								 });
								 //$('.package-sec').fadeOut();
								  $('#packagemodal')[0].reset();
							 $('.package-sec').fadeOut();
							 $('#documentlisttable').html('');
								swal("SUCCESS","Package  Deleted", "success");
                             }
					
                        });
		 
				})
				e.preventDefault(); //STOP default action  
			  
		 
	})	
	
	/* ===========================END OF PRADEEP FUNCTIONS========================== */	
	
/*
=======================================================
				Electronic Siganture Setup 
				Author - Sarfaraz
=======================================================
*/
$(document).on('change','#select_member_name',function () 
	{ 	
		var selID = $("#select_member_name option:selected");
		var memberId = $(selID).attr('data-memberid');
	    var memberNo = $(selID).attr('data-memberno');
	    // var esignid = document.getElementById('select_member_name').value;
		 // console.log(document.getElementById('select_member_name'));
		 var memberSelect = selID.val();
		jQuery.ajax(
		{	type: "POST",
			url : "ajax-esign.php",
			dataType:"text",
			data:{
				memberId:memberId,
				memberNo:memberNo,
				memberSelectChange:memberSelect
			},
			beforeSend:function(){
				$('.esignature-sec').fadeIn();
				$('#esign_content').html('');
				$('.pre_loader_block').css({'position':'absolute','top':'0px', 'bottom':'0px','z-index':'99','left':'0','right':'0', 'min-height':'160px', 'padding-top':'80px','height':'100%','border-radius':'5px'})
				$('.pre_loader_block').fadeIn();
				
				
			},
			success:function(data){
				$('#esign_content').html(data);
				$('.esignature-sec').fadeIn(); 
				$('.pre_loader_block').fadeOut();			
				validate_values('#frm-esign');				
			},
			error: function(jqXHR, textStatus, errorThrown) 
			{
				//if fails   
			 
			}
		});  
		  
		  
		
	});
	

/* $(document).on('click','#applyMemberChanges', function()
{
		var memberfrm = $('form#memberForm,form#frm-esign');
		var applyChanges = 'applyChanges';
		var select_id = '#select_member_name :selected';
		var member_no=$(select_id).attr('data-memberno');
		var member_id=$(select_id).attr('data-memberid');
		var esignLimit=$('#esignLimit').val();
		var reminderFreq=$('#reminderFreq').val();
		var emailMsg=$('#emailMsg').val();
		var autoCounter=$('#autoCounter').val();
		var counterName=$('#counterName').val();
		var memberSelect = $('#select_member_name').val();
		document.getElementById('emember_id').value = member_id;
		document.getElementById('emember_no').value = member_no;
		document.getElementById('set_input').value = memberSelect;
		var memberdata = memberfrm.serialize()+'&applyChanges='+applyChanges;
		console.log(memberdata);
		jQuery.ajax(
		{	type: "POST",
					url : "ajax-esign.php",
					data:memberdata,
					success:function(response)
					{	 
						 
					},
					error: function(jqXHR, textStatus, errorThrown) 
					{
						 
					swal("Error", "unknown error occured, Please try again", "error");	 
						   
					}
		});
});
  */ 
 

/* diable selected member option */ 
function checkCurrentMember(e){
	 return false;
};
/* Reset all the checked fields  */
function modelResetMemberChecked(member_id,member_no){
	selectedMember = '#'+member_id+member_no;
	$('#membersModel input:checked').prop('checked', false);
	$(selectedMember).prop('checked', true);
};	

$(document).on('click','.save-esign,#applyMemberChanges,#saveApplyall', function()
{
		 
		var form = $('#frm-esign');
		var save = 'save';
		var select_id = '#select_member_name :selected';
		var member_no=$(select_id).attr('data-memberno');
		var member_id=$(select_id).attr('data-memberid');
		document.getElementById('set_input').value = 'formsubmit'; 
		document.getElementById('emember_id').value = member_id;
		document.getElementById('emember_no').value = member_no;
		var memberSelect = $('#select_member_name').val();
		var formdata = form.serialize()+'&memberSelect='+memberSelect+'&save='+save;
		
		validate_values('#frm-esign');
		resultset = 1; 
		var checkStatus = 0;
		var esignLimit = $('#esignLimit option:selected').val();
		var reminderFreq = $('#reminderFreq option:selected').val();
		if ($('#autoCounter').is(':checked'))
		{
			var counterName = $('#counterName').val();
			if(counterName!='')
			{
				// Dont do any thing 
			}
			else{
				checkStatus =1;
			}
		
			
		}

		if(esignLimit=='0' || reminderFreq =='0' || checkStatus == 1 ){
			if(esignLimit=='0')
			{
				$('#esignLimit').addClass('ErrorField');
			}
			if(reminderFreq =='0')
			{
				$('#reminderFreq').addClass('ErrorField');
			}
			if(checkStatus == 1)
			{
				$('#counterName').addClass('ErrorField');
				 
			}
			else
			{
				$('#counterName').removeClass('ErrorField');
				$('#counterName').val('');
				 
			}
			
		}
		else
		{
			$('#counterName').removeClass('ErrorField');
			var resi = $('.esignsetup').trigger('submit');
			
			if(resultset)
			{    
				/* Reset the model checkboxes  */
				modelResetMemberChecked(member_id,member_no);
				if($(this).attr('id')=='saveApplyall')
				{
					$('#membersModel').modal();
					
				}
				else
				{
					jQuery.ajax(
					{	type: "POST",
						url : "ajax-esign.php",
						//dataType:"text",
						data:formdata,
						beforeSend:function(){
							$('#membersModel').modal('hide');
							$('#esign-pre').css('position','fixed');
							$('#esign-pre').fadeIn();
						},
						success:function(response)
						{	 
							modelResetMemberChecked(member_id,member_no);
							response = response.trim();	
							if(response=='signatureUpdated')
							{
								$('#esign-pre').css('display','none'); 
								$('#esign-pre').css('position','absolute');
								swal("Done!", "It was succesfully updated!", "success");
								validate_values('#frm-esign');		
							}
							else if(response=='unknownError')
							{
								$('#esign-pre').css('display','none'); 
								swal("Error", "Error occured, Please try again", "error");	
								validate_values('#frm-esign');		
								
							}
							else
							{
								$('#esign-pre').css('display','none'); 
								swal("Error", "Unknown Error occured, Please check the inserted values and  try again", "error");		
								validate_values('#frm-esign');		
							}
						},
						error: function(jqXHR, textStatus, errorThrown) 
						{
							$('#esign-pre').css('display','none'); 
							swal("Error", "unknown error occured, Please try again", "error");	 
							
							   
						}
					}); 
				}	
			}
			else
			{
				
			}
		}		
});	

				 
 
	
/* =======================================================
		Delete Electronic Template
    =======================================================
	*/
	$(document).on('click', '.delete_electronic_signature', function(e)
	{
		var selID = $("#select_member_name option:selected");
		 
		var memberno = $(selID).attr('data-memberno');
		var esignid = $(selID).val();
		console.log(esignid);
		var memberid = $(selID).attr('data-memberno');
		var del = 'delete';
		swal({
        title: "Are you sure?",
        text: "You will not be able to recover this later!",
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: "#DD6B55",
        confirmButtonText: "Yes, delete it!",
        closeOnConfirm: false
    }, function (isConfirm) {
        if (!isConfirm) return;
        $.ajax({
            url: "ajax-esign.php",
            type: "POST",
            data: {
                memberno: memberno,
				esignid:esignid,
				memberid:memberid,
				del:del
            },
            success: function (response) {
				response = response.trim();
				if(response == 'sucessdelete'){
					var drop_down = '#select_member_name';
					$(drop_down+' option:selected').removeAttr('selected');	
					$( drop_down+' option:nth-child(1)').prop('selected', true);
					$('#esign_content').html('');
					swal("Done!", "It was succesfully deleted!", "success");
					
				}
				else if(response=='errordelete')
				{
					swal("Unknown Error!", "It was not succesfully deleted, no records found, or Unknown Error occured!", "error");
				}
				else
				{
					swal("Unknown Error!", "It was not succesfully deleted, no records found, or Unknown Error occured!", "error");
				}
                
            },
            error: function (xhr, ajaxOptions, thrownError) {
                swal("Error deleting!", "Please try again", "error");
            }
        });
    });
	
	});
	
/* Add new Row */

$(document).on('click', '.add_more', function(e)
{
	var current = $(this);
	var clickType=$(this).attr('data-clickType'); 
	var target_table = "#"+$(this).attr('data-target-table');
	var last_row = $(target_table+' tr:last-child td:first-child').html();
	console.log(last_row);
	 e.stopImmediatePropagation();//use to stop click twice or more
	 
	if(target_table=="#email_cc"){
		if(typeof(last_row) == 'undefined')
		{
		last_row = 0;
		}	
		if(last_row <3)
			{	
			  var new_row ='';
			   
			  if(clickType=="cc")
			  {
			   new_row = '<tr><td>'+ ++last_row+'</td><td class="center"><input type="text" class="form-control" name="ccName[]"  id="ccName_'+ last_row+'" data-type="name"></td><td class="center"><input type="text" class="form-control" name="ccEmail[]" id="ccEmail_'+ last_row+'" data-type="email"></td><td><span class="delete_table_row" data-parent-table="cc_del" data-templte-id=""><i class="fa fa-trash-o common_del" aria-hidden="true"></i></span></td></tr>	';
			  }
			  $(target_table).find('tbody').append($(new_row));		
			  validate_values('#frm-esign');
			  
			  if(last_row == 3)
			  {
				$(current).fadeOut();
			  }
			  
			}
			else
			{
				$(current).fadeOut();
			}
	
	}
	
	else if(target_table=="#email_main")
	{
		if(last_row <6)
		{	
		  var new_row ='';
		  if(clickType=="primary")
		  {
			new_row = '<tr><td>'+ ++last_row+'</td><td class="center"><input type="text" class="form-control" name="primaryName[]" id="primaryName_'+ last_row+'" data-type="name"></td><td class="center"><input type="text" class="form-control" name="primaryEmail[]" id="primaryEmail_'+ last_row+'" data-type="email"></td><td><span class="delete_table_row" data-parent-table="email_main_add"><i class="fa fa-trash-o common_del" aria-hidden="true"></i></span></td></tr>	';
		  }
		 $(target_table).find('tbody').append($(new_row));		
		 validate_values('#frm-esign');
		 
		  if(last_row == 6)
		  {
			  $(current).fadeOut();
		  }
		  
		}
		else
		{	
		 $(current).fadeOut();
		}
	}
});	

/* Delete Row */
 
$(document).on('click', '.delete_table_row', function(){
	var table= '#'+$(this).attr('data-parent-table');
	 
		$(this).closest('tr').fadeOut(function(){
		var rows = $(this).closest('tr').nextAll('tr');
		var cont1 = $(this).closest('tr').prevAll('tr').length;
		var cont2 = $(this).closest('tr').nextAll('tr').length;
		
		var current_row = $(this).closest('tr').find('td:first-child').html();
		$(this).closest('tr').remove();
		validate_values('#frm-esign');
		jQuery.each(rows, function() {
			$(this).find('td:first-child').html(current_row++);
		});
		
		
		if(table == "#cc_del"){
			if((cont2+cont1)<3){
				$('#cc_del').fadeIn();
			}
		}
		if(table == "#email_main_add"){
			console.log(cont2);
			console.log(cont1);
			if((cont2+cont1)<6){
				 
				$('#email_main_add').fadeIn();
			}
		}
		
	
	});
	validate_values('#frm-esign');
	
});


$(document).on('change','#autoCounter',function() {
    if(this.checked) 
	{
		$('#counterName').prop('disabled', false);
	}
	else
	{
		$('#counterName').prop('disabled', true);
		$('#counterName').removeClass('ErrorField');
		$('#counterName').val('');
	}
});



function validate_values(target_accordion_id){
	
	/* Message Not Empty */
	 
	jQuery(function()
    {
	   jQuery('#emailMsg').validate({
			  
			expression: "if (VAL) return true; else return false;",
			message: "Value Required "
			 
			
		}); 
	})	
	/* TIME LIMIT NOT EMPTY */
	jQuery(function()
    {
	   jQuery('#esignLimit').validate({
			  
			expression: "if (VAL != '0') return true; else return false;",
            message: "Please make a selection"
			 
			
		}); 
	})
	/* Reminder Frequency  */
	jQuery(function()
    {
	   jQuery('#reminderFreq').validate({
			  
			expression: "if (VAL != '0') return true; else return false;",
            message: "Please make a selection"
			 
			
		}); 
	})	
	
	
	$(target_accordion_id+" input[type=text]").each(function() 
	{
            var id = "#"+$(this).attr("id");
			var type = $(this).attr("data-type")
			 
		   jQuery(function()
		   {
			   
			   if(type=="name")
			   {
                jQuery(id).validate({
					  
                    expression: "if (VAL) return true; else return false;",
                    message: "Value Required "
					 
					
                }); 
			   }
			  else if(type=="email")
			  {		 
					jQuery(id).validate({
					expression: "if (VAL.match(/^[^\\W][a-zA-Z0-9\\_\\-\\.]+([a-zA-Z0-9\\_\\-\\.]+)*\\@[a-zA-Z0-9_]+(\\.[a-zA-Z0-9_]+)*\\.[a-zA-Z]{2,4}$/)) return true; else return false;",
                    message: "Please enter a valid Email ID"
					});
			  }	
			 })		
    });
	
 }


// Validation function 

function validate_DefaultInputs(target_accordion_id){
		 
	$(target_accordion_id+" input[type=text]").each(function() 
	{
            var id = "#"+$(this).attr("id");
			 
			var type = $(this).attr("data-type");
			var dataRequired = 1;
			 
				 
			  
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
								expression: "if (VAL != '0' || '') return true; else return false;",
								message: "Please make a valid selection"
							});
						}	
						
					}
				   
				});  
			 	
			 
		});	
		
		
 }
 
	
 
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

function onkeyPress(){
	$('input[data-type="Zip"], input[data-type="Phone"], input[data-type="Year"]').keyup(function(e)
  {      
  if (/\D/g.test(this.value))
  {
    // Filter non-digits from input value.
    this.value = this.value.replace(/\D/g, '');
  }
});
} 
 
 	
	
	
	
	
	
function navigationBarCat(){
	var SETTINGS = {
    navBarTravelling: false,
    navBarTravelDirection: "",
	 navBarTravelDistance: 150
	}

	var colours = {
    0: "rgb(245, 130, 32)",
    1: "rgb(245, 130, 32)",
    2: "rgb(245, 130, 32)",
    3: "rgb(245, 130, 32)",
    4: "rgb(245, 130, 32)",
    5: "rgb(245, 130, 32)",
    6: "rgb(245, 130, 32)",
    7: "rgb(245, 130, 32)",
    8: "rgb(245, 130, 32)",
    9: "rgb(245, 130, 32)",
    10: "rgb(245, 130, 32)",
    11: "rgb(245, 130, 32)",
    12: "rgb(245, 130, 32)",
    13: "rgb(245, 130, 32)",
    14: "rgb(245, 130, 32)",
    15: "rgb(245, 130, 32)",
    16: "rgb(245, 130, 32)",
    17: "rgb(245, 130, 32)",
    18: "rgb(245, 130, 32)",
    19: "rgb(245, 130, 32)",
	}

	document.documentElement.classList.remove("no-js");
	document.documentElement.classList.add("js");

	// Out advancer buttons
	var pnAdvancerLeft = document.getElementById("pnAdvancerLeft");
	var pnAdvancerRight = document.getElementById("pnAdvancerRight");
	// the indicator
	var pnIndicator = document.getElementById("pnIndicator");

	var pnProductNav = document.getElementById("pnProductNav");
	var pnProductNavContents = document.getElementById("pnProductNavContents");

	pnProductNav.setAttribute("data-overflowing", determineOverflow(pnProductNavContents, pnProductNav));

	// Set the indicator
	moveIndicator(pnProductNav.querySelector("[aria-selected=\"true\"]"), colours[0]);

	// Handle the scroll of the horizontal container
	var last_known_scroll_position = 0;
	var ticking = false;

	function doSomething(scroll_pos) {
		pnProductNav.setAttribute("data-overflowing", determineOverflow(pnProductNavContents, pnProductNav));
	}

	pnProductNav.addEventListener("scroll", function() {
		last_known_scroll_position = window.scrollY;
		if (!ticking) {
			window.requestAnimationFrame(function() {
				doSomething(last_known_scroll_position);
				ticking = false;
			});
		}
		ticking = true;
	});


	pnAdvancerLeft.addEventListener("click", function() {
		// If in the middle of a move return
		if (SETTINGS.navBarTravelling === true) {
			return;
		}
		// If we have content overflowing both sides or on the left
		if (determineOverflow(pnProductNavContents, pnProductNav) === "left" || determineOverflow(pnProductNavContents, pnProductNav) === "both") {
			// Find how far this panel has been scrolled
			var availableScrollLeft = pnProductNav.scrollLeft;
			// If the space available is less than two lots of our desired distance, just move the whole amount
			// otherwise, move by the amount in the settings
			if (availableScrollLeft < SETTINGS.navBarTravelDistance * 2) {
				pnProductNavContents.style.transform = "translateX(" + availableScrollLeft + "px)";
			} else {
				pnProductNavContents.style.transform = "translateX(" + SETTINGS.navBarTravelDistance + "px)";
			}
			// We do want a transition (this is set in CSS) when moving so remove the class that would prevent that
			pnProductNavContents.classList.remove("pn-ProductNav_Contents-no-transition");
			// Update our settings
			SETTINGS.navBarTravelDirection = "left";
			SETTINGS.navBarTravelling = true;
		}
		// Now update the attribute in the DOM
		pnProductNav.setAttribute("data-overflowing", determineOverflow(pnProductNavContents, pnProductNav));
	});

	pnAdvancerRight.addEventListener("click", function() {
		// If in the middle of a move return
		if (SETTINGS.navBarTravelling === true) {
			return;
		}
		// If we have content overflowing both sides or on the right
		if (determineOverflow(pnProductNavContents, pnProductNav) === "right" || determineOverflow(pnProductNavContents, pnProductNav) === "both") {
			// Get the right edge of the container and content
			var navBarRightEdge = pnProductNavContents.getBoundingClientRect().right;
			var navBarScrollerRightEdge = pnProductNav.getBoundingClientRect().right;
			// Now we know how much space we have available to scroll
			var availableScrollRight = Math.floor(navBarRightEdge - navBarScrollerRightEdge);
			// If the space available is less than two lots of our desired distance, just move the whole amount
			// otherwise, move by the amount in the settings
			if (availableScrollRight < SETTINGS.navBarTravelDistance * 2) {
				pnProductNavContents.style.transform = "translateX(-" + availableScrollRight + "px)";
			} else {
				pnProductNavContents.style.transform = "translateX(-" + SETTINGS.navBarTravelDistance + "px)";
			}
			// We do want a transition (this is set in CSS) when moving so remove the class that would prevent that
			pnProductNavContents.classList.remove("pn-ProductNav_Contents-no-transition");
			// Update our settings
			SETTINGS.navBarTravelDirection = "right";
			SETTINGS.navBarTravelling = true;
		}
		// Now update the attribute in the DOM
		pnProductNav.setAttribute("data-overflowing", determineOverflow(pnProductNavContents, pnProductNav));
	});

	pnProductNavContents.addEventListener(
		"transitionend",
		function() {
			// get the value of the transform, apply that to the current scroll position (so get the scroll pos first) and then remove the transform
			var styleOfTransform = window.getComputedStyle(pnProductNavContents, null);
			var tr = styleOfTransform.getPropertyValue("-webkit-transform") || styleOfTransform.getPropertyValue("transform");
			// If there is no transition we want to default to 0 and not null
			var amount = Math.abs(parseInt(tr.split(",")[4]) || 0);
			pnProductNavContents.style.transform = "none";
			pnProductNavContents.classList.add("pn-ProductNav_Contents-no-transition");
			// Now lets set the scroll position
			if (SETTINGS.navBarTravelDirection === "left") {
				pnProductNav.scrollLeft = pnProductNav.scrollLeft - amount;
			} else {
				pnProductNav.scrollLeft = pnProductNav.scrollLeft + amount;
			}
			SETTINGS.navBarTravelling = false;
		},
		false
	);

	// Handle setting the currently active link
	pnProductNavContents.addEventListener("click", function(e) {
		
		
		var links = [].slice.call(document.querySelectorAll(".pn-ProductNav_Link"));
		links.forEach(function(item) {
			item.setAttribute("aria-selected", "false");
		})
		if(event.target.nodeName!='I')
		{	
			e.target.setAttribute("aria-selected", "true");
			// Pass the clicked item and it's colour to the move indicator function
			moveIndicator(e.target, colours[links.indexOf(e.target)]);
		}
		else{
			var target = event.target;
			var parent = target.parentElement;
			parent.setAttribute("aria-selected", "true");
			// Pass the clicked item and it's colour to the move indicator function
			moveIndicator(parent, colours[links.indexOf(parent)]);	
		}
		
		
	});

	// var count = 0;
	function moveIndicator(item, color) {
		var textPosition = item.getBoundingClientRect();
		var container = pnProductNavContents.getBoundingClientRect().left;
		var distance = textPosition.left - container;
		 var scroll = pnProductNavContents.scrollLeft;
		pnIndicator.style.transform = "translateX(" + (distance + scroll) + "px) scaleX(" + textPosition.width * 0.01 + ")";
		// count = count += 100;
		// pnIndicator.style.transform = "translateX(" + count + "px)";
		
		if (color) {
			pnIndicator.style.backgroundColor = color;
		}
	}

	function determineOverflow(content, container) {
		var containerMetrics = container.getBoundingClientRect();
		var containerMetricsRight = Math.floor(containerMetrics.right);
		var containerMetricsLeft = Math.floor(containerMetrics.left);
		var contentMetrics = content.getBoundingClientRect();
		var contentMetricsRight = Math.floor(contentMetrics.right);
		var contentMetricsLeft = Math.floor(contentMetrics.left);
		 if (containerMetricsLeft > contentMetricsLeft && containerMetricsRight < contentMetricsRight) {
			return "both";
		} else if (contentMetricsLeft < containerMetricsLeft) {
			return "left";
		} else if (contentMetricsRight > containerMetricsRight) {
			return "right";
		} else {
			return "none";
		}
	}

	/**
	 * @fileoverview dragscroll - scroll area by dragging
	 * @version 0.0.8
	 * 
	 * @license MIT, see https://github.com/asvd/dragscroll
	 * @copyright 2015 asvd <heliosframework@gmail.com> 
	 */


	(function (root, factory) {
		if (typeof define === 'function' && define.amd) {
			define(['exports'], factory);
		} else if (typeof exports !== 'undefined') {
			factory(exports);
		} else {
			factory((root.dragscroll = {}));
		}
	}(this, function (exports) {
		var _window = window;
		var _document = document;
		var mousemove = 'mousemove';
		var mouseup = 'mouseup';
		var mousedown = 'mousedown';
		var EventListener = 'EventListener';
		var addEventListener = 'add'+EventListener;
		var removeEventListener = 'remove'+EventListener;
		var newScrollX, newScrollY;

		var dragged = [];
		var reset = function(i, el) {
			for (i = 0; i < dragged.length;) {
				el = dragged[i++];
				el = el.container || el;
				el[removeEventListener](mousedown, el.md, 0);
				_window[removeEventListener](mouseup, el.mu, 0);
				_window[removeEventListener](mousemove, el.mm, 0);
			}

			// cloning into array since HTMLCollection is updated dynamically
			dragged = [].slice.call(_document.getElementsByClassName('dragscroll'));
			for (i = 0; i < dragged.length;) {
				(function(el, lastClientX, lastClientY, pushed, scroller, cont){
					(cont = el.container || el)[addEventListener](
						mousedown,
						cont.md = function(e) {
							if (!el.hasAttribute('nochilddrag') ||
								_document.elementFromPoint(
									e.pageX, e.pageY
								) == cont
							) {
								pushed = 1;
								lastClientX = e.clientX;
								lastClientY = e.clientY;

								e.preventDefault();
							}
						}, 0
					);

					_window[addEventListener](
						mouseup, cont.mu = function() {pushed = 0;}, 0
					);

					_window[addEventListener](
						mousemove,
						cont.mm = function(e) {
							if (pushed) {
								(scroller = el.scroller||el).scrollLeft -=
									newScrollX = (- lastClientX + (lastClientX=e.clientX));
								scroller.scrollTop -=
									newScrollY = (- lastClientY + (lastClientY=e.clientY));
								if (el == _document.body) {
									(scroller = _document.documentElement).scrollLeft -= newScrollX;
									scroller.scrollTop -= newScrollY;
								}
							}
						}, 0
					);
				 })(dragged[i++]);
			}
		}

		  
		if (_document.readyState == 'complete') {
			reset();
		} else {
			_window[addEventListener]('load', reset, 0);
		}

		exports.reset = reset;
	}));
	
}	