<?php
session_start();

echo "We found: " . var_export($_SESSION['loggedInUser'], true);

?>
