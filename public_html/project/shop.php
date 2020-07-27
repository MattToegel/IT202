<?php
include_once(__DIR__."/partials/header.partial.php");
$items = array();
if(Common::is_logged_in()){
    //this will auto redirect if user isn't logged in
    Common::aggregate_stats_and_refresh();
    $result = DBH::get_shop_items();
    $_items = Common::get($result, "data", false);
    if($_items){
        $items = $_items;
        //echo var_export($items);
    }
}
$last_updated = Common::get($_SESSION, "last_sync", false);
?>
<div>
    <h4>Shop</h4>
    <?php if($last_updated):?>
        <p>Points Last Updated: <?php echo $last_updated->format('Y-m-d H:i:s');;?></p>
    <?php endif;?>
    <div class="row">
        <div class="col-8">
            <table class="table">
                <tbody>
                <?php $total = count($items);
                if($total > 0):?>
                    <?php

                        $rows = (int)($total/ 5) + 1;
                        //echo "<br>Rows: $rows<br>";
                    ?>
                <?php for($i = 0; $i < $rows; $i++):?>
                <tr>
                    <?php for($k = 0; $k < 5; $k++):?>
                        <?php $index = (($i) * 5) + ($k);
                        $item = null;
                        if($index < $total){
                        $item = $items[$index];
                    }
                        ?>
                        <?php if(isset($item)):?>
                            <td>
                                <div class="card">
                                    <div class="card-body">
                                        <h5 class="card-title">
                                            <?php echo Common::get($item,"name");?></h5>
                                        <p class="card-text">
                                            <?php echo Common::get($item, "description");?>
                                        </p>
                                        <p class="card-text">
                                            Cost: <?php echo Common::get($item,"cost", 0);?>
                                        </p>
                                        <button class="btn btn-sm btn-secondary"
                                        data-id="<?php echo Common::get($item, "id", -1);?>"
                                        data-type="<?php echo Common::get($item, "stat","");?>"
                                        data-cost="<?php echo Common::get($item, "cost", 0);?>"
                                        data-name="<?php echo Common::get($item, "name");?>"
                                        onclick="addToCart(this);">Add</button>
                                    </div>

                                </div>
                            </td>
                        <?php endif;?>
                    <?php endfor;?>
                </tr>
                <?php endfor;?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
        <div class="col-4">
            <h5>Cart</h5>
            <h6 class="row">Points: <div id="used">0</div>/<div><?php echo Common::get($_SESSION["user"], "points", 0);?></div></h6>
            <ul class="list-group" id="cart">

            </ul>
            <button class="btn btn-secondary" onclick="purchase();">Complete Purchase</button>
        </div>
    </div>
</div>
<script>
    //$ in var name signifies a jquery obj
    let $cart = $("#cart");
    //this is fine because php is executed first on the server then the result is sent to the browser
    //and will be the expected value by the time JS gets to this
    let points = <?php echo Common::get($_SESSION["user"], "points", 0);?>;
    let total = 0;
    function updateCost(){
        let sum = 0;
        $cart.find("li").each(function (index, item) {
            let q = $(item).data("quantity");
            let c = $(item).data("cost");
            sum += (q * c);

        });
        total = sum;
        let $used = $("#used");
        $used.text(total);
    }
    function addToCart(ele){

        let itemType = $(ele).data("type");
        let itemCost = $(ele).data("cost");
        let itemName = $(ele).data("name");
        let itemId = $(ele).data("id");
        if(total + itemCost > points){
            alert("You can't afford that");
            return;
        }
        let updated = false;
        $cart.find("li").each(function (index, item) {
            let _itemType = $(item).data("type");
            let _itemName = $(item).data("name");
            if(_itemType == itemType){
                let q = $(item).data("quantity");
                q++;
                $(item).data("quantity", q);
                $(item).find("span").text(_itemName + ": " + q);
                updated = true;
            }
        });
        if(!updated){
            let $li = $("<li></li>");
            $li.attr("class", "list-group-item");
            $li.append("<span></span><button onclick='removeFromCart(this);' class='btn btn-sm btn-danger'>X</button>");
            $li.data("type", itemType);
            $li.data("quantity", 1);
            $li.data("cost", itemCost);
            $li.data("name", itemName);
            $li.data("id", itemId);
            $li.find("span").text(itemName + ": " + 1);

            $cart.append($li);
        }
        updateCost();
    }
    function removeFromCart(ele){
        $(ele).closest("li").remove();
        updateCost();
    }
    function purchase(){
        let data = [];
        $cart.find("li").each(function(index, item){
            let itemType = $(item).data("type");
            let itemQuantity = $(item).data("quantity");
            let itemCost = $(item).data("cost");
            let itemId = $(item).data("id");
            data.push({type: itemType, quantity: itemQuantity, cost: itemCost, id: itemId});
        });
        console.log(data);
        console.log(JSON.stringify(data));
        $.post("api/complete_purchase.php", {"order": JSON.stringify(data)}, function(data, status){
            //alert("Data: " + data + "\nStatus: " + status);
            //reload the page
            window.location.replace("shop.php");
        });
    }
</script>