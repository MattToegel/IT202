<?php require_once(__DIR__ . "/partials/nav.php"); ?>
<?php
if (!is_logged_in()) {
    flash("You must be logged in to waste...spend your points");
    die(header("Location: login.php"));
}
?>
<?php
$query = "SELECT id,name, price, quantity, description from tfp_products WHERE quantity > 0 limit 25";
$db = getDB();
$stmt = $db->prepare($query);
$results = [];
$r = $stmt->execute();
if ($r) {
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>
<script>
    function addToCart(itemId) {
        console.log("adding", itemId);
        $.post("api/add_to_cart.php", {
            itemId: itemId
        }, (data, status) => {
            console.log("response", data, status);
            getCart();
        });

    }

    function getCart() {
        $.get("api/get_cart.php", (data, status) => {
            console.log("response", data, status);
            let $cartContainer = $("#cart");
            $cartContainer.html("");
            let cart = JSON.parse(data).cart;
            cart.forEach(item => {
                let $item = $("<div></div>").text(item.name + ": " + item.price + " " + item.quantity + "x = " + item.sub);
                $cartContainer.append($item);
            });

        });
    }
    $(document).ready(() => {
        getCart();
    });
</script>
<div class="container-fluid">
    <div class="h3">Shop</div>
    <?php if (count($results) > 0) : ?>
        <div class="card-group">
            <?php foreach ($results as $item) : ?>
                <div class="card p-1" style="max-width: 15em; min-width:15em">
                    <div class="card-body">
                        <h5 class="card-title"><?php safer_echo($item["name"]); ?></h5>
                        <p class="card-text"><?php safer_echo($item["description"]); ?></p>
                        <div class="card-text">
                            <div class="row">
                                <div class="col">
                                    <?php safer_echo($item["price"]); ?>
                                </div>
                                <div class="col">
                                    Stock: <?php safer_echo($item["quantity"]); ?></div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <button onclick="addToCart(<?php safer_echo($item['id']); ?>);">Add to Cart</button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else : ?>
        <p>Sorry, shop's all sold out.</p>
    <?php endif; ?>
</div>
<div id="cart" style="float: right;">

</div>

<?php require(__DIR__ . "/partials/flash.php");
