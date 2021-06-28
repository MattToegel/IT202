<?php
require("nav.php");
//Note: this is a complex dynamic add form and is merely provided as an exploratory tool
//It lacks in many features and fine-tuning. Do not use this in your project.
//Extract the data yourselfs and customize your forms/data processing, etc.
$db = getDB();
//let's insert a new record if the form was submitted
if (isset($_POST["submit"])) {
    $query = "INSERT INTO Samples "; //TODO change the table name
    $columns = array_filter(array_keys($_POST), function ($x) {
        return $x !== "submit";
    });
    //arrow function uses fn and doesn't have return or { }
    //https://www.php.net/manual/en/functions.arrow.php
    $placeholders = array_map(fn ($x) => ":$x", $columns);
    $query .= "(" . join(",", $columns) . ") VALUES (" . join(",", $placeholders) . ")";
    //echo var_export($query, true);
    $params = [];
    foreach ($columns as $col) {
        $params[":$col"] = $_POST[$col];
    }
    //echo var_export($params, true);
    $stmt = $db->prepare($query);
    try {
        $stmt->execute($params);
        //https://www.php.net/manual/en/pdo.lastinsertid.php
        echo "Successfully added new record with id " . $db->lastInsertId();
    } catch (PDOException $e) {
        echo "<pre>" . var_export($e->errorInfo, true) . "</pre>";
    }
}


//using show columns let's get the table definition

$query = "SHOW COLUMNS from Samples"; //TODO change table name
$stmt = $db->prepare($query);
$results = [];
try {
    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "<pre>" . var_export($e, true) . "</pre>";
}
echo "<pre>" . var_export($results, true) . "</pre>";
?>
<h3>Create Sample</h3>
<form method="POST">
    <?php foreach ($results as $index => $column) : ?>
        <?php /* Lazily ignoring fields with default values to avoid capturing created/modified,
        also ignoring auto_increment to avoid the id since these are all fields we don't need to provide*/ ?>
        <?php if (!isset($column["Default"]) && $column["Extra"] !== "auto_increment") : ?>
            <label for="<?php se($column, "Field"); ?>"><?php se($column, "Field"); ?></label>
            <input id="<?php se($column, "Field"); ?>" type=" text" name="<?php se($column, "Field"); ?>" />
        <?php endif; ?>
    <?php endforeach; ?>
    <input type="submit" value="Create" name="submit" />
</form>
<details>
    <summary>Show Columns Output</summary>
    <?php echo "<pre>" . var_export($results, true) . "</pre>"; ?>
</details>
<style>
    input {
        width: 100%;
        display: inline-block;
    }

    label {
        text-transform: capitalize;
    }

    form {
        width: 30%;
        margin-left: auto;
        margin-right: auto;
    }
</style>