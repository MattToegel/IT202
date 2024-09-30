<?php
require(__DIR__."/../../partials/nav.php");
?>
<h1>Home</h1>
<?php
error_log("SESSION DATA: " . var_export($_SESSION, true));
if(isset($_SESSION["user"]) && isset($_SESSION["user"]["email"])){
 echo "Welcome, " . $_SESSION["user"]["email"]; 
}
else{
  echo "You're not logged in";
}
?>