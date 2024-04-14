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
    $broker = [
        "name" => "",
        "rarity" => rand(1, 10),
    ];
    $db = getDB();
    //fetch a name
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
    //insert data
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
    /*$query = "INSERT INTO `IT202-S24-Brokers` ";
    $columns = [];
    $params = [];
    //per record
    foreach ($broker as $k => $v) {
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

        error_log("Something broke with the query" . var_export($e, true));
        flash("An error occurred during insert", "danger");
    }*/
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