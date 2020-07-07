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
    }
}
$last_updated = Common::get($_SESSION, "last_sync", false);
?>
<div>
    <p>Welcome, <?php echo Common::get_username();?></p>
    <?php if($last_updated):?>
        <p>Points: <?php echo Common::get($_SESSION["user"], "points", 0);?></p>
        <p>Last Updated: <?php echo $last_updated->format('Y-m-d H:i:s');;?></p>
    <?php endif;?>
    <table class="table">
        <tbody>
        <?php if(count($items) > 0):?>
            <?php
                $rows = (int)(count($items) / 5);
            ?>
        <?php for($i = 0; $i < $rows; $i++):?>
        <tr>
            <?php for($k = 0; $k < 5; $k++):?>
                <?php $index = (($i) * 5) + ($k+1);
                $item = $items[$index];?>
                <td>Name: <?php echo Common::get($item, "name");?></td>
            <?php endfor;?>
        </tr>
        <?php endfor;?>
        <?php endif; ?>
        </tbody>
    </table>
</div>
