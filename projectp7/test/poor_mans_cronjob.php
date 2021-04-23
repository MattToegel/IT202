<?php
session_start();
function poor_mans_cronjob(){
	//change 0 here if you want to provide an initial delay
	//otherwise it's instant
	$next = isset($_SESSION["nextTime"])?$_SESSION["nextTime"]:0;
	if(time() >= $next){
		//do something here at the interval
		
		//rest of it is just reset and tracking logic
		$runs = 1;
		if(isset($_SESSION["runs"])){
			$runs = (int)$_SESSION["runs"];
		}
       
		echo "weeee! we ran $runs times.";
		$delay = 5;
		if(isset($_SESSION["delay"])){
			$delay = (int)$_SESSION['delay'];
		}
		$_SESSION["nextTime"] = time() + $delay;
		$_SESSION["runs"] += 1;
	}
}

poor_mans_cronjob();
?>