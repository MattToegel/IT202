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
    $names = [];
    if ($action === "fetch") {
        $result = fetch_names();
        error_log("Data from API" . var_export($result, true));
        if ($result) {
            $names = $result;
        }
    }
    if (count($names) > 0) {
        $data = [];
        foreach ($names as $name) {
            array_push($data, ["name" => $name]);
        }
        //insert data
        try {
            //optional options for debugging and duplicate handling
            $opts =
                ["debug" => true, "update_duplicate" => true, "columns_to_update" => []];
            $result = insert("IT202-S24-Names", $data, $opts);
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
                flash("An entry for this name already exists", "warning");
            } else {
                error_log("Database error" . var_export($e2, true));
                flash("Database error", "danger");
            }
        } catch (Exception $e3) {
            error_log("Invalid data records" . var_export($e3, true));
            flash("Invalid data records", "danger");
        }
       /* $db = getDB();
        $query = "INSERT INTO `IT202-S24-Names` ";
        $query .= "(name) VALUES";
        $params = [];
        //per record
        foreach ($names as $index => $v) {
            $params[":name$index"] = $v;
            if ($index > 0) {
                $query .= ",";
            }
            $query .= "(:name$index)";
        }
        $query .= " ON DUPLICATE KEY UPDATE name = name";



        error_log("Query: " . $query);
        error_log("Params: " . var_export($params, true));
        try {
            $stmt = $db->prepare($query);
            $stmt->execute($params);
            flash("Inserted record " . $db->lastInsertId(), "success");
        } catch (PDOException $e) {
            if ($e->errorInfo[1] == 1062) {
                flash("Unhandled duplicate name occurred", "warning");
            } else {
                error_log("Something broke with the query" . var_export($e, true));
                flash("An error occurred", "danger");
            }
        }*/
    } else {
        flash("No names fetched", "warning");
    }
}

//TODO handle manual create stock
?>
<div class="container-fluid">
    <h3>Fetch Random Mixed Gendered Names</h3>

    <form method="POST">
        <?php render_input(["type" => "hidden", "name" => "action", "value" => "fetch"]); ?>
        <?php render_button(["text" => "Search", "type" => "submit",]); ?>
    </form>

</div>
<?php
//note we need to go up 1 more directory
require_once(__DIR__ . "/../../../partials/flash.php");
?>