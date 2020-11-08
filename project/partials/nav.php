<link rel="stylesheet" href="static/css/styles.css">
<?php
//we'll be including this on most/all pages so it's a good place to include anything else we want on those pages
require_once(__DIR__ . "/../lib/helpers.php");
?>
<nav>
    <ul class="nav">
        <li><a href="home.php">Home</a></li>
        <?php if (!is_logged_in()): ?>
            <li><a href="login.php">Login</a></li>
            <li><a href="register.php">Register</a></li>
        <?php endif; ?>
        <?php if (has_role("Admin")): ?>
            <li><a href="<?php echo getURL("test_create_egg.php"); ?>">Create Egg</a></li>
            <li><a href="<?php echo getURL("test_list_egg.php"); ?>">View Eggs</a></li>
            <li><a href="<?php echo getURL("test_create_incubator.php"); ?>">Create Incubator</a></li>
            <li><a href="<?php echo getURL("test_list_incrubators.php"); ?>">View Incubator</a></li>
        <?php endif; ?>
        <?php if (is_logged_in()): ?>
            <li><a href="profile.php">Profile</a></li>
            <li><a href="logout.php">Logout</a></li>
        <?php endif; ?>
    </ul>
</nav>