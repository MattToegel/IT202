<?php
//note we need to go up 1 more directory
require(__DIR__ . "/../../../partials/nav.php");

if (!has_role("Admin")) {
    flash("You don't have permission to view this page", "warning");
    redirect("home.php");
}

//build search form
$form = [
    ["type" => "text", "name" => "name", "placeholder" => "Broker Name", "label" => "Broker Name", "include_margin" => false],

    ["type" => "number", "name" => "rarity_min", "placeholder" => "Min Rarity", "label" => "Min Rarity", "include_margin" => false],
    ["type" => "number", "name" => "rarity_max", "placeholder" => "Max Rarity", "label" => "Max Rarity", "include_margin" => false],

    ["type" => "number", "name" => "stonks_min", "placeholder" => "Min Stonks", "label" => "Min Stonks", "include_margin" => false],
    ["type" => "number", "name" => "stonks_max", "placeholder" => "Max Stonks", "label" => "Max Stonks", "include_margin" => false],

    ["type" => "select", "name" => "sort", "label" => "Sort", "options" => ["name" => "Name", "rarity" => "Rarity", "life" => "Life", "power" => "Power", "defense" => "Defense", "stonks" => "Stonks (Combat Effectiveness)", "created" => "Created", "modified" => "Modified"], "include_margin" => false],
    ["type" => "select", "name" => "order", "label" => "Order", "options" => ["asc" => "+", "desc" => "-"], "include_margin" => false],

    ["type" => "number", "name" => "limit", "label" => "Limit", "value" => "10", "include_margin" => false],
];
error_log("Form data: " . var_export($form, true));



$query = "SELECT id, name, rarity, life, power, defense, stonks FROM `IT202-S24-Brokers` WHERE 1=1";
$params = [];
$session_key = $_SERVER["SCRIPT_NAME"];
$is_clear = isset($_GET["clear"]);
if ($is_clear) {
    session_delete($session_key);
    unset($_GET["clear"]);
    redirect($session_key);
} else {
    $session_data = session_load($session_key);
}

if (count($_GET) == 0 && isset($session_data) && count($session_data) > 0) {
    if ($session_data) {
        $_GET = $session_data;
    }
}
if (count($_GET) > 0) {
    session_save($session_key, $_GET);
    $keys = array_keys($_GET);

    foreach ($form as $k => $v) {
        if (in_array($v["name"], $keys)) {
            $form[$k]["value"] = $_GET[$v["name"]];
        }
    }
    //name
    $name = se($_GET, "name", "", false);
    if (!empty($name)) {
        $query .= " AND name like :name";
        $params[":name"] = "%$name%";
    }
    //rarity range
    $rarity_min = se($_GET, "rarity_min", "-1", false);
    if (!empty($rarity_min) && $rarity_min > -1) {
        $query .= " AND rarity >= :rarity_min";
        $params[":rarity_min"] = $rarity_min;
    }
    $rarity_max = se($_GET, "rarity_max", "-1", false);
    if (!empty($rarity_max) && $rarity_max > -1) {
        $query .= " AND rarity <= :rarity_max";
        $params[":rarity_max"] = $rarity_max;
    }
    //stonks range
    $stonks_min = se($_GET, "stonks_min", "", false);
    if (!empty($stonks_min) && $stonks_min != "") {
        $query .= " AND stonks >= :stonks_min";
        $params[":stonks_min"] = $stonks_min;
    }
    $stonks_max = se($_GET, "stonks_max", "", false);
    if (!empty($stonks_max) && $stonks_max != "") {
        $query .= " AND stonks <= :stonks_max";
        $params[":stonks_max"] = $stonks_max;
    }

    //sort and order
    $sort = se($_GET, "sort", "created", false);
    if (!in_array($sort, ["name", "rarity", "life", "power", "defense", "stonks", "created", "modified"])) {
        $sort = "created";
    }
    $order = se($_GET, "order", "desc", false);
    if (!in_array($order, ["asc", "desc"])) {
        $order = "desc";
    }
    //IMPORTANT make sure you fully validate/trust $sort and $order (sql injection possibility)
    $query .= " ORDER BY $sort $order";
    //limit
    try {
        $limit = (int)se($_GET, "limit", "10", false);
    } catch (Exception $e) {
        $limit = 10;
    }
    if ($limit < 1 || $limit > 100) {
        $limit = 10;
    }
    //IMPORTANT make sure you fully validate/trust $limit (sql injection possibility)
    $query .= " LIMIT $limit";
}





$db = getDB();
$stmt = $db->prepare($query);
$results = [];
try {
    $stmt->execute($params);
    $r = $stmt->fetchAll();
    if ($r) {
        $results = $r;
    }
} catch (PDOException $e) {
    error_log("Error fetching stocks " . var_export($e, true));
    flash("Unhandled error occurred", "danger");
}
foreach ($results as $index => $broker) {
    foreach ($broker as $key => $value) {
        if (is_null($value)) {
            $results[$index][$key] = "N/A";
        }
    }
}

$table = [
    "data" => $results, "title" => "Brokers", "ignored_columns" => ["id"],
    "view_url" => get_url("admin/view_broker.php"),
    //"delete_url" => get_url("admin/delete_stock.php")
];
?>
<div class="container-fluid">
    <h3>List Brokers</h3>
    <form method="GET">
        <div class="row mb-3" style="align-items: flex-end;">

            <?php foreach ($form as $k => $v) : ?>
                <div class="col">
                    <?php render_input($v); ?>
                </div>
            <?php endforeach; ?>

        </div>
        <?php render_button(["text" => "Search", "type" => "submit", "text" => "Filter"]); ?>
        <a href="?clear" class="btn btn-secondary">Clear</a>
    </form>
    <?php render_table($table); ?>
</div>


<?php
//note we need to go up 1 more directory
require_once(__DIR__ . "/../../../partials/flash.php");
?>