<?php

$varname="pradeep";
for($i=0; $i<strlen($varname); $i++)
{
	if($varname[$i]=="e")
	{
		$clr='red';
	}else
	{
		$clr='black';
	}
	echo "<p style='color:{$clr}'>".$varname[$i]."<P></br>";
}

?>