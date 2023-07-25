<?php
$intent_types = get_intent_types();
$intent_statuses = get_intent_statuses();
$search = $_GET;
$VALID_INTENT_COLUMNS = ["processor_name", "intent_status", "intent_type", "cat_name", "modified"];
$intent_types = array_map(function ($v) {
    return ["label" => $v["label"], "value" => $v["id"]];
}, $intent_types);
array_unshift($intent_types, ["label" => "Any", "value" => ""]);

$intent_statuses = array_map(function ($v) {
    return ["label" => $v["label"], "value" => $v["id"]];
}, $intent_statuses);
array_unshift($intent_statuses, ["label" => "Any", "value" => ""]);

// make columns options for order by
//map order columns
$cols = array_map(function ($v) {
    return ["label" => str_replace("_", " ", $v), "value" => strtolower($v)];
}, $VALID_INTENT_COLUMNS); //$VALID_ORDER_COLUMNS is defined in cat_helpers.php
array_unshift($cols, ["label" => "Any", "value" => ""]);

$orders = ["asc", "desc"];
$orders = array_map(function ($v) {
    return ["label" => $v, "value" => strtolower($v)];
}, $orders);
array_unshift($orders, ["label" => "Any", "value" => ""]);

?>
<form method="GET">
    <div class="row">
        <div class="col-auto">
            <?php render_input(["type" => "select", "id" => "status", "name" => "status", "label" => "Status", "options" => $intent_statuses, "value" => se($search, "status", "", false)]); ?>
        </div>
        <div class="col-auto">
            <?php render_input(["type" => "select", "id" => "type", "name" => "type", "label" => "Type", "options" => $intent_types, "value" => se($search, "type", "", false)]); ?>
        </div>
        <div class="col-2">
            <?php render_input(["type" => "select", "id" => "column", "name" => "column", "label" => "Columns", "options" => $cols, "value" => se($search, "column", "", false)]); ?>
        </div>
        <div class="col-2">
            <?php render_input(["type" => "select", "id" => "order", "name" => "order", "label" => "Order", "options" => $orders, "value" => se($search, "order", "", false)]); ?>
        </div>
    </div>
    <div class="row">
        <div class="col-1">
            <?php render_button(["type" => "submit", "text" => "Search"]); ?>
        </div>
        <div class="col-1">
            <a class="btn btn-secondary" href="?">Reset</a>
        </div>
    </div>
</form>
<style>
    option {
        text-transform: capitalize;
    }
</style>