<?php
//note we need to go up 1 more directory
require(__DIR__ . "/../../../partials/nav.php");

if (!has_role("Admin")) {
    flash("You don't have permission to view this page", "warning");
    redirect("home.php");
}

//build search form
$form = [
    ["type" => "text", "name" => "symbol", "placeholder" => "Stock Symbol", "label" => "Stock Symbol", "include_margin" => false],

    ["type" => "number", "name" => "price_min", "placeholder" => "Min Price", "label" => "Min Price", "include_margin" => false],
    ["type" => "number", "name" => "price_max", "placeholder" => "Max Price", "label" => "Max Price", "include_margin" => false],

    ["type" => "number", "name" => "per_change_min", "placeholder" => "% Change Min", "label" => "% Change Min", "include_margin" => false],
    ["type" => "number", "name" => "per_change_max", "placeholder" => "% Change Max", "label" => "% Change Max", "include_margin" => false],

    ["type" => "number", "name" => "volume_min", "placeholder" => "Min Volume", "label" => "Min Volume", "include_margin" => false],
    ["type" => "number", "name" => "volume_max", "placeholder" => "Max Volume", "label" => "Max Volume", "include_margin" => false],

    ["type" => "date", "name" => "date_min", "placeholder" => "Min Date", "label" => "Min Date", "include_margin" => false],
    ["type" => "date", "name" => "date_max", "placeholder" => "Max Date", "label" => "Max Date", "include_margin" => false],

    ["type" => "select", "name" => "sort", "label" => "Sort", "options" => ["price" => "Price", "per_change" => "Percent", "latest" => "Date", "volume" => "Volume"], "include_margin" => false],
    ["type" => "select", "name" => "order", "label" => "Order", "options" => ["asc" => "+", "desc" => "-"], "include_margin" => false],

    ["type" => "number", "name" => "limit", "label" => "Limit", "value" => "10", "include_margin" => false],
];
error_log("Form data: " . var_export($form, true));



$query = "SELECT id, symbol, open, low, high, price, per_change, latest, volume, is_api FROM `IT202-S24-Stocks` WHERE 1=1";
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
    //symbol
    $symbol = se($_GET, "symbol", "", false);
    if (!empty($symbol)) {
        $query .= " AND symbol like :symbol";
        $params[":symbol"] = "%$symbol%";
    }
    //price range
    $price_min = se($_GET, "price_min", "-1", false);
    if (!empty($price_min) && $price_min > -1) {
        $query .= " AND price >= :price_min";
        $params[":price_min"] = $price_min;
    }
    $price_max = se($_GET, "price_max", "-1", false);
    if (!empty($price_max) && $price_max > -1) {
        $query .= " AND price <= :price_max";
        $params[":price_max"] = $price_max;
    }
    //percent range
    $per_change_min = se($_GET, "per_change_min", "", false);
    if (!empty($per_change_min) && $per_change_min != "") {
        $query .= " AND per_change >= :per_change_min";
        $params[":per_change_min"] = $per_change_min;
    }
    $per_change_max = se($_GET, "per_change_max", "", false);
    if (!empty($per_change_max) && $per_change_max != "") {
        $query .= " AND per_change <= :per_change_max";
        $params[":per_change_max"] = $per_change_max;
    }
    //volume range
    $volume_min = se($_GET, "volume_min", "-1", false);
    if (!empty($volume_min) && $volume_min > -1) {
        $query .= " AND volume >= :volume_min";
        $params[":volume_min"] = $volume_min;
    }
    $volume_max = se($_GET, "volume_max", "-1", false);
    if (!empty($volume_max) && $volume_max > -1) {
        $query .= " AND per_change <= :volume_max";
        $params[":volume_max"] = $volume_max;
    }
    //date range
    $date_min = se($_GET, "date_min", "", false);
    if (!empty($date_min) && $date_min != "") {
        $query .= " AND latest >= :date_min";
        $params[":date_min"] = $date_min;
    }
    $date_max = se($_GET, "date_max", "-1", false);
    if (!empty($date_max) && $date_max > -1) {
        $query .= " AND latest <= :date_max";
        $params[":date_max"] = $date_max;
    }
    //sort and order
    $sort = se($_GET, "sort", "date", false);
    if (!in_array($sort, ["price", "per_change", "latest", "volume"])) {
        $sort = "date";
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





$query = "SELECT id, symbol, open, low, high, price, per_change, latest, volume, api_id FROM `IT202-S24-Stocks` ORDER BY created DESC LIMIT 25";
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

$table = [
    "data" => $results, "title" => "Latest Stocks", "ignored_columns" => ["id"],
    "edit_url" => get_url("admin/edit_stock.php"),
    "delete_url" => get_url("admin/delete_stock.php")
];
?>
<div class="container-fluid">
    <h3>List Stocks</h3>
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