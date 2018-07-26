/*
=======================================================
       Default repository setup

=======================================================
*/
/* Author :Pradeep
* Date : 01 August 2017
* Used in: ajax-default-rp.php
* Description :
* This function is to add
  new default field*/


  $('body').on('click','.details_save',function ()
 {


   var category=$("#select_new_Cat").val();
   var subCategory=$("#select_subcat_field").val();
   var fieldType=$("#field_type").val();

   var fieldname=$("#select_field_name").val();
   var optionalLable = $("#optional_lable").val();
   var xmlBlockTag  = $("#xmlBlockTag").val();
   var xmlFieldTag  = $("#xmlFieldTag").val();
   var dropdownCheck = 0;
   if(fieldType == 'Dropdown')
   {
     var fieldDropdownType = $("#field_dropdown_type").val();
     if(fieldDropdownType!='')
     {
       dropdownCheck = 1;
     }
     else
     {
       dropdownCheck = 0;
     }

   }
   else
   {
     dropdownCheck =1;
   }



   if((category!='') && (subCategory!='') && (fieldType!='') && (fieldname!='') &&  (xmlBlockTag!='') && (xmlFieldTag!='') && (dropdownCheck!=0))
   {
     var adddetail=document.getElementById("newfields").value="adddetails";
     var formdata=$("#newfiledform").serialize();
       $.ajax(
       {
         type: "POST",
         url : "ajax-default-rp.php",
         data:formdata,
         beforeSend: function()
          {
           $('.pre_loader_block').fadeIn();
          },
         success: function(data)
          {
           if(data.trim()=='inserted')
           {
             swal("SUCCESS","New field created, for additional  attributes like field default or making it required you can use template edit option", "success");
              $('#newfiledform')[0].reset();
             $('.disableInit').each(function(){
               $(this).prop("disabled", true);
             })
             $('#select_subcat_field').prop("disabled", true);
             //$('#new_content_area').fadeOut("slow");
             $('.pre_loader_block').fadeOut();
           }else{
             swal("Error", "Something went wrong, Please try again!", "error");
           }
          }
       });

   }
   else
   {
     swal("Error", "All fields are required", "error");
   }
 });




  $('body').on('click','.add_new_template',function ()
 {
   document.getElementById("select_field").options[0].selected = true;
   $('.realtive1').fadeOut();
   $('.realtive').fadeIn("slow");
   $('#field_content_table').fadeIn("slow");


 });

/*
 $('body').on('click','.addnewfielddetails',function ()
 {
   $('.successmsg').hide();

 }); */



 $('body').on('click','.details_cancel',function ()
 {
   $('#newfiledform')[0].reset();
   $('.realtive').fadeOut("slow");
 });



/* Author :Pradeep
* Date : 01 August 2017
* Used in: ajax-default-rp.php
* Description :
* This function is to display
  category fields*/

$(document).on('change','#select_field',function ()
 {
   var optionvalue = $("#select_field option:selected").val();
   $('#field_content_table').fadeOut();
   $('.realtive').fadeOut();
   $('.realtive1').fadeIn();
   var variable='option_value1='+optionvalue;

   if(optionvalue!='')
   {
     jQuery.ajax(
           {
             type: "POST",
             url : "ajax-default-rp.php",
             data:variable,
             beforeSend: function()
              {

                            },
                           success: function(response)
                            {
               $('#alldetails_content_area').html(response.trim());

                            }

                       });
   }

 });



 /* Author :Pradeep
* Date : 01 August 2017
* Used in: ajax-default-rp.php
* Description :
* This function is to save
  the cahnges in a row*/
  $('body').on('click','.savesinglerow',function ()
 {

   var recordID = $(this).attr('id');
   var selectedRecord = 'selectedRecord='+ recordID;
   jQuery.ajax(
   {	type: "POST",
     url : "ajax-default-rp.php",
     data:selectedRecord,
     success:function(response)
     {
       if(response.trim()!='unknownFeedID')
       {
         $('#modelEditField').html(response);
         $('#modelEditBox').modal();
       }else{
           swal("Error", "Something went wrong, Please try again!", "error");
       }

     },

   })

 });


   /* Author :Pradeep
* Date : 01 August 2017
* Used in: ajax-default-rp.php
* Description :
* This function is to delete
  the a row*/
  $('body').on('click','.deleterow',function (e)
 {
 var deleterow=$(this).closest('tr');
   var deleteid=$(this).attr('id');
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
       var deleterpid='delete_rp_row='+deleteid;
       jQuery.ajax(
       {	type: "POST",
         url : "ajax-default-rp.php",
         data:deleterpid,
         success:function(response)
         {
         if(response.trim()=='deleted')
         {
           $(deleterow).remove();
           swal("SUCCESS","Template Deleted", "success");
         }else
         {
           swal("Error", "Something went wrong, Please try again!", "error");
         }

           /* if(response.trim()=='done')
           {
             swal("SUCCESS","Changes updated.", "success");
           }else{
               swal("Error", "Something went wrong, Please try again!", "error");
           } */

         }

       })
       e.preventDefault(); //STOP default action

   });
   return true;
 });








 // Functions to Handle the Entry Of  a New Field



