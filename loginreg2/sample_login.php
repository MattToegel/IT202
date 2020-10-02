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
  if(!strpos($email, "@")){
   $isValid = false;
    echo "<br>Invalid email<br>";
  }
  if($isValid){
    require_once(__DIR__."/../lib/db.php");
    $db = getDB();
	if(isset($db)){
		$stmt = $db->prepare("SELECT id, email, password from Users WHERE email = :email LIMIT 1");
		
		$params = array(":email"=>$email);
		$r = $stmt->execute($params);
		echo "db returned: " . var_export($r, true);
		$e = $stmt->errorInfo();
		if($e[0] != "00000"){
			echo "uh oh something went wrong: " . var_export($e, true);
		}
		$result = $stmt->fetch(PDO::FETCH_ASSOC);
		if($result && isset($result["password"])){
			$password_hash_from_db = $result["password"];
			if(password_verify($password, $password_hash_from_db)){
        session_start();//we only need to active session when it's worth activating it
        unset($result["password"]);//remove password so we don't leak it beyond this page
        //let's create a session for our user based on the other data we pulled from the table
        $_SESSION["user"] = $result;//we can save the entire result array since we removed password
        //on successful login let's serve-side redirect the user to the home page.
			  header("Location: home.php");
			}
			else{
			 echo "<br>Invalid password, get out!<br>"; 
			}
		}
		else{
			echo "<br>Invalid user<br>";
		}
	}
  }
  else{
   echo "There was a validation issue"; 
  }
}
?>
