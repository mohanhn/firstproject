$('body').on('change','#select_member_name', function()
{
	
	var member= $( "#select_member_name option:selected" ).text();
	$('#membername').val(member);
	$('#onchanged').val("onchangeevent");
	 var formdetails=$('#select_member_list').serialize();
		 jQuery.ajax(
				{	type: "POST",
					url : "ajax-signaturestp.php",
					data:formdetails,
					success:function(data)
					{	
						$("#cardbox_signature").html(data);
					},
					error: function(jqXHR, textStatus, errorThrown) 
					{
						 //$('#cancel_occ').click();
						swal("Error", "unknown error occured, Please try again", "error");	 
						   
					}
				}) 
});






 $('body').on('change', '#select_esign_days', function()
 {
	$("#reminder_frequency").removeAttr('disabled');
 });
 
 
	$('body').on('click', '#btn_addmore_1', function()
 {
	 var current = $("#count").val();
	 var next= Number(current) + 1;
	 var displaydiv="#tr_1_"+next;
	 //var deleIdshow="#del_1_"+next;
	 var deleIdhide="#del_1_"+current;
	  $(displaydiv).show();
	  $(deleIdhide).hide();
	  $("#count").val(next);
	  
		if(next==6)
		{
			$('#btn_addmore_1').hide();
		} 
 });
	
	
	
	$('body').on('click', '#btn_addmore_2', function()
 {
	 var current = $("#count1").val();
	 var next= Number(current) + 1;
	 var displaydiv="#tr_2_"+next;
	var deleIdhide="#del_2_"+current;
	  $(displaydiv).show();
	   $(deleIdhide).hide();
	  $("#count1").val(next);
	  
		if(next==3)
		{
			$('#btn_addmore_2').hide();
		}
		
	 
	
	 
 });
 
 $('body').on('click', '.deleteBtn', function()
 {
	  var current = $("#count").val();
	  var prev= Number(current) - 1;
	 // var next= Number(current) + 2;
	   var displaydiv="#tr_1_"+current;
	    $(displaydiv).hide();
		$("#count").val(prev);
		var deleIdhide="#del_1_"+prev;
		$(deleIdhide).show();
		
		if(prev==5)
		{
			$('#btn_addmore_1').show();
		} 
	
	
	 
	
	 
 });
 
 
 
 
 $('body').on('click', '.deleteBtn1', function()
 {
	  var current1 = $("#count1").val();
	  var prev1= Number(current1) - 1;
	 // var next= Number(current) + 2;
	   var displaydiv1="#tr_2_"+current1;
	    $(displaydiv1).hide();
		$("#count1").val(prev1);
		var deleIdhide1="#del_2_"+prev1;
		$(deleIdhide1).show();
		
		if(prev1==2)
		{
			$('#btn_addmore_2').show();
		} 
	
	
	 
	
	 
 });
 
 
 

 
 
$('body').on('click','#check_autocounter', function()
{
	if($(this). prop("checked") == true){
		$("#countername").attr("disabled", false);
	}else
	{
		$("#countername").attr("disabled", true);
	}
});






$('body').on('click', '#save_sign', function()
 {
	  $('#saveall').val('save');    // setting value save to saveall hidden field
	  $('#inserted').val("Inserting");
		 var formdetails=$('#select_member_list').serialize();
		 jQuery.ajax(
				{	type: "POST",
					url : "ajax-signaturestp.php",
					data:formdetails,
					success:function(response)
					{	
						if(response.trim()=='inserted')
						{
							swal("SUCCESS","values inserted.", "success");
							//$('#form_details')[0].reset();
							//$('#cancel_occ').click();
							 //setTimeout(function() {location.reload();},2000);
						
							
						}else
						{
							swal("Error", "Not inserted, Please try again", "error");
						}
					},
					error: function(jqXHR, textStatus, errorThrown) 
					{
						 //$('#cancel_occ').click();
						swal("Error", "unknown error occured, Please try again", "error");	 
						   
					}
				}) 
 });
 
 
 
 
 
 
 
 
 
 
 
 
 
 
