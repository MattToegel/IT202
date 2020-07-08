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
    <p>Welcome, <?php echo Common::get_username();?></p>
    <?php if($last_updated):?>
        <p>Last Updated: <?php echo $last_updated->format('Y-m-d H:i:s');;?></p>
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
        </div>
    </div>
</div>
<script>
    let cart = document.getElementById("cart");
    function addToCart(){

    }
    function removeFromCart(){

    }
    function purchase(){

    }
</script>