<?php
require_once (__DIR__."/../includes/common.inc.php");
?>
<nav>
    <ul>
        <li>
            <a href="<?php echo Common::url_for("home");?>">Home</a>
        </li>
        <li>
            <a href="<?php echo Common::url_for("login");?>">Login</a>
        </li>
        <li>
            <a href="<?php echo Common::url_for("register");?>">Register</a>
        </li>
        <li>
            <a href="<?php echo Common::url_for("logout");?>">Logout</a>
        </li>
    </ul>
</nav>
