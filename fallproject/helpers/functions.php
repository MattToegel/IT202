<?php

function get_username(){
	if(isset($_SESSION['user']['name'])){
		echo $_SESSION['user']['name'];
	}
	else{
		echo "[Session missing]";
	}
}

function get_user_id(){
	if(isset($_SESSION['user']['id'])){
		return $_SESSION['user']['id'];
	}
	else{
		return -1;
	}
}

function is_admin(){
	if(isset($_SESSION['user']['isAdmin'])){
		return $_SESSION['user']['isAdmin'];
	}
	return false;
}
?>