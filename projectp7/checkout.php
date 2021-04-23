<?php require_once(__DIR__ . "/partials/nav.php"); ?>
<?php
if (!is_logged_in()) {
    flash("You must be logged in to waste...spend your points");
    die(header("Location: login.php"));
}
?>
<?php
//fetch cart
$db = getDB();
$results = [];
$query = "SELECT c.id, name, p.id as product_id, p.price, c.quantity, (p.price * c.quantity) as sub, (c.price - p.price) as diff, p.name FROM tfp_cart c JOIN tfp_products p on c.product_id = p.id WHERE c.user_id = :uid";
$stmt = $db->prepare($query);
$r = $stmt->execute([
    ":uid" => get_user_id()
]);
if ($r) {
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>
<?php
//use the results from above query, recalc the total to get source of truth, attempt purchase
if (isset($_POST["purchase"])) {
    $total = 0;
    foreach ($results as $item) {
        $total += (int)safe_get($item, "sub", "0");
    }
    $balance = get_points_balance();

    if ($total > $balance) {
        flash("You can't afford this.");
    } else {
        flash("You can afford this");
        //get new order_id
        $query = "SELECT IFNULL(MAX(order_id),1) as oid FROM tfp_orders";
        $stmt = $db->prepare($query);
        $r = $stmt->execute();
        if ($r) {
            $order = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($order && isset($order["oid"])) {
                $order_id = (int)$order["oid"];
                $order_id++;
                //TODO make sure desired product quantity is still available in the products table
                //only then proceed

                //copy cart to orders table
                $query = "INSERT INTO tfp_orders (product_id, quantity, user_id, price, order_id) SELECT product_id, quantity, user_id, price, :oid FROM tfp_cart WHERE tfp_cart.user_id = :uid";
                $stmt = $db->prepare($query);
                $r = $stmt->execute([":uid"=>get_user_id(), ":oid"=>$order_id]);
                if($r){
                    //deduct quantity
                    $query = "UPDATE tfp_products set quantity = quantity - :q WHERE id = :pid";
                    $stmt = $db->prepare($query);
                     foreach ($results as $item) {
                         $pid = (int)safe_get($item, "product_id", -1);
                         $q = (int)safe_get($item, "quantity", 0);
                       $r = $stmt->execute([":pid"=>$pid, ":q"=>$q]);
                       flash(var_export($stmt->errorInfo(), true));
                    }
                    changePoints(get_user_id(), -$total, "Purchase Order ID: $order_id");

                    //empty cart just for this user
                    $query = "DELETE from tfp_cart WHERE user_id = :uid";
                    $stmt = $db->prepare($query);
                    $r = $stmt->execute([":uid"=>get_user_id()]);
                    if($r){
                        flash("Your order has been processed, Tanks for Shopping");
                        die(header("Location: shop.php"));
                    }
                    else{
                        flash("Error deleting cart: " . var_export($stmt->errorInfo(), true));
                    }
                }
                else{
                    flash("Error placing order: " . var_export($stmt->errorInfo(), true));
                }
            }
        }
        else{
            flash("Error getting max: " . var_export($stmt->errorInfo(), true));
        }
    }
}
?>
<div class="container">
    <div class="h3">Cart</div>
    <?php if (count($results) > 0) : ?>
        <ul class="list-group">
            <div class="row fw-bold">
                <div class="col">Name</div>
                <div class="col">Price</div>
                <div class="col">Quantity</div>
                <div class="col">SubTotal</div>
                <div class="col">Difference</div>
                <div></div>
            </div>
            <?php $total = 0; ?>
            <?php foreach ($results as $item) : ?>
                <?php /*calc total */ $total += (int)safe_get($item, "sub", "0"); ?>
                <li class="list-group-item">
                    <div class="row">
                        <div class="col"><?php safer_echo(safe_get($item, "name", "N/A")); ?></div>
                        <div class="col"><?php safer_echo(safe_get($item, "price", "0")); ?></div>
                        <div class="col"><?php safer_echo(safe_get($item, "quantity", "?")); ?></div>
                        <div class="col"><?php safer_echo(safe_get($item, "sub", "0")); ?></div>
                        <div class="col"><?php safer_echo(safe_get($item, "diff", "0")); ?></div>
                        <div class="col-1"><button onclick="deleteCartItem(<?php safer_echo(safe_get($item, 'id', -1)); ?>);" class="btn btn-danger">X</button></div>
                    </div>
                </li>
            <?php endforeach; ?>
            <div class="row fw-bold text-end">
                <div class="col-12">Total: <?php safer_echo($total); ?></div>
            </div>
        </ul>
        <div class="row">
            <div class="col">
                <form method="post">
                    <input type="submit" name="purchase" class="btn btn-success" value="Purchase" />
                </form>
            </div>
        </div>
    <?php else : ?>
        <p> No items in your cart</p>
    <?php endif; ?>
</div>

<script>
    function deleteCartItem(id) {
        if (id) {
            $.post("api/remove_item_from_cart.php", {
                cart_id: id
            }, (data, status) => {
                data = JSON.parse(data);
                if (data.status === 200) {
                    window.location.reload();
                    //$("#"+id).remove();
                }
            });
        }
    }
</script>
<?php require(__DIR__ . "/partials/flash.php");
