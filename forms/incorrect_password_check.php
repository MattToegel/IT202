<?php
$password = "test";
$hash = password_hash($password, PASSWORD_BCRYPT);
$hash_compare = password_hash($password, PASSWORD_BCRYPT);
if($hash == $hash_compare){
  echo "Hashes match!";
}
else{
  echo "Hashes don't match";
}
echo "<br>Hash1: $hash<br>Hash2:$hash_compare<br>";
?>
