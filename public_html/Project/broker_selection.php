<?php
require(__DIR__ . "/../../partials/nav.php");

is_logged_in(true);


$broker_id = -1;
try {
    $broker_id = (int)se($_GET, "id", -1, false);
} catch (Exception $e) {
}
if ($broker_id < 1) {
    flash("Invalid target", "danger");
    redirect("battle_list.php");
}
$_SESSION["target_id"] = $broker_id;
//TODO fetch my brokers
$params = [":user_id" => get_user_id()];
$query = "SELECT b.id, name, rarity, stonks FROM `IT202-S24-Brokers` b
JOIN `IT202-S24-UserBrokers` ub ON b.id = ub.broker_id WHERE ub.user_id = :user_id ORDER BY stonks desc LIMIT 10";





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
    "data" => $results, "title" => "Brokers", "ignored_columns" => ["id", "user_id"],
    "view_url" => get_url("battle.php"), "view_label" => "Select",
];
?>
<div class="container-fluid">
    <h3>Select Your Broker</h3>
    <div class="row w-75 g-4 mx-auto">
        <?php render_table($table); ?>
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