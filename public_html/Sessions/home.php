<?php
session_start();
echo "Welcome, " . $_SESSION["user"]["email"];
?>
<a href="logout.php">Logout</a>