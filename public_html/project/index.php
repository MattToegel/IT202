<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
include_once(__DIR__."/partials/header.partial.php");
?>
<div class="container-fluid">
<div class="jumbotron">
    <h1 class="display-4">Welcome to <u>Tanks For Playing!</u></h1>
    <p class="lead">This is a sample project by Matt Toegel for his IT202 Summer class.</p>
    <hr class="my-4">
    <div class="list-group">
        <div class="list-group-item">
            <p>This is a 'simple' tank game project.</p>
        </div>
        <div class="list-group-item">
            <p>Each player gets a tank they can level up via the shop.</p>
        </div>
        <div class="list-group-item">
            <p>Players compete with a dynamic AI tank to progress.</p>
        </div>
        <div class="list-group-item">
            <p>Various activities award points that can be spent in the shop.</p>
        </div>
        <div class="list-group-item">
            <p>Register for your account today and get 10 points as a welcome bonus!</p>
        </div>
    </div>
    <a class="btn btn-primary btn-lg" href="register.php" role="button">Join the Fun!</a>
</div>
</div>