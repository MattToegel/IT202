<?php
session_start();
echo "Session Data <br>";
echo "<pre>" . var_export($_SESSION, true) . "</pre>";
echo "Cookie Data <br>";
echo "<pre>" . var_export($_COOKIE, true) . "</pre>";
?>
