<?php
//note we need to go up 1 more directory
require(__DIR__ . "/../../../partials/nav.php");

if (!has_role("Admin")) {
    flash("You don't have permission to view this page", "warning");
    die(header("Location: $BASE_PATH" . "/home.php"));
}
?>

<?php

//TODO handle stock fetch
if (isset($_POST["action"])) {
    $action = $_POST["action"];
    if ($action === "create") {
        foreach ($_POST as $k => $v) {
            if (!in_array($k, ["symbol", "open", "low", "high", "price", "previous", "per_change", "volume", "latest"])) {
                unset($_POST[$k]);
            }
            $quote = $_POST;
            error_log("Cleaned up POST: " . var_export($quote, true));
        }
    }
    //Step 1: begin broker data generation
    $broker = [
        "name" => "",
        "rarity" => rand(1, 10),
    ];
    $db = getDB();
    //Step 2: fetch a random name
    $query = "SELECT name FROM `IT202-S24-Names` ORDER BY RAND() LIMIT 1";
    try {
        $stmt = $db->prepare($query);
        $stmt->execute();
        $r = $stmt->fetch();
        if ($r) {
            error_log("Fetched name " . var_export($r, true));
            $broker["name"] = $r["name"];
        } else {
            flash("Didn't find any saved names", "danger");
        }
    } catch (PDOException $e) {
        error_log("Something broke with the select query" . var_export($e, true));
        flash("An error occurred", "danger");
    }
    //Step 3: insert base broker data
    try {
        $result = insert("IT202-S24-Brokers", $broker);
        if (!$result) {
            flash("Unhandled error", "warning");
        } else {
            flash("Created record(s)" . var_export($result, true), "success");
        }
    } catch (InvalidArgumentException $e1) {
        error_log("Invalid arg" . var_export($e1, true));
        flash("Invalid data passed", "danger");
    } catch (PDOException $e2) {
        if (
            $e2->errorInfo[1] == 1062
        ) {
            flash("An entry for this already exists", "warning");
        } else {
            error_log("Database error" . var_export($e2, true));
            flash("Database error", "danger");
        }
    } catch (Exception $e3) {
        error_log("Invalid data records" . var_export($e3, true));
        flash("Invalid data records", "danger");
    }
    if (isset($result) && isset($result["lastInsertId"])) {
        //Step 4: fetch random unique symbols from stocks based on rarity
        $stocks = [];
        $limit = $broker["rarity"];
        $query = "SELECT DISTINCT symbol FROM `IT202-S24-Stocks` ORDER BY RAND() LIMIT $limit";
        try {
            $stmt = $db->prepare($query);
            $stmt->execute();
            $r = $stmt->fetchAll();
            if ($r) {
                error_log("Fetched stocks " . var_export($r, true));
                $stocks = $r;
            } else {
                flash("Didn't find any saved names", "danger");
            }
        } catch (PDOException $e) {
            error_log("Something broke with the select query" . var_export($e, true));
            flash("An error occurred", "danger");
        }
        if ($stocks) {
            //Step 5: insert fetched stock symbols into portfolio for broker
            $broker_id = $result["lastInsertId"];
            foreach ($stocks as $index => $stock) {
                $stocks[$index]["broker_id"] = $broker_id;
            }
            error_log("Stock data: " . var_export($stocks, true));
            try {
                $result = insert("IT202-S24-Portfolios", $stocks);
                if ($result) {
                    flash("Associated stocks to broker $broker_id " . var_export($result, true), "success");
                }
            } catch (Exception $e) {
                error_log("Error associating stocks" . var_export($e, true));
                flash("Error adding stocks to generated broker", "danger");
            }
            //Step 6: fetch stock data (details)
            $query = "SELECT 
                    s.symbol, 
                    s.per_change AS `change`, 
                    s.volume, 
                    b.shares,
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
                    JOIN `IT202-S24-Portfolios` b on b.symbol = s.symbol
                  WHERE b.broker_id = $broker_id AND s.symbol IN ";
            $placeholders = str_repeat(",?", count($stocks));
            $placeholders = substr($placeholders, 1);
            $query .= "($placeholders)";
            $query .= "   ORDER BY s.latest DESC";
            $stockData = [];
            try {
                $symbols = [];
                foreach ($stocks as $index => $stock) {
                    array_push($symbols, $stock["symbol"]);
                }
                $stmt = $db->prepare($query);
                $stmt->execute($symbols);
                $stockData = $stmt->fetchAll();
                flash("Fetched stocks", "success");
                error_log("stocks: " . var_export($stockData, true));
            } catch (PDOException $e) {
                flash("Error fetching stocks", "danger");
                error_log("Error fetching stocks" . var_export($e, true));
            }

            if ($stockData) {
                //Step 7: calculate broker stats from stocks
                $broker["stocks"] = $stockData;
                $br = calculate_broker_stats($broker);
                error_log("br: " . var_export($br, true));
                //Step 8: update broker with calculated stats (Note: This will be recalculated on another page in the future)
                $query = "UPDATE `IT202-S24-Brokers` set life = :life, defense = :defense, power = :power, stonks = :stonks WHERE id = :id";
                try {
                    $stmt = $db->prepare($query);
                    $params = [];
                    foreach ($br["stats"] as $k => $v) {
                        $params[":$k"] = $v;
                    }
                    $params[":id"] = $broker_id;
                    $stmt->execute($params);
                    flash("Updated broker: " . var_export($br["stats"], true));
                    //finally done
                } catch (PDOException $e) {
                    error_log("Error updating broker " . var_export($e, true));
                }
            }
        }
    }
}

//TODO handle manual create stock
?>
<div class="container-fluid">
    <h3>Create Broker</h3>
    <form method="POST">
        <?php render_input(["type" => "hidden", "name" => "action", "value" => "create"]); ?>
        <?php render_button(["text" => "Search", "type" => "submit", "text" => "Create"]); ?>
    </form>
</div>
<script>
    function switchTab(tab) {
        let target = document.getElementById(tab);
        if (target) {
            let eles = document.getElementsByClassName("tab-target");
            for (let ele of eles) {
                ele.style.display = (ele.id === tab) ? "none" : "block";
            }
        }
    }
</script>

<?php
//note we need to go up 1 more directory
require_once(__DIR__ . "/../../../partials/flash.php");
?>