<?php
require("config.php");
session_start();
$configuration = array(
'db_cs'  => "mysql:host=$dbhost;dbname=$dbdatabase",
'db_user' => "$dbuser",
'db_pass' => "$dbpass",
);
require_once __DIR__ . '/lib/service/Container.php';
require_once __DIR__ . '/lib/service/Users.php';
require_once __DIR__ . '/lib/model/User.php';
require_once __DIR__ . '/lib/model/Role.php';

$container = new Container($configuration);
?>