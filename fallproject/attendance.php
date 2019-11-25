<?php
include_once("partials/header.php");
include_once("helpers/functions.php");
?>

<html>
<head>
</head>
<body>
	<form method="POST">
		<input type="hidden" name="id" value="<?php get_user_id();?>"/>
		<input type="text" name="confirm"/>
		<input type="submit" value="Check In"/>
	</form>
</body>
</html>

<?php

if(isset($_POST["id"]) && isset($_POST["confirm"])){
	$id = $_POST["id"];
	if($id !== get_user_id()){
		echo "ID should match, form was tempered with";
		exit(0);
	}
	if(get_user_id() == -1){
		echo "You shouldn't be here";
		exit(0);
}
	$confirm = $_POST["confirm"];
	//TODO implement confirm; remove placeholder
	if($confirm){
		require("config.php");
		$conn_string = "mysql:host=$host;dbname=$database;charset=utf8mb4";
		$db = new PDO($conn_string, $username, $password);
		$stmt = $db->prepare("INSERT into `Attendance` (`user_id`, `code`, `attendance_id`) VALUES(:user_id, :code, :attendance_id)");
		//TODO implement attendance_id and remove hard coded "1"
		$result = $stmt->execute(
			array(":user_id"=>$id,
				":code"=>$confirm,
				":attendance_id"=>1
			)
		);
		if($result){
			echo "You successfully checked in, thank you!";
		}
		else{
			//TODO implement single day check-in
			echo "There was an error";
			echo var_export($stmt->errorInfo(), true);
			echo var_export($result, true);
		}
	}
}