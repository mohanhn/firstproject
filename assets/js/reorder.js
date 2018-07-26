/*
		=======================================================
	
				 TEMPLATE REORDER
	
	=======================================================
					Display Selected tab info
    =======================================================
	*/
	
		/* Author :Pradeep 
 * Date : 21 July 2017 
 * Used in:arrange-default.php
 * Description :  
 * This function is to display
	default lables*/
	
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
				url : "ajax-reorder.php",
				dataType:"text",
				data:optionval,
				beforeSend:function(){
					$("#category-list .label-accordion").removeClass("active");
					 $('.order-loader').fadeIn();
					/*$(".template-sec").fadeOut(); */
					$('.order-content').html('');
					
				},
				success:function(data){
					 $(".order-default").fadeIn();
					 $('.order-content').html(data);
					/*  setTemplateLabel(selval, sel_inner_html); */
					$(".template-sec").fadeIn();
					$("#category-list .label-accordion:first").addClass("active");
					$('.order-loader').fadeOut();
					
					
					  $(function() {
          $('.draggable-element').arrangeable();
          $('li').arrangeable({dragSelector: '.drag-area'});
      });
					  
				},
				error: function(jqXHR, textStatus, errorThrown) 
				{
					//if fails   
				 
				}
			})
			
			e.preventDefault(); //STOP default action    
		}
	});
	

	
		/* Author :Pradeep 
 * Date : 24 July 2017 
 * Used in:arrange-default.php
 * Description :  
 * This function is to update 
 order number to the respected tables*/
	
	$(document).on('click', '.save-order', function(e)
	{
	
		var currentFrtom = $('.form-block.active');
		var current = $('#category-list .label-accordion.active');
		/* var currentInput = current.prev('input');
		console.log(currentInput); */
		var currentID = '#'+$(current).attr('data-target');
		var next = current.parent().next().find('label.label-accordion');
		var nextt = next.attr('data-target');
		var nextID = '#'+next.attr('data-target');
		var nextTable = next.attr('data-input-tab');
		var previous = current.parent().prev().find('label.label-accordion');
		var previousID = '#'+previous.attr('data-target');
		var k = 0;
		
		
		$('.form-block.active').find("input[name='autoCounter[]']").each(function(){
			k++;
			$(this).val(k);
		});
			var form1=currentFrtom.serialize();
			console.log(form1);
			jQuery.ajax(
			{	type: "POST",
				url : "ajax-reorder.php",
				dataType:"text",
				data:form1,
				beforeSend:function(){
					 $('.order-loader').fadeIn();
					
				},
				success:function(data){
					if(data=="UnknownTableName")
					{
						swal("Sorry!", "Your data was not saved.", "error");
					}
					else if(data=="NotExist")
					{
						swal("Sorry!", "No template exist.", "error");
					}
					else{
						$(current).removeClass("active");
						$(currentID).removeClass("active");
						$(next).addClass('active');
						$(nextID).addClass('active');
						$(currentID).hide();
						$(nextID).fadeIn();
						$('.order-loader').fadeOut();
					}
					
					
					  
				},
				error: function(jqXHR, textStatus, errorThrown) 
				{
					//if fails   
				 
				}
			})
			
			e.preventDefault(); //STOP default action    
	
	});
	
	
	
	
	$(document).on('click', '.save-continue', function(e)
	{
	
		var currentFrtom = $('.form-block.active');
		var current = $('#category-list .label-accordion.active');
		/* var currentInput = current.prev('input');
		console.log(currentInput); */
		var currentID = '#'+$(current).attr('data-target');
		var next = current.parent().next().find('label.label-accordion');
		var nextt = next.attr('data-target');
		var nextID = '#'+next.attr('data-target');
		var nextTable = next.attr('data-input-tab');
		var previous = current.parent().prev().find('label.label-accordion');
		var previousID = '#'+previous.attr('data-target');
		var k = 0;
		
	    $(current).removeClass("active");
	    $(currentID).removeClass("active");
	    $(next).addClass('active');
	    $(nextID).addClass('active');
	    $(currentID).hide();
	    $(nextID).fadeIn(); 
	
	});
	
	

	
	
		$(document).on('click','#category-list .label-accordion',function(e){
			var target = '#'+$(this).attr('data-target');
			$("#category-list .label-accordion").removeClass("active");
			$(this).addClass('active');
			$('.form-block').removeClass('active');
			$(target).addClass('active');
			$('.form-block').hide();
			$(target).fadeIn();
		})
		  
			/* --------------------------------------------------------
				Active Tab 
			----------------------------------------------------------- */ 
		 function active_tab(){
				 var taget_id = $(".nav-tabs .active a").attr("href");
				 $("#panels .tab-pane.active").removeClass("active");
				 $(taget_id).addClass("active");
				
			 }
	
	