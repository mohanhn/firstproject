$('body').on('change','.package_selection', function()

{
	var package =$(this).val().trim();
	alert(package);
	var tex=$("package_selection option:selected").text();
	var selected_package='package_select='+package;
	
	
})