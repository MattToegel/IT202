<?php
#turn error reporting on
ini_set('display_errors',1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
//pull in config.php so we can access the variables from it
require('config.php');
echo "Loaded Host: " . $host;

$conn_string = "mysql:host=$host;dbname=$database;charset=utf8mb4";

try{
	$db = new PDO($conn_string, $username, $password);
	echo "Connected";
	//create table
	$query = "create table if not exists `TestUsers`(
		`id` int auto_increment not null,
		`username` varchar(30) not null unique,
		`pin` int default 0,
		PRIMARY KEY (`id`)
		) CHARACTER SET utf8 COLLATE utf8_general_ci";
	$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
	$stmt = $db->prepare($query);
	print_r($stmt->errorInfo());
	$r = $stmt->execute();
	echo "<br>" . ($r>0?"Created table or already exists":"Failed to create table") . "<br>";
	unset($r);
	//simple insert
	//TODO/homework make values variables bindable
	$insert_query = "INSERT INTO `TestUsers`( `username`, `pin`) VALUES ('JohnDoe', 1234)";
	$stmt = $db->prepare($insert_query);
	$r = $stmt->execute();
	//TODO catch error from DB
	echo "<br>" . ($r>0?"Insert successful":"Insert failed") . "<br>";
	
	//TODO select query using bindable :username is where clause
	//select * from TestUsers where username = 
}
catch(Exception $e){
	echo $e->getMessage();
	exit("Something went wrong");
}
?>
