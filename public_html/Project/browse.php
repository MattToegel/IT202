<?php
require(__DIR__ . "/../../partials/nav.php");

// remove single view filter
if (isset($_GET["id"])) {
    unset($_GET["id"]);
}
$cats = search_cats();
?>
<div class="container-fluid">
    <h4>Purfect Friends</h4>
    <div class="container mx-auto">
        <div>
            <?php include(__DIR__ . "/../../partials/search_form.php"); ?>
        </div>
        <div class="row justify-content-center">
            <?php foreach ($cats as $cat) : ?>
                <div class="col">
                    <?php render_cat_list_item($cat); ?>
                </div>
            <?php endforeach; ?>
            <?php if (count($cats) === 0) : ?>
                <div class="col-12">
                    No furry friends available
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php
require_once(__DIR__ . "/../../partials/footer.php");
?>