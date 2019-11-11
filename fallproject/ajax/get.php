<?php

if(!isset($_POST)){
	echo "not a post";
	exit(0);
}
$req = $_POST;
$res = array("status"=>"404", "response"=>"nada");
if(isset($_POST['type'])){
	switch($_POST['type']){
		case "get/user":
			$res = "Here's a user";
			break;
		case "get/post":
			$res = "post it";
			break;
		case "get/comment":
			break;
		case "login":
			$res["status"] = "200";
			$res["response"] = doLogin($_POST['username'],$_POST['password']);
			break;
		default:
			break;
	}
}
function doLogin($username, $password){
	//db connection
	//find user by username
	//password verify
	return "Welcome, $username";
}
echo json_encode($res);

?>