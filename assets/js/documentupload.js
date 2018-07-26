
/*
	=======================================================
		Document Upload Sarfarz
    =======================================================
*/    
    /*
     * Form Validator for input fields
     */
   
   function form_validate()
   {	
		var frmvalidator = new Validator("fileupload");
		frmvalidator.EnableOnPageErrorDisplay();
		frmvalidator.EnableMsgsTogether();
		frmvalidator.addValidation("docProfileSelect", "Please select a member");
		frmvalidator.addValidation("document", "req", "Please choose a file to upload");
		frmvalidator.addValidation("name", "req", "Name field is empty");
   }	
    /**
     * @param {type} delId is to delete the
     * selected document throw ajax request
     * from documents table & posting page is 
     * ajax-delete-download-doc.php
     */
    function getId(delId){
        var del = "doc"+delId;
        //passing the id to retrieve 
        //membername & mamber state to get
        //members document directory
         swal({
        title: "Are you sure?",
        text: "You want to delete this document file!",
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: "#DD6B55",
        confirmButtonText: "Yes, delete it!",
        closeOnConfirm: false
    }, function (isConfirm) {
        if (!isConfirm) return;
        $.ajax({
            url: 'ajax-delete-download-doc.php?delete='+delId,
            type: "POST",
            data:{delete:delId},
            success: function (resp) {
               swal("Done!", resp, "success");
               document.getElementById(''+del+'').remove();
            },
            error: function (xhr, ajaxOptions, thrownError) {
                swal("Error deleting!", "Please try again", "error");
            }
        });
    });
    }
    
    
     /**
     * Description:
     * This function wsa to created to get the
     * selected document from that particular id
     */
    function getDoc(e){
         //  var member = e.dataset.member;
         ////   var docname = e.id;
        // window.open('documents/'+member+'/'+docname);
		// location.href = "http://localhost/mavitech/leasingpro" + 'documents/'+member+'/'+docname;
 
    }
    
    
    /*
     * Desctioption:
     * On dropdown select change event make a ajax request 
     * & clear the previous table content & load new content
     * with respect to that member. 
     */
    $(document).ready(function(){
        //alert(this.value);
        $('#docContent').fadeIn();
        $.ajax({
            url: 'ajax-delete-download-doc.php?docProfileSelect="set"',
            type: "POST",
            data:{delete:''},
            beforeSend: function()
            {
                var rows = document.getElementById('tblbody').getElementsByTagName("tr").length;
                var row = document.getElementById('tblbody');
                 if(rows == 0)
                    {
                    // do nothing
                    }
                    else{	
                     $('#tblbody').html('');
                    }
            },
            success: function (response) 
            {
                console.log(response);
                var parsed_data = JSON.parse(response);
                var table = document.getElementById("tblbody");
                
                for(var i=0;i<parsed_data.length;i++)
                    {
                        var row = table.insertRow(i);
                         row.id="doc"+parsed_data[i]['iddocuments'];
                        var cell1 = row.insertCell(0);
                         
                        var cell3 = row.insertCell(1);
                        var cell4 = row.insertCell(2);
                        var cell6 = row.insertCell(3);
                         

                        var j = i+1;
                        cell1.innerHTML = j;
                        cell1.style.textAlign = "center";
                       
                         
                        cell3.innerHTML = parsed_data[i]['documentuploaded'];
                        cell4.innerHTML = parsed_data[i]['dateuploaded'];
                        
                        cell6.innerHTML = "<table class='table action_table'><tr><td><span class='delete glyphicon glyphicon-trash ' style='color:red; padding:4%;' id ='"+parsed_data[i]['iddocuments']+"'"+
                        "onclick='getId(this.id);'></span></td>"+"<td><a href='http://"+parsed_data[i]['ftpbaseurl']+parsed_data[i]['ftpfoldername']+"/"+parsed_data[i]['documentname']+"' download=''><span class='download glyphicon glyphicon-download-alt'></span></a></td></tr></table>";
                        cell6.style.textAlign = "center";
                    }
            }
        });
    });
    
    

	$(document).on('click', '#uploadFile', function fn(e){
		
		e.preventDefault();
		var form1 = $('#fileupload').serialize();
		var filePath = $('#document').val();
		var filename = (($('#name').val()).replace( /\s\s+/g, ' ' )).trim();
		if((filename!='') && (filePath!=''))
		{	
			var formDataCheck = '&mfilePath='+filePath+'&mfilename='+filename;
			console.log(formDataCheck);
			$.ajax(
			{	type: "POST",
				url : "ajax-delete-download-doc.php",
				data:formDataCheck,
				 beforeSend: function(){
							
						},
				success: function(data)
					{
						 
						var data = data.trim();
						if(data=='nameExists')
						{
							$('#fileupload_name_errorloc').html('File Name exists in records, please enter a diffrent name')
							$('#fileupload_name_errorloc').css('display', 'block');
						}	
						else{
						  $('#upload').trigger('click');
						}
						
						 
					}

			});
		}
		else{
			if(filePath=='')
			{
				$('#document').addClass('ErrorField');
			}
			if(filename=='')
			{
				$('#name').addClass('ErrorField');
				$('#name').val('');
			}			
		}
			
	})
		
	$(document).on('input', '#name', function fn(e){
		
		$('#name').removeClass('ErrorField');
		$('#fileupload_name_errorloc').css('display', 'none');
		$('#fileupload_name_errorloc').html('');
		 
	})
	$(document).on('change', '#document', function fn(e){
		
		 
		$('#document').removeClass('ErrorField');
	})
    
