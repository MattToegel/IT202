<?php
session_start();
?>
<html>
<head></head>
<body>
Hello there, <?php echo $_SESSION['user']['name'];?>
</body>
</html>
