<?php
require_once(__DIR__ . "/../../partials/nav.php");
is_logged_in(true); //login guard 
$user_id = get_user_id();

$search["owner_id"] = $user_id;
$cats = search_cats();

?>
<div class="container-fluid">
    <h4>My Cats</h4>
    <div class="container mx-auto">
        <div>
            <?php include(__DIR__ . "/../../partials/cat_search_form.php"); ?>
        </div>
        <?php $results = $cats;
        include(__DIR__ . "/../../partials/result_metrics.php"); ?>
        <div class="row justify-content-center">
            <?php foreach ($cats as $cat) : ?>
                <div class="col">
                    <?php render_cat_list_item($cat); ?>
                </div>
            <?php endforeach; ?>
            <?php if (count($cats) === 0) : ?>
                <div class="col-12">
                    You don't own any cats.
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