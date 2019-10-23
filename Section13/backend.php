<?php

function get_sample_users(){
	require('config.php');
	$conn_string = "mysql:host=$host;dbname=$database;charset=utf8mb4";
	try{
		$db = new PDO($conn_string, $username, $password);
		$select_query = "select id, username from `TestUsers`";
		$stmt = $db->prepare($select_query);
		$r = $stmt->execute();
		$response = json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
	}
	catch(Exception $e){
		$response = "DB Error: " . $e;
	}
	return $response;
}
if(isset($_POST["type"])){
	$type = $_POST["type"];
	$response = "nothing";
	switch($type){
		case "echo":
			if(isset($_POST["message"])){
				$response = "<b>Echo</b> " . $_POST["message"];
			}
			else{
				$response = "Nothing to echo...";
			}
			break;
		case "add":
			if(isset($_POST["number1"]) && isset($_POST["number2"])){
				$a = $_POST["number1"];
				$b = $_POST["number2"];
				if(is_numeric($a) && is_numeric($b)){
					$a = (int)$a;
					$b = (int)$b;
					$response = ($a+$b);
				}
				else{
					$response = "At least one of the values isn't a number";
				}
			}
			else{
				$response = "Missing one of the required fields of number1 or number2";
			}
			break;
		case "html":
			$response = "<p>I was pulled from my home backend.php</p>";
			break;
		case "json":
			$obj->name = "Jack";
			$obj->job = "Waiting at backend.php";
			$obj->age = 70;
			$response = json_encode($obj);
			break;
		case "db":
			$response = get_sample_users();
			break;
		default:
			$response = $type . " hasn't been implemented yet, please try again";
			break;
	}
	echo $response;
}
?>
