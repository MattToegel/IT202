<?php
require(__DIR__ . "/../../partials/nav.php");
$db = getDB();
//remove all associations
if (isset($_GET["remove"])) {
    $query = "DELETE FROM `IT202-S24-UserBrokers` WHERE user_id = :user_id";
    try {
        $stmt = $db->prepare($query);
        $stmt->execute([":user_id" => get_user_id()]);
        flash("Successfully removed all brokers", "success");
    } catch (PDOException $e) {
        error_log("Error removing broker associations: " . var_export($e, true));
        flash("Error removing broker associations", "danger");
    }

    redirect("my_brokers.php");
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
//error_log("Form data: " . var_export($form, true));

$total_records = get_total_count("`IT202-S24-Brokers` b
JOIN `IT202-S24-UserBrokers` ub ON b.id = ub.broker_id
WHERE user_id = :user_id", [":user_id" => get_user_id()]);


$query = "SELECT username, b.id, name, rarity, life, power, defense, stonks, user_id FROM `IT202-S24-Brokers` b
JOIN `IT202-S24-UserBrokers` ub ON b.id = ub.broker_id LEFT JOIN Users u ON u.id = ub.user_id
WHERE user_id = :user_id";
$params = [":user_id" => get_user_id()];
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
    //tell mysql I care about the data from table "b"
    if ($sort === "created" || $sort === "modified") {
        $sort = "b." . $sort;
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
    "view_url" => get_url("broker.php"),
];
?>
<div class="container-fluid">
    <h3>My Brokers</h3>
    <div>
        <a href="?remove" onclick="confirm('Are you sure')?'':event.preventDefault()" class="btn btn-danger">Remove All Brokers</a>
    </div>
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
    <?php render_result_counts(count($results), $total_records); ?>
    <div class="row w-100 row-cols-auto row-cols-sm-1 row-cols-md-2 row-cols-lg-3 row-cols-xl-4 row-cols-xxl-5 g-4">
        <?php foreach ($results as $broker) : ?>
            <div class="col">
                <?php render_broker_card($broker); ?>
            </div>
        <?php endforeach; ?>
        <?php if (count($results) === 0) : ?>
            <div class="col">
                No results to show
            </div>
        <?php endif; ?>
    </div>
</div>


<?php
require_once(__DIR__ . "/../../partials/flash.php");
?>