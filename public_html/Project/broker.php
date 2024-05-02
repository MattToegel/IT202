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
    $query = "SELECT IF(MAX(latest)<DATE(CONVERT_TZ(CURDATE(),'+00:00','-05:00')), 1, 0) as `needs_update`, symbol FROM `IT202-S24-Stocks` 
    WHERE symbol in (SELECT symbol FROM `IT202-S24-Brokers` b JOIN `IT202-S24-Portfolios` p ON b.id = p.broker_id WHERE b.id = :id) GROUP BY symbol LIMIT 5";
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
    //TODO add a check if anything actually changed (not in this video 4-27-24)
    recaculate_broker($id);

    //fetch

    /*$query = "SELECT name, rarity, life, power, defense, stonks, created, modified FROM `IT202-S24-Brokers` WHERE id = :id";
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
    }*/
    $brokers = fetch_broker_data($id);
    if ($brokers && count($brokers) >= 1) {
        $broker = $brokers[0];
    }
    //fetch stocks
    $stocks = [];
    $query = "SELECT 
                    s.symbol, 
                    s.price,
                    s.per_change AS `change`, 
                    s.volume, 
                    p.shares,
                    (SELECT AVG(per_change) FROM `IT202-S24-Stocks` AS hist WHERE hist.symbol = s.symbol) AS `historical_change`
                    FROM 
                        `IT202-S24-Stocks` s
                    INNER JOIN 
                        (
                            SELECT symbol, MAX(latest) AS MaxDate
                            FROM `IT202-S24-Stocks`
                            GROUP BY symbol
                        ) AS latest
                    
                    ON s.symbol = latest.symbol AND s.latest = latest.MaxDate
                    JOIN `IT202-S24-Portfolios` p on p.symbol = s.symbol
                  WHERE p.broker_id = :broker_id";
    try {
        $stmt = $db->prepare($query);
        $stmt->execute([":broker_id" => $id]);
        $r = $stmt->fetchAll();
        if ($r) {
            $stocks = $r;
        }
    } catch (PDOException $e) {
        error_log("Error fetching stocks: " . var_export($e, true));
        flash("Error fetching broker stocks", "danger");
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
    <div class="list-group">
        <?php foreach ($stocks as $stock) : ?>
            <div class="list-group-item">

                <div class="row">
                    <div class="col">
                        Symbol: <?php se($stock, "symbol"); ?>
                    </div>
                    <div class="col">
                        Price: <?php se($stock, "price"); ?>
                    </div>
                    <div class="col">
                        Change: <?php se($stock, "change"); ?>
                    </div>
                    <div class="col">
                        Volume: <?php se($stock, "volume"); ?>
                    </div>
                    <div class="col">
                        Shares: <?php se($stock, "shares"); ?>
                        <form method="POST" action="<?php echo get_url("api/purchase_shares.php"); ?>">
                            <?php render_input(["type" => "number", "name" => "shares", "value" => 1]); ?>
                            <?php render_input(["type" => "hidden", "name" => "broker_id", "value" => $id]); ?>
                            <?php render_input(["type" => "hidden", "name" => "symbol", "value" => $stock["symbol"]]); ?>
                            <?php $cost = ceil(se($stock, "price", 0, false)); ?>
                            <?php render_button(["text" => "Purchase ($cost x shares)", "type" => "submit"]); ?>
                        </form>
                    </div>
                    <div class="col">
                        Historical Change: <?php se($stock, "historical_change"); ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>


<?php
require_once(__DIR__ . "/../../partials/flash.php");
?>