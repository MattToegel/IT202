<?php
// this file is part of an example of how we can persist query params
//note we need to go up 1 more directory
require(__DIR__ . "/../../../lib/functions.php");
// don't forget to start the session if you need it since this is done in nav.php and not functions.php
if (session_status() != PHP_SESSION_ACTIVE) {
    session_start();
}
if (!has_role("Admin")) {
    error_log("Doesn't have permission");
    flash("You don't have permission to view this page", "warning");
    //die(header("Location: " . get_url("home.php")));
    redirect("home.php");
}


$id = (int)se($_GET, "id", 0, false);
if ($id <= 0) {
    flash("Invalid cat", "danger");
} else {
    $db = getDB();
    $query = "UPDATE CA_Cats set status = 'unavailable' WHERE id = :id";
    $stmt = $db->prepare($query);
    try {
        $stmt->execute([":id" => $id]);
        flash("Successfully marked cat as unavailable", "success");
    } catch (PDOException $e) {
        flash("Error updating cat profile", "danger");
        error_log("Error setting cat as unavailable: " . var_export($e, true));
    }
}

if (isset($_SESSION["previous"]) && strpos($_SESSION["previous"], "admin") !== false) {
    $url = "admin/list_cats.php";
} else {
    $url = "browse.php";
}
$url .= "?" . http_build_query($_GET);
error_log("redirecting to " . var_export($url, true));
redirect(get_url($url));
