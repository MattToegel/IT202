<?php
session_start();
if(isset($_POST['trigger'])){
	$_SESSION['loggedIn'] = "Hal";
}
function getUser(){
	if(isset($_SESSION['loggedIn'])){
		echo var_export($_SESSION['loggedIn']);
	}
}
?>

<html>
<head>
</head>
<body>
<header>
	<p style="<?php if(!getUser()) echo 'display:none;';?>">
		Good morning, <?php getUser();?>
	</p>
</header>
<main>
	<section>
		<form method="POST">
			<input type="hidden" name="trigger" value="y"/>
			<input type="submit" value="Don't click me"/>
		</form>
	</section>
</main>
</body>
</html>
