<?php
	echo "<pre>" . var_export($_GET, true) . "</pre>";

	if(isset($_GET['name'])){
		echo "<br>Hello, " . $_GET['name'] . "<br>";
	}
	if(isset($_GET['number'])){
		$number = $_GET['number'];
		echo "<br>" . $number . " should be a number...";
		echo "<br>but it might not be<br>";
	}
	//TODO
	//handle addition of 2 or more parameters with proper number parsing
	//concatenate 2 or more parameter values and echo them
	//try passing multiple same-named parameters with different values
	//try passing a parameter value with special characters


	//web.njit.edu/~ucid/IT202/handleRequestData.php?parameter1=a&p2=b
?>
