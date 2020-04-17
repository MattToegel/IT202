<?php
//TODO making it dynamically load boostrap if we're not using the routing sample
if (!isset($container)) {
	require(__DIR__ . "/../bootstrap.php");
}

if(isset($_POST['email']) && isset($_POST['password']) && !empty($_POST['password'])){
	$email = $_POST['email'];
	$pass = $_POST['password'];
	
	$users = $container->getUsers();
	$user = $users->login($email, $pass);
	if($user){
		//$_SESSION['user'] = $user;
		Utils::login($user);
		Utils::flash("Login successful!");
		header("Location: index.php?home");
	}
	else{
        Utils::flash("Failed to login");
    }
}
?>
<form name="loginform" id="myForm" method="POST">
	<div class="form-group">
		<label for="text">Email/Username: </label>
		<input class="form-control" type="text" id="email" name="email" placeholder="Enter Email/Username"/>
	</div>
	<div class="form-group">
		<label for="pass">Password: </label>
		<input class="form-control" type="password" id="pass" name="password" placeholder="Enter password"/>
	</div>
	<input class="btn btn-primary" type="submit" value="Login"/>
</form>
