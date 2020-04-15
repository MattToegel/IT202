<?php
//TODO making it dynamically load boostrap if we're not using the routing sample
if (!isset($container)) {
    require("bootstrap.php");
}

?>
<a href="story.php">Create Story</a>