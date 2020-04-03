<?php
require("config.php");

$configuration = array(
'db_cs'  => "mysql:host=$dbhost;dbname=$dbdatabase",
'db_user' => "$dbuser",
'db_pass' => "$dbpass",
);
require_once __DIR__ . '/lib/model/User.php';
require_once __DIR__ . '/lib/model/Role.php';
require_once __DIR__ . '/lib/model/Story.php';
require_once __DIR__ . '/lib/service/Container.php';
require_once __DIR__ . '/lib/service/Users.php';
require_once __DIR__ . '/lib/service/Stories.php';
require_once __DIR__ . '/helpers/functions.php';
session_start();


$container = new Container($configuration);
?>