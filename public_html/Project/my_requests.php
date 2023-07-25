<?php
require_once(__DIR__ . "/../../partials/nav.php");
is_logged_in(true); //login guard 
$user_id = get_user_id();

$intents = search_intents(requestor_id: $user_id);
if (count($intents) > 0) {
    $headers = array_keys($intents[0]);
    $headers = array_map(function ($v) {
        return str_replace("_", " ", $v);
    }, $headers);
    $headers = join(",", $headers);
}
$table = ["data" => $intents, "header_override" => $headers];

?>
<div class="container-fluid">
    <h4>My Requests</h4>
    <div>
        <?php include(__DIR__ . "/../../partials/intent_search_form.php");
        ?>
    </div>
    <div>
        <?php render_table($table); ?>
    </div>
    <div class="row">
        <?php include(__DIR__ . "/../../partials/pagination_nav.php"); ?>
    </div>
</div>
<?php
require_once(__DIR__ . "/../../partials/footer.php");
?>