<?php require_once(__DIR__ . "/partials/nav.php"); ?>
<?php
//we use this to safely get the email to display
if(!is_logged_in()){
 die(header("Location: login.php"));
}
$email = "";
if (isset($_SESSION["user"]) && isset($_SESSION["user"]["email"])) {
    $email = $_SESSION["user"]["email"];
}
?>
<p>Welcome, <?php echo $email; ?></p>
<?php require(__DIR__ . "/partials/flash.php");