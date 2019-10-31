<?php
session_start();//this should be the first call on the page that's using sessions


$_SESSION['loggedInUser'] = array('userId'=>1, 'userName'=>'Bill');

echo "Saved user: " . var_export($_SESSION['loggedInUser'], true);

?>
