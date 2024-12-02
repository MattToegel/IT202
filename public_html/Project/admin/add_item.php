<?php
//note we need to go up 1 more directory
require(__DIR__ . "/../../../partials/nav.php");
$TABLE_NAME = "RM_Items";
if (!has_role("Admin")) {
    flash("You don't have permission to view this page", "warning");
    //die(header("Location: $BASE_PATH/home.php"));
    redirect("home.php");
}
if (isset($_POST["submit"])) {
    $id = save_data($TABLE_NAME, $_POST);
    if ($id > 0) {
        flash("Created Item with id $id", "success");
    }
}
//get the table definition
$columns = get_columns($TABLE_NAME);
//echo "<pre>" . var_export($columns, true) . "</pre>";
$ignore = ["id", "modified", "created"];
?>
<div class="container-fluid">
    <h1>Add Item</h1>
    <form method="POST">
        <?php foreach ($columns as $index => $column) : ?>
            <?php /* Lazily ignoring fields via hardcoded array*/ ?>
            <?php if (!in_array($column["Field"], $ignore)) : ?>
                <div class="mb-4">
                    <label class="form-label" for="<?php se($column, "Field"); ?>"><?php se($column, "Field"); ?></label>
                    <input class="form-control" id="<?php se($column, "Field"); ?>" type="<?php echo input_map(se($column, "Type", "", false)); ?>" name="<?php se($column, "Field"); ?>" />
                </div>
            <?php endif; ?>
        <?php endforeach; ?>
        <input class="btn btn-primary" type="submit" value="Create" name="submit" />
    </form>
</div>
<?php
//note we need to go up 1 more directory
require_once(__DIR__ . "/../../../partials/footer.php");
