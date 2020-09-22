<p>Run me in the browser from your server to try</p>
<form method="POST">
  <label for="email">Email:</label>
  <input type="email" id="email" name="email" required/>
  <label for="p1">Password:</label>
  <input type="password" id="p1" name="password" required/>
  <input type="submit" name="login" value="Login"/>
</form>

<?php
if(isset($_POST["login"])){
  $email = null;
  $password = null;
  if(isset($_POST["email"])){
    $email = $_POST["email"];
  }
  if(isset($_POST["password"])){
    $password = $_POST["password"];
  }
  $isValid = true;
  if(!isset($email) || !isset($password)){
   $isValid = false; 
  }
  //TODO other validation as desired, remember this is the last line of defense
  //here you'd probably want some email validation, for sake of example let's do a super basic one
  if(!strpos($email, "@")){
   $isValid = false;
    echo "<br>Invalid email<br>";
  }
	echo $password;
  if($isValid){
    //for password matching, we can't use this, every time it's ran it'll be a different value
    //so will never log us in!
    //$hash = password_hash($password, PASSWORD_BCRYPT);
    //instead we'll want to run password_verify
    //TODO pretend we got our use from the DB
    $password_hash_from_db = '$2y$10$nyogxGqrfQYEg8mG4nnHJ./t/na9m3HHePyVy5yegJ2zJRQ23PDEm';//placeholder, you can copy/paste a hash generated from sample_reg.php if you want to test it
    //otherwise it'll always be false
    
    //note it's raw password, saved hash as the parameters
    if(password_verify($password, $password_hash_from_db)){
     echo "<br>Welcome! You're logged in!<br>"; 
    }
    else{
     echo "<br>Invalid password, get out!<br>"; 
    }
  }
  else{
   echo "There was a validation issue"; 
  }
}
?>
