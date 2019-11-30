<?php
include_once("partials/header.php");
include_once("helpers/functions.php");
if(is_admin()){
	//TODO fill it out
}
else{
	echo "Get outta here!";
	header("Location: dashboard.php");
}
?>

<form method="POST" style="display: inline-grid;">
	<input type="text" name="name" placeholder="Course Name/Text ID"/>
	<input type="text" name="section" placeholder="Section"/>
	<textarea name="description" placeholder="Description of Course"></textarea>
	<input type="submit" value="Create Course"/>
</form>


<?php
if(isset($_POST['name']) && isset($_POST['section']) && isset($_POST['description'])){
	
	$name = $_POST['name'];
	$section = $_POST['section'];
	$desc = $_POST['description'];
	
	require("config.php");
	$conn_string = "mysql:host=$host;dbname=$database;charset=utf8mb4";
	$db = new PDO($conn_string, $username, $password);
	$sql = "INSERT INTO Courses (name, section, description)
			VALUES (:name, :section, :description)";
	$stmt = $db->prepare($sql);
	$r = $stmt->execute(array(":name"=>$name, ":section"=>$section, ":description"=>$desc));
	echo ($r)?"Course created":"Course not created";
}