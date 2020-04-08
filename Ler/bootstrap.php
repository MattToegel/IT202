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
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous"
<script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>
