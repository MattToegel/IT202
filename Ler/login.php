<html>
	<head>
		<title>Ler - Login</title>
	</head>
	<body>
		<!-- This is how you comment -->
		<form name="loginform" id="myForm" method="POST">
			<label for="text">Email/Username: </label>
			<input type="text" id="email" name="email" placeholder="Enter Email/Username"/>
			<label for="pass">Password: </label>
			<input type="password" id="pass" name="password" placeholder="Enter password"/>
			<input type="submit" value="Login"/>
		</form>
	</body>
</html>
<?php 
ini_set('display_errors',1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require("bootstrap.php");

if(isset($_POST['email']) && isset($_POST['password']) && !empty($_POST['password'])){
	$email = $_POST['email'];
	$pass = $_POST['password'];
	
	$users = $container->getUsers();
	$user = $users->login($email, $pass);
	echo var_export($user,true);
	echo var_dump($user->hasRoleByName("admin"));
	if($user){
		//$_SESSION['user'] = $user;
		Utils::login($user);
		header("Location: home.php");
	}
}
?> 