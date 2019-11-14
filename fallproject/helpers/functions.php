<?php

function get_username(){
	if(isset($_SESSION['user']['name'])){
		echo $_SESSION['user']['name'];
	}
	else{
		echo "[Session missing]";
	}
}
?>