$(document).on('change','#select_new_Cat',function ()
{
 var optionvalue = $("#select_new_Cat option:selected").val();

 var variable='inputSelCategory='+optionvalue;
 var preLoad = '<div class="pre_loader_block loader_height" style="z-index: 2147483647;background: transparent;" id="leaseInput-pakg-loader"><img id="image" class="pre_loader_img img-responsive" src="assets/images/progresscircle.gif"></div>';

 if(optionvalue!='')
 {
   jQuery.ajax(
         {
           type: "POST",
           url : "ajax-default-rp.php",
           data:variable,
           beforeSend: function()
            {
             $('#select_subcat_div').append(preLoad);
            },
           success: function(response)
            {
             $('.disableInit').each(function(){
               $(this).prop("disabled", false);
             })
             $('#select_field_name').val("");
             var jsonData = JSON.parse(response);
             $('#select_subcat_div').html(jsonData['dispSubCat']);
             $('#xmlField_Block_div').html(jsonData['dispXmlMasterTag']);
             console.log(jsonData['dispSubCat']);
            }

         });
 }

});

// field_type
$(document).on('change', '#field_type', function(){
 var fieldType = $('#field_type option:selected').val();
 if(fieldType == 'Dropdown')
 {	$("#field_dropdown_type").prop("disabled", false);
   $('#dropdownTypeName').fadeIn();
 }
 else
 {
   $("#field_dropdown_type").prop("disabled", true);
   $('#dropdownTypeName').fadeOut();
 }
})

// effield_type for Filed Update

$(document).on('change', '#effield_type', function(){
 var fieldType = $('#effield_type option:selected').val();
 if(fieldType == 'Dropdown')
 {  $("#effield_dropdown_type").prop("disabled", false);
   $('#efdropdownTypeName').addClass('drpShow');
 }
 else
 {
   $("#effield_dropdown_type").prop("disabled", true);
   $('#efdropdownTypeName').removeClass('drpShow');
 }
})


// Checking Field Name

$(document).on('blur', '#select_field_name', function(){
 var optionvalue = $("#select_new_Cat option:selected").val();
 var myNewName = $("#select_field_name").val();
 var variable='inputCatName='+optionvalue+ '&myNewName='+ myNewName;
  console.log("dd");

 //var preLoad = '<div class="pre_loader_block loader_height" style="z-index: 2147483647;background: transparent;" id="leaseInput-pakg-loader"><img id="image" class="pre_loader_img img-responsive" src="assets/images/progresscircle.gif"></div>';

 if(optionvalue!='')
 {
   jQuery.ajax(
         {
           type: "POST",
           url : "ajax-default-rp.php",
           data:variable,

           success: function(response)
            {
             response = response.replace(/\s/g, "");
             if(response == 'exists')
             {
               $('#mErrorMsg').fadeIn();
             }
             else if(response =='notexists')
             {
               $('#mErrorMsg').fadeOut();
             }
             else if(response == 'unKnownError')
             {
               $('#mErrorMsg').fadeOut();
             }

             else
             {
               $('#mErrorMsg').fadeOut();
             }

            }

         });
 }

})
