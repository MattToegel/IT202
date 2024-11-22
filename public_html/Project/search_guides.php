<?php
require_once(__DIR__ . "/../../partials/nav.php");

//search before query
$title = se($_GET, "title", "", false);
$topic = se($_GET, "topic", "", false);

$sql = "SELECT * FROM SC_Guides
WHERE title like :title AND type = :topic
 LIMIT 10";
$db = getDB();
$results = [];
try {
    $stmt = $db->prepare($sql);
    $stmt->execute([":title" => "%$title%", ":topic" => $topic]);
    $r = $stmt->fetchAll();
    if ($r) {
        $results = $r;
    }
} catch (Exception $e) {
    error_log(var_export($e, true));
    error_log("fun happened");
    flash("Failed to fethc");
}
$table = ["data" => $results, "title" => "Guides"];
?>

<div class="container-fluid">
    <div>
        <form>
            <div class="row">
                <div class="col">
                    <?php render_input(["name" => "title", "label" => "Title", "value" => $title]); ?>
                </div>
                <div class="col">
                    <?php render_input(["name" => "topic", "label" => "Topic", "value" => $topic]); ?>
                </div>
                <div class="col">
                    <?php render_button(["text" => "Search", "type" => "submit"]); ?>
                </div>
            </div>
        </form>
    </div>
    <?php render_table($table); ?>

</div>

<?php
//note we need to go up 1 more directory
require_once(__DIR__ . "/../../partials/flash.php");
?>