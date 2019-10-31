<?php
session_start();

session_unset();
session_destroy();
echo "It's all gone!";
echo var_export($_SESSION, true);
?>
