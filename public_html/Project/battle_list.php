<?php
require(__DIR__ . "/../../partials/nav.php");

is_logged_in(true);

$params = [":user_id" => get_user_id()];

$query = "SELECT u.username, b.id, name, rarity, stonks, user_id FROM `IT202-S24-Brokers` b
JOIN `IT202-S24-UserBrokers` ub ON b.id = ub.broker_id JOIN Users u on u.id = ub.user_id WHERE ub.user_id != :user_id AND stonks is not null ORDER BY RAND() LIMIT 10";

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
    "view_url" => get_url("broker_selection.php"), "view_label" => "Battle",
];
?>
<div class="container-fluid">
    <h3>Battle List</h3>
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