<?php
require(__DIR__ . "/../../partials/nav.php");

$results = [];
$db = getDB();
$stmt = $db->prepare("SELECT id, name, description, cost, stock, image FROM RM_Items WHERE stock > 0 LIMIT 50");
try {
    $stmt->execute();
    $r = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if ($r) {
        $results = $r;
    }
} catch (PDOException $e) {
    error_log(var_export($e, true));
    flash("Error fetching items", "danger");
}
?>
<script>
    function purchase(item) {
        console.log("TODO purchase item", item);
        //alert("It's almost like you purchased an item, but not really");
        if (add_to_cart) {
            add_to_cart(item);
        }
    }
</script>

<div class="container-fluid">
    <h1>Shop</h1>
    <div class="row">
        <div class="col">
            <div class="row row-cols-1 row-cols-sm-1 row-cols-md-2 row-cols-lg-3 g-4">
                <?php foreach ($results as $item) : ?>
                    <div class="col">
                        <div class="card bg-light" style="height:25em">
                            <div class="card-header">
                                RM Placeholder
                            </div>
                            <?php if (se($item, "image", "", false)) : ?>
                                <img src="<?php se($item, "image"); ?>" class="card-img-top" alt="...">
                            <?php endif; ?>

                            <div class="card-body">
                                <h5 class="card-title">Name: <?php se($item, "name"); ?></h5>
                                <p class="card-text">Description: <?php se($item, "description"); ?></p>
                            </div>
                            <div class="card-footer">
                                Cost: <?php se($item, "cost"); ?>
                                <button onclick="purchase('<?php se($item, 'id'); ?>')" class="btn btn-primary">Buy Now</button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <div class="col-4" style="min-width:30em">
            <?php require(__DIR__ . "/../../partials/cart.php"); ?>
        </div>
    </div>
</div>

<?php
require(__DIR__ . "/../../partials/footer.php");
?>