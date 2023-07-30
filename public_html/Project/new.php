<?php
require_once(__DIR__ . "/../../partials/nav.php");

$search["new"] = true;
$search["column"] = "modified";
$search["order"] = "desc";
$cats = search_cats();

?>
<div class="container-fluid">
    <h4>New Cats!</h4>
    <div class="container mx-auto">
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
        <div class="row">
            <?php include(__DIR__ . "/../../partials/pagination_nav.php"); ?>
        </div>
    </div>
</div>
<?php
require_once(__DIR__ . "/../../partials/footer.php");
?>