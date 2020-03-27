<?php
$age = 55;
if(isset($_REQUEST['age'])){
	$age = $_REQUEST['age'];
}
$fake_data = array("name"=>"John", "age"=>$age, "gender"=>male);
echo json_encode($fake_data);
?>
