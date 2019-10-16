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
	if(isset($_GET['n1'])){
		$n1 = $_GET['n1'];
	}
	if(isset($_GET['n2'])){
		$n2 = $_GET['n2'];
	}
	if(isset($n1) && isset($n2)){
		if(is_numeric($n1) && is_numeric($n2)){
			$n1 = (int)$n1;
			$n2 = (int)$n2;
			echo "Sum: " . ($n1 + $n2);
		}
		else{
			echo "<br> Values aren't numbers";
		}
		echo "<br>";
		echo "Concat: " . ($n1 . $n2);
	}
	if(isset($_GET['parameter'])){
		echo "<div> " . $_GET['parameter'] . "</div>";
	}

	//concatenate 2 or more parameter values and echo them
	//try passing multiple same-named parameters with different values
	//try passing a parameter value with special characters


	//web.njit.edu/~ucid/IT202/handleRequestData.php?parameter1=a&p2=b
?>
