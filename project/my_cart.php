<?php require_once(__DIR__ . "/partials/nav.php"); ?>
<?php
$db = getDB();
$stmt = $db->prepare("SELECT p.name, c.price, c.quantity, (c.price * c.quantity) as sub from F20_Cart c JOIN F20_Products p on c.product_id = p.id where c.user_id = :id");
$stmt->execute([":id"=>get_user_id()]);
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
    <div class="container-fluid">
        <h3>My Cart</h3>
        <div class="list-group">
        <?php if($results && count($results) > 0):?>
            <div class="list-group-item">
                <div class="row">
                    <div class="col">
                       Name
                    </div>
                    <div class="col">
                        Price
                    </div>
                    <div class="col">
                        Quantity
                    </div>
                    <div class="col">
                        Subtotal
                    </div>
                </div>
            </div>
            <?php foreach($results as $r):?>
            <div class="list-group-item">
                <div class="row">
                    <div class="col">
                        <?php echo $r["name"];?>
                    </div>
                    <div class="col">
                        <?php echo $r["price"];?>
                    </div>
                    <div class="col">
                        <?php echo $r["quantity"];?>?
                    </div>
                    <div class="col">
                        <?php echo $r["sub"];?>
                    </div>
                </div>
            </div>
            <?php endforeach;?>
        <?php else:?>
        <div class="list-group-item">
            No items in cart
        </div>
        <?php endif;?>
        </div>
    </div>
<?php require(__DIR__ . "/partials/flash.php");