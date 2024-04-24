<?php
require(__DIR__ . "/../../partials/nav.php");

?>

<?php
$id = se($_GET, "id", -1, false);


$broker = [];
if ($id > -1) {
    $db = getDB();
    $stocks = [];
    //fetch latest stock times
    $query = "SELECT IF(MAX(latest)<DATE(CONVERT_TZ(CURDATE(),'+00:00','-05:00')), 1, 0) as `needs_update`, symbol FROM `IT202-S24-Stocks` WHERE symbol in (SELECT symbol FROM `IT202-S24-Brokers` b WHERE b.id = :id) GROUP BY symbol LIMIT 5";
    try {
        $stmt = $db->prepare($query);
        $stmt->execute([":id" => $id]);
        $r = $stmt->fetchAll();
        if ($r) {
            $stocks = $r;
        }
    } catch (PDOException $e) {
        error_log("Error fetching latest stocks for broker: " . var_export($e, true));
    }
    $didUpdate = false;
    if ($stocks) {
        //fetch
        foreach ($stocks as $stock) {
            if ($stock["needs_update"]) {
                //fetch
                $symbol = $stock["symbol"];
                try {
                    $result = fetch_quote($symbol);
                    $result = insert("`IT202-S24-Stocks`", $result);
                    $didUpdate = true;
                    error_log("Update of $symbol: " . var_export($result, true));
                } catch (Exception $e) {
                    error_log("Error updating Symbol $symbol: " . var_export($e, true));
                }
            }
        }
    }
    if ($didUpdate) {
        recaculate_broker($id);
    }
    //fetch
    
    $query = "SELECT name, rarity, life, power, defense, stonks, created, modified FROM `IT202-S24-Brokers` WHERE id = :id";
    try {
        $stmt = $db->prepare($query);
        $stmt->execute([":id" => $id]);
        $r = $stmt->fetch();
        if ($r) {
            $broker = $r;
        }
    } catch (PDOException $e) {
        error_log("Error fetching record: " . var_export($e, true));
        flash("Error fetching record", "danger");
    }
} else {
    flash("Invalid id passed", "danger");
    redirect("brokers.php");
}
foreach ($broker as $key => $value) {
    if (is_null($value)) {
        $broker[$key] = "N/A";
    }
}
//TODO handle manual create stock
?>
<div class="container-fluid">
    <h3>Broker: <?php se($broker, "name", "Unknown"); ?></h3>
    <div>
        <a href="<?php echo get_url("brokers.php"); ?>" class="btn btn-secondary">Back</a>
    </div>

    <?php render_broker_card($broker); ?>

</div>


<?php
require_once(__DIR__ . "/../../partials/flash.php");
?>