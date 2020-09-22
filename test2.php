<?php
//these lines attempt to configure error output for this script
//unfortunately it doesn't work on w3schools so you'll need to run the file on your own to see the output
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
//ignore this, it's just for output formatting
function newline(){
  //attempt to create newline for command line or browser, can ignore
  echo "<br>\n";
}
//echo $arr["test"];
$age = 21;//note the number that doesn't match a case
switch($age){
  case 21:
    echo "You have all the priviledges given at the legal age of 21";
    newline();
  case 18:
    echo "You have all the priviledges given at the legal age of 18";
    newline();
    break;
  //note the missing default case
}
?>
