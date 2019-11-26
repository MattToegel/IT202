<html>
<head>
</head>
<body>
<?php
include_once("partials/header.php");
include_once("helpers/functions.php");
?>

<section>Welcome, <?php get_username();?>.
<?php if(is_admin()): ?>
	<h4>You're an admin, Harry</h4>
<?php endif;?>

</section>
<section>
<header>Items Due Soon</header>
<p>Item one</p>
<p>Item two</p>
<p>Item three</p>
<p>...</p>
</section>
</body>
</html>