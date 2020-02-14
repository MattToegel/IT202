<?php
session_start();
//showing you can pull from a sub folder
include("helpers/function.php");
echo "<pre>" . var_export($_SESSION, true) . "</pre>";

echo "User is logged in: " . is_logged_in();
echo "<br>";
echo "User is admin: " . is_admin();

$test = 1;
echo "<br>";
echo "value of test is $test";
echo "<br>";
echo 'value of test is $test';

if(is_admin()){
	echo "Only you can see this";
}

/*if(!is_admin()){
	//exit();
	header("Location: login.php");
}*/
is_admin_redirect();
?>
