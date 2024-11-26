<?php
require(__DIR__ . "/../../lib/functions.php");
session_start();
$id = se($_GET, "id", -1, false);

if ($id > 0) {
    $db = getDB();
    try {
        // if there are relationships, delete from child tables first
        // alternatively, during FOREIGN KEY creation would could have used cascade delete
        $stmt = $db->prepare("DELETE FROM SC_GuideProviders where guide_id = :id");
        $stmt->execute([":id" => $id]);
        $stmt = $db->prepare("DELETE FROM SC_GuideImages where guide_id = :id");
        $stmt->execute([":id" => $id]);
        $stmt = $db->prepare("DELETE FROM SC_GuideTopics where guide_id = :id");
        $stmt->execute([":id" => $id]);

        $stmt = $db->prepare("DELETE FROM SC_Guides WHERE id = :id");
        $stmt->execute([":id" => $id]);
        flash("Delete successful", "success");
    } catch (PDOException $e) {
        error_log("Error deleting: " . var_export($e, true));
        flash("There was an error deleting the record", "danger");
    }
}
unset($_GET["id"]);
$loc = get_url("search_guides.php")."?" . http_build_query($_GET);
error_log("Location: $loc");
die(header("Location: $loc"));
