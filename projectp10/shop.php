<?php require_once(__DIR__ . "/partials/nav.php"); ?>
<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
if (!is_logged_in()) {
    flash("You must be logged in to waste...spend your points");
    die(header("Location: login.php"));
}
?>
<?php
$db = getDB();
//fetch count of our filtered results
$query = "SELECT count(1) as total from tfp_products WHERE quantity > 0";
$stmt = $db->prepare($query);
$r = $stmt->execute();
$total_pages = 0;
if ($r) {
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $total_pages = (int)safe_get($result, "total", 0);
}
$items_per_page = 3;
//calc number of pages
$total_pages = ceil($total_pages / $items_per_page);

//get current page (default to 1)
$page = (int)safe_get($_GET, "page", 1);
if ($page < 1) {
    $page = 1;
}
//IMPORTANT: this is required for the execute to set the limit variables properly
//otherwise it'll convert the values to a string and the query will fail since LIMIT expects only numerical values and doesn't cast
$db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
//END IMPORTANT

//determine offset for running the data query
$offset = ($page - 1) * $items_per_page;
$query = "SELECT id,name, price, quantity, description from tfp_products WHERE quantity > 0 limit :o, :l";
$stmt = $db->prepare($query);
$results = [];
//$stmt->bindValue(":o", $offset, PDO::PARAM_INT);//alternative way to determine the datatype of how a parameter gets bound
$r = $stmt->execute([":o" => $offset, ":l" => $items_per_page]);

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
<div class="container">
    <div width="80%" style="margin-left: auto; margin-right: auto" >
        <div class="h3">Shop</div>
        <?php if (count($results) > 0) : ?>
            <div class="card-group justify-content-center">
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
    <ul class="pagination justify-content-center">
                <?php if (($page - 1) >= 1) : ?>
                    <li class="page-item">
                        <a class="page-link" href="<?php safer_echo($_SERVER['PHP_SELF'] . "?page=" . ($page - 1)); ?>" aria-label="Previous">
                            <span aria-hidden="true">&laquo;</span>
                        </a>
                    </li>
                <?php endif; ?>
                <?php for ($i = 0; $i < $total_pages; $i++) : ?>
                    <li class="page-item"><a class="page-link" href="<?php safer_echo($_SERVER['PHP_SELF'] . "?page=" . ($i + 1)); ?>"><?php safer_echo(($i + 1)); ?></a></li>
                <?php endfor; ?>
                <?php if (($page + 1) <= $total_pages) : ?>
                    <li class="page-item">
                        <a class="page-link" href="<?php safer_echo($_SERVER['PHP_SELF'] . "?page=" . ($page + 1)); ?>" aria-label="Next">
                            <span aria-hidden="true">&raquo;</span>
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
</div>
<div id="cart" style="float: right;">

</div>
<?php require(__DIR__ . "/partials/flash.php");
