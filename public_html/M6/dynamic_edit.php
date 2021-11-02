<?php
require("nav.php");
//Note: this is a complex dynamic edit form and is merely provided as an exploratory tool
//It lacks in many features and fine-tuning. Do not use this in your project.
//Extract the data yourselves and customize your forms/data processing, etc.

$db = getDB();
//check if there's a change (remember, save the change, then select the fresh data, if this is swapped you'll get stale data)
if (isset($_POST["submit"])) {
    $columns = array_keys($_POST);
    foreach ($columns as $index => $value) {
        //Note: normally it's bad practice to remove array elements during iteration

        //remove id, we'll use this for the WHERE not for the SET
        //remove submit, it's likely not in your table
        if ($value === "id" || $value === "submit") {
            unset($columns[$index]);
        }
    }
    // echo "<pre>" . var_export($columns, true) . "</pre>";
    $query = "UPDATE Samples SET "; //TODO change table name
    $total = count($columns);
    foreach ($columns as $index => $col) {
        $query .= "$col = :$col";
        if ($index < $total) {
            $query .= ", ";
        }
    }
    $query .= " WHERE id = :id";

    $params = [":id" => $_GET["id"]];
    foreach ($columns as $col) {
        $params[":$col"] = se($_POST, $col, "", false);
    }
    echo var_export($query, true);
    echo "<br>";
    echo var_export($params, true);
    echo "<br>";
    $stmt = $db->prepare($query);
    try {
        $stmt->execute($params);
        echo "Successfully updated record";
    } catch (PDOException $e) {
        echo "<pre>" . var_export($e->errorInfo, true) . "</pre>";
    }
}


//select the id and only editable fields
$query = "SELECT id, message from Samples where id = :id"; //TODO change table name and selected columns to edit (include id if using post value)
$id = se($_GET, "id", -1, false);
if ($id === -1) {
    echo 'You must pass a query parameter ($_GET) of the id you want to work with.';
    return;
}
$stmt = $db->prepare($query);
$result = [];
try {
    $stmt->execute([":id" => $id]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "<pre>" . var_export($e, true) . "</pre>";
}
?>
<h3>Edit Sample</h3>
<form method="post">
    <?php if (!$result) : ?>
        <p>No record found</p>
    <?php else : ?>
        <?php foreach ($result as $column => $value) : ?>
            <?php if ($column === "id") : ?>
                <?php /* we have a choice here. We can either make a hidden 
            field so id is in $_POST, or we can use the $_GET variable defined in the url that was used for the lookup */ ?>
                <input type="hidden" name="id" value="<?php se($value); ?>" />
            <?php else : ?>
                <label for="<?php se($column); ?>"><?php se($column); ?></label>
                <input id="<?php se($column); ?>" type="text" name="<?php se($column); ?>" value="<?php se($value); ?>" />
            <?php endif; ?>
        <?php endforeach; ?>
        <input type="submit" value="Save" name="submit" />
    <?php endif; ?>
</form>