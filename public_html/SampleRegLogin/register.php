<form method="POST">
	<label for="email">Email
	<input type="email" id="email" name="email"/>
	</label>
	<label for="p">Password
	<input type="password" id="p" name="password"/>
	</label>
	<label for="cp">Confirm Password
	<input type="password" id="cp" name="cpassword"/>
	</label>
	<input type="submit" name="register" value="Register"/>
</form>

<?php
//echo var_export($_GET, true);
//echo var_export($_POST, true);
//echo var_export($_REQUEST, true);
if(isset($_POST["register"])){
	if(isset($_POST["password"]) && isset($_POST["cpassword"])){
		$password = $_POST["password"];
		$cpassword = $_POST["cpassword"];
		if($password == $cpassword){
			echo "<div>Passwords Match</div>";
		}
		else{
			echo "<div>Passwords don't match</div>";
		}
	}
}
?>