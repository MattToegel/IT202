<?php
require_once(__DIR__ . "/../../../partials/nav.php");
if (!has_role("Admin")) {
    flash("You don't have permission to view this page", "warning");
    redirect("home.php");
}
$user_id = get_user_id();

$intents = search_intents(processor_id: $user_id);
if (count($intents) > 0) {
    $headers = array_keys($intents[0]);
    $headers = array_map(function ($v) {
        return str_replace("_", " ", $v);
    }, $headers);
    $headers = join(",", $headers);
}
$table = ["data" => $intents, "header_override" => $headers, "view_url" => "admin/request.php"];

?>
<div class="container-fluid">
    <h4>My Requests</h4>
    <div>
        <?php include(__DIR__ . "/../../../partials/intent_search_form.php");
        ?>
    </div>
    <div>
        <?php render_table($table); ?>
    </div>
    <div class="row">
        <?php include(__DIR__ . "/../../../partials/pagination_nav.php"); ?>
    </div>
</div>
<?php
require_once(__DIR__ . "/../../../partials/footer.php");
?>