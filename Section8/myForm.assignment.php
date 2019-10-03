<?php
ini_set('display_errors',1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
function getName(){
	if(isset($_GET['name'])){
		echo "<p>Hello, " . $_GET['name'] . "</p>";
	}
}
?>
<html>
<head></head>
<body><?php getName();?>
<form mode="GET" action="#">
<input name="name" type="text" placeholder="Enter your name"/>
<!-- add password field-->
<!-- add confirm password field-->
<!-- ensure passwords match before sending the form

		AND/OR
	validate password matches confirmation on php side
-->
<!-- change form submit type to post, adjust php checks for change in type-->

<input type="submit" value="Try it"/>
</form>
</body>
</html>

<?php
if(isset($_GET)){
	echo "<br><pre>" . var_export($_GET, true) . "</pre><br>";
}
?>
