<?php
// make status options
$statuses = ["Adopted", "Fostered", "Unavailable", "Available"];
if (!has_role("Admin")) {
    $statuses = array_filter($statuses, function ($v) {
        return $v !== "Unavailable";
    });
}
$statuses = array_map(function ($v) {
    return ["label" => $v, "value" => strtolower($v)];
}, $statuses);
array_unshift($statuses, ["label" => "Any", "value" => ""]);

// make breed options
$result = get_breeds();
// convert breed data to render_input's expected "options" data
$breeds = array_map(function ($v) {
    return ["label" => $v["name"], "value" => $v["id"]];
}, $result);
array_unshift($breeds, ["label" => "Any", "value" => ""]);

// make sex options
$sex = [
    ["label" => "Any", "value" => ""],
    ["label" => "Male", "value" => "m"],
    ["label" => "Female", "value" => "f"]
];

// make fixed options
$fixed = [
    ["label" => "Any", "value" => ""],
    ["label" => "Fixed", "value" => "1"],
    ["label" => "Not-fixed", "value" => "0"]
];

// make temperament options
$temps = get_temperaments();
$temps = array_map(function ($v) {
    return ["label" => $v["name"], "value" => $v["id"]];
}, $temps);
array_unshift($temps, ["label" => "Any", "value" => ""]);

// make columns options for order by
//map order columns
$cols = array_map(function ($v) {
    return ["label" => $v, "value" => strtolower($v)];
}, $VALID_ORDER_COLUMNS); //$VALID_ORDER_COLUMNS is defined in cat_helpers.php
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
            <?php render_input(["type" => "text", "id" => "name", "name" => "name", "label" => "Name", "value" => se($search, "name", "", false)]); ?>
        </div>
        <div class="col-auto">
            <?php render_input(["type" => "select", "id" => "status", "name" => "status", "label" => "Status", "options" => $statuses, "value" => se($search, "status", "", false)]); ?>
        </div>
        <div class="col-auto">
            <?php render_input(["type" => "select", "id" => "breed", "name" => "breed_id", "label" => "Breed", "options" => $breeds, "value" => se($search, "breed_id", "", false)]); ?>
        </div>
        <div class="col-auto">
            <?php render_input(["type" => "select", "id" => "temperament", "name" => "temperament[]", "label" => "Temperament", "options" => $temps, "rules" => ["multiple" => true], "value" => isset($search["temperament"]) ? $search["temperament"] : []]); ?>
        </div>
        <div class="col-auto">
            <?php render_input(["type" => "select", "id" => "sex", "name" => "sex", "label" => "Sex", "options" => $sex, "value" => se($search, "sex", "", false)]); ?>
        </div>
        <div class="col-auto">
            <?php render_input(["type" => "select", "id" => "fixed", "name" => "fixed", "label" => "Fixed (spayed/neutered)", "options" => $fixed, "value" => se($search, "fixed", "", false)]); ?>
        </div>
        <div class="col-2">
            <?php render_input(["type" => "number", "id" => "age_min", "name" => "age_min", "label" => "Age (min)", "value" => se($search, "age_min", "", false)]); ?>
        </div>
        <div class="col-2">
            <?php render_input(["type" => "number", "id" => "age_max", "name" => "age_max", "label" => "Age (max)", "value" => se($search, "age_max", "", false)]); ?>
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