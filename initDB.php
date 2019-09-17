<?php
#turn error reporting on
ini_set('display_errors',1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


require('config.php');
echo $host;


$connection_string = "mysql:host=$host;dbname=$database;charset=utf8mb4";

try{
	$db = new PDO($connection_string, $username, $password);
	echo "Should have connected";
}
catch(Exception $e){
	echo $e->getMessage();
	exit("It didn't work");
}
?>
