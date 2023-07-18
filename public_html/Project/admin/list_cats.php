<?php
//note we need to go up 1 more directory
require(__DIR__ . "/../../../partials/nav.php");

if (!has_role("Admin")) {
    flash("You don't have permission to view this page", "warning");
    redirect("home.php");
}

$cats = search_cats();
$table = ["data" => $cats, "delete_url" => "admin/disable_cat_profile.php", "view_url" => "admin/cat_profile.php", "edit_url" => "admin/cat_profile.php"];

?>
<div class="container-fluid">
    <h1>List Cats</h1>
    <div>
        <?php include(__DIR__ . "/../../../partials/search_form.php"); ?>
    </div>
    <div>
        <?php render_table($table); ?>
    </div>
    <div class="row">
        <?php include(__DIR__ . "/../../../partials/pagination_nav.php"); ?>
    </div>
</div>

<?php
//note we need to go up 1 more directory
require_once(__DIR__ . "/../../../partials/footer.php");
?>