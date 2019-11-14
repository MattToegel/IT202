<html>
<head>
<style>
.nav{padding:1%;}
</style>
</head>
<body>
<?php
include_once("partials/header.php");
include_once("helpers/functions.php");
?>

<section>Welcome, <?php get_username();?>.</section>
<section>
<header>Items Due Soon</header>
<p>Item one</p>
<p>Item two</p>
<p>Item three</p>
<p>...</p>
</section>
</body>
</html>