<?php
include_once(__DIR__."/partials/header.partial.php");
session_unset();
session_destroy();
//get session cookie and delete/clear it for this session
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    //clones then destroys since it makes it's lifetime
    //negative (in the past)
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}
Common::flash("You have successfully logged out");
die(header("Location: " . Common::url_for("login")));
?>