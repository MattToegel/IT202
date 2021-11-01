<?php
require(__DIR__ . "/../../partials/nav.php");
?>
<h1>Home</h1>
<?php
if (is_logged_in()) {
    echo "Welcome, " . get_user_email();
} else {
    echo "You're not logged in";
}
//shows session info
echo "<pre>" . var_export($_SESSION, true) . "</pre>";
?>