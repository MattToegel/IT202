<?php
require(__DIR__ . "/../../partials/nav.php");

is_logged_in(true);


$target_id = -1;
try {
    $target_id = (int)se($_SESSION, "target_id", -1, false);
    unset($_SESSION["target_id"]);
} catch (Exception $e) {
}
if ($target_id < 1) {
    flash("Invalid target", "danger");
    redirect("battle_list.php");
}
$my_broker = -1;
try {
    $my_broker = (int)se($_GET, "id", -1, false);
} catch (Exception $e) {
};
if ($my_broker < 1) {
    flash("Invalid selected broker", "danger");
    redirect("battle_list.php");
}
$attacker = [];
$defender = [];
//TODO fetch my brokers
$params = [":target" => $target_id, ":mine" => $my_broker];
//query is just going to check broker_ids, ideally you'll want to verify ownership of both
$query = "SELECT b.id, name, rarity, life, defense, power, stonks, user_id FROM `IT202-S24-Brokers` b
JOIN `IT202-S24-UserBrokers` ub ON b.id = ub.broker_id WHERE b.id in (:target, :mine) LIMIT 2";

$db = getDB();
$stmt = $db->prepare($query);
$results = [];
try {
    $stmt->execute($params);
    $r = $stmt->fetchAll();
    if ($r) {
        $results = $r;
        if ($results[0]["user_id"] == get_user_id()) {
            $attacker = $results[0];
            $defender = $results[1];
        } else {
            $attacker = $results[1];
            $defender = $results[0];
        }
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
$battle_uuid = uniqid();
$events = battle($attacker, $defender, $battle_uuid);
$result = insert("`IT202-S24-BattleEvents`", $events);
$end = $events[count($events) - 1];
$entry = ["user_id" => get_user_id()];
if ($end) {
    $points = 0;
    if ($end["broker1_life"] <= 0) {
        //lost
        $points = -5;
        $entry["point_change"] = $points;
    } else if ($end["broker2_life"] <= 0) {
        //won
        $points = 10;
        $entry["point_change"] = $points;
    }
    $result = insert("`IT202-S24-Points`", $entry);
    if ($result["lastInsertId"] > 0) {
        $msg = "You " . ($points > 0 ? "won" : "lost") . " $points points";
        flash($msg, "info");
    }
}

$results = [];

$query = "SELECT be.id, action, b1.name as attacker, b2.name as defender, broker1_life as attacker_life, broker2_life as defender_life, broker1_dmg as attacker_damage, broker2_dmg as defender_damage, round
FROM `IT202-S24-BattleEvents` be JOIN `IT202-S24-Brokers` b1 ON b1.id = be.broker1_id JOIN `IT202-S24-Brokers` b2 ON b2.id = be.broker2_id WHERE battle_uuid = :bid
ORDER BY round asc";
$db = getDB();
try {
    $stmt = $db->prepare($query);
    $stmt->execute([":bid" => $battle_uuid]);
    $r = $stmt->fetchAll();
    if ($r) {
        $results =  $r;
    }
} catch (PDOException $e) {
    error_log("Error fetching battle data: " . var_export($e, true));
    flash("Error handling battle", "danger");
}
$table = [
    "data" => $results, "title" => "Battle Events", "ignored_columns" => [],
];
?>
<div class="container-fluid">
    <h3>Battle Results</h3>
    <a class="btn btn-secondary" href="<?php echo get_url("battle_list.php"); ?>">Back</a>
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