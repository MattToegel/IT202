<?php
require_once(__DIR__ . "/../../partials/nav.php");
if (!has_role("Admin")) {
    flash("You don't have permission to access this page", "danger");

    die(header("Location: " . $BASE_PATH));
}
?>
<?php
$form = [
    ["id" => "name", "name" => "name", "type" => "text", "required" => true],
    ["id" => "description", "name" => "description", "type" => "textarea", "required" => true],
    ["id" => "stock", "name" => "stock", "type" => "number", "required" => true],
    ["id" => "cost", "name" => "cost", "type" => "number", "required" => true],
];
?>
<?php

//TODO handle submit
//another way to check if a form was submitted, sorta (this checks if a POST was received, not necessarily this form)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = se($_POST, "name", "", false);
    $description = se($_POST, "description", "", false);
    $stock = (int)se($_POST, "stock", 0, false);
    $cost = (int)se($_POST, "cost", 99999, false);
    if (!empty($name)) {
        //TODO other validation (i.e., stock/cost limits)
        //TODO add image in the future
        //Note: 07/12/2021, added iname field for internal name. There's no need to set this because of how the default value works
        //the default value uses the name, lowercases it, and replaces spaces with underscore. The goal is to make it better to use in code
        $query = "INSERT INTO Items(name, description, stock, cost) VALUES (:name, :desc, :stock, :cost)";
        $db = getDB();
        $stmt = $db->prepare($query);
        try {
            $stmt->execute([":name" => $name, ":desc" => $description, ":stock" => $stock, ":cost" => $cost]);
            flash("Created product \"$name\" with id " . $db->lastInsertId());
            //you can force reload the page to prevent duplicate form submissions
            //resolve headers already been sent (due to the balance include in nav.php)
            //https://stackoverflow.com/a/8028987
            if (headers_sent()) {
                echo '<meta http-equiv="refresh" content="0;url=#" />';
                die();
            } else {
                die(header("Refresh:0"));
            }
            
        } catch (PDOException $e) {
            if ($e->errorInfo[0] === '23000') {
                flash("A product with the name \"$name\" already exists, please try another", "warning");
            } else {
                flash("An unexpected error occurred: " . var_export($e->errorInfo, true), "warning");
            }
        }
    }
}

?>
<div class="container-fluid">
    <h3>Add Item</h3>
    <form method="post">
        <?php foreach ($form as $ele) : ?>
            <div class="mb-3">
                <label class="form-label" for="<?php se($ele, "id"); ?>"><?php se($ele, "name", ""); ?></label>
                <?php if ($ele["type"] === "textarea") : ?>
                    <?php  /*Note for required: conditionally applies required attribute, note this is done via echo*/ ?>
                    <textarea class="form-control" name="<?php se($ele, "name"); ?>" id="<?php se($ele, "id"); ?>" <?php echo (se($ele, "required", false, false) ? 'required' : ''); ?>></textarea>
                <?php elseif ($ele["type"] === "select") : ?>
                    <?php /* TODO pending example, likely may extract this form into a reusable component*/ ?>
                <?php else : ?>
                    <?php  /*Note for required: conditionally applies required attribute, note this is done via echo*/ ?>
                    <input class="form-control" name="<?php se($ele, "name"); ?>" id="<?php se($ele, "id"); ?>" <?php echo (se($ele, "required", false, false) ? 'required' : ''); ?> type="<?php se($ele, "type"); ?>" />
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
        <input class="btn btn-primary" type="submit" value="Create" />
    </form>
</div>
<?php
require_once(__DIR__ . "/../../partials/flash.php");
?>