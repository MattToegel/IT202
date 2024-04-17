<?php
//note we need to go up 1 more directory
require(__DIR__ . "/../../../partials/nav.php");

if (!has_role("Admin")) {
    flash("You don't have permission to view this page", "warning");
    redirect("home.php");
}
?>

<?php

//TODO handle stock fetch
if (isset($_POST["action"])) {
    $action = $_POST["action"];
    $symbol =  strtoupper(se($_POST, "symbol", "", false));
    $quote = [];
    if ($symbol) {
        if ($action === "fetch") {
            $result = fetch_quote($symbol);
            error_log("Data from API" . var_export($result, true));
            if ($result) {
                $quote = $result;
                $quote["is_api"] = 1;
            }
        } else if ($action === "create") {
            foreach ($_POST as $k => $v) {
                if (!in_array($k, ["symbol", "open", "low", "high", "price", "previous", "per_change", "volume", "latest"])) {
                    unset($_POST[$k]);
                }
                $quote = $_POST;
                error_log("Cleaned up POST: " . var_export($quote, true));
            }
        }
    } else {
        flash("You must provide a symbol", "warning");
    }
    //insert data
    try {
        //optional options for debugging and duplicate handling
        $opts =
            ["debug" => true, "update_duplicate" => false, "columns_to_update" => []];
        $result = insert("IT202-S24-Stocks", $quote, $opts);
        if (!$result) {
            flash("Unhandled error", "warning");
        } else {
            flash("Created record with id " . var_export($result, true), "success");
        }
    } catch (InvalidArgumentException $e1) {
        error_log("Invalid arg" . var_export($e1, true));
        flash("Invalid data passed", "danger");
    } catch (PDOException $e2) {
        if ($e2->errorInfo[1] == 1062) {
            flash("An entry for this symbol already exists for today", "warning");
        } else {
            error_log("Database error" . var_export($e2, true));
            flash("Database error", "danger");
        }
    } catch (Exception $e3) {
        error_log("Invalid data records" . var_export($e3, true));
        flash("Invalid data records", "danger");
    }
    /*$db = getDB();
    $query = "INSERT INTO `IT202-S24-Stocks` ";
    $columns = [];
    $params = [];
    //per record
    foreach ($quote as $k => $v) {
        array_push($columns, "`$k`");
        $params[":$k"] = $v;
    }
    $query .= "(" . join(",", $columns) . ")";
    $query .= "VALUES (" . join(",", array_keys($params)) . ")";
    error_log("Query: " . $query);
    error_log("Params: " . var_export($params, true));
    try {
        $stmt = $db->prepare($query);
        $stmt->execute($params);
        flash("Inserted record " . $db->lastInsertId(), "success");
    } catch (PDOException $e) {
        if ($e->errorInfo[1] == 1062) {
            flash("A quote for the symbol and this date already exists, please try another or edit it", "warning");
        } else {
            error_log("Something broke with the query" . var_export($e, true));
            flash("An error occurred", "danger");
        }
    }*/
}

//TODO handle manual create stock
?>
<div class="container-fluid">
    <h3>Create or Fetch Stock</h3>
    <ul class="nav nav-tabs">
        <li class="nav-item">
            <a class="nav-link bg-success" href="#" onclick="switchTab('create')">Fetch</a>
        </li>
        <li class="nav-item">
            <a class="nav-link bg-success" href="#" onclick="switchTab('fetch')">Create</a>
        </li>
    </ul>
    <div id="fetch" class="tab-target">
        <form method="POST">
            <?php render_input(["type" => "search", "name" => "symbol", "placeholder" => "Stock Symbol", "rules" => ["required" => "required"]]); ?>
            <?php render_input(["type" => "hidden", "name" => "action", "value" => "fetch"]); ?>
            <?php render_button(["text" => "Search", "type" => "submit",]); ?>
        </form>
    </div>
    <div id="create" style="display: none;" class="tab-target">
        <form method="POST">

            <?php render_input(["type" => "text", "name" => "symbol", "placeholder" => "Stock Symbol", "label" => "Stock Symbol", "rules" => ["required" => "required"]]); ?>
            <?php render_input(["type" => "number", "name" => "open", "placeholder" => "Stock Open", "label" => "Stock Open", "rules" => ["required" => "required"]]); ?>
            <?php render_input(["type" => "number", "name" => "low", "placeholder" => "Stock Low", "label" => "Stock Low", "rules" => ["required" => "required"]]); ?>
            <?php render_input(["type" => "number", "name" => "high", "placeholder" => "Stock High", "label" => "Stock High", "rules" => ["required" => "required"]]); ?>
            <?php render_input(["type" => "number", "name" => "price", "placeholder" => "Stock Current Price", "label" => "Stock Current Price", "rules" => ["required" => "required"]]); ?>
            <?php render_input(["type" => "number", "name" => "previous", "placeholder" => "Stock Previous", "label" => "Stock Previous", "rules" => ["required" => "required"]]); ?>
            <?php render_input(["type" => "number", "name" => "per_change", "placeholder" => "Stock % change", "label" => "Stock % change", "rules" => ["required" => "required"]]); ?>
            <?php render_input(["type" => "number", "name" => "volume", "placeholder" => "Stock Volume", "label" => "Stock Volume", "rules" => ["required" => "required"]]); ?>
            <?php render_input(["type" => "date", "name" => "latest", "placeholder" => "Stock Date", "label" => "Stock Date", "rules" => ["required" => "required"]]); ?>

            <?php render_input(["type" => "hidden", "name" => "action", "value" => "create"]); ?>
            <?php render_button(["text" => "Search", "type" => "submit", "text" => "Create"]); ?>
        </form>
    </div>
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