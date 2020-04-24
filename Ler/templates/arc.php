<?php
//TODO making it dynamically load boostrap if we're not using the routing sample
if (!isset($container)) {
    require(__DIR__ . "/../bootstrap.php");
}
$arc = array();
if(isset($_GET['arc'])){
    $arc_id = $_GET['arc'];
    $user = Utils::getLoggedInUser();
    $user_id = $user->getId();
    $arcs_service = $container->getArcs();
    $result = $arcs_service->get_arc($arc_id);
    if($result && $result['status'] == 'success'){
        $arc = $result['arc'];
        $story_id = $arc["story_id"];//Need to set story id for pulling related arcs for dropdowns
        $result = $arcs_service->get_decisions($arc_id);
        if($result && $result['status'] == 'success'){
            $decisions = $result['decisions'];
        }
    }
}
?>
<div>
    <h3><?php Utils::show($arc, "title");?></h3>
    <div class="content">
        <?php Utils::show($arc,"content");?>
    </div>
    <div>
        <ul>
            <?php if(isset($decisions)):?>
                <?php foreach($decisions as $d):?>
                    <?php if(Utils::get($d, "next_arc_id") > 0):?>

            <li>
                <a href="index?arc/view&arc=<?php Utils::show($d, "next_arc_id");?>">
                    <?php Utils::show($d, "content");?>
                </a>
            </li>
                    <?php endif; ?>
                <?php endforeach;?>
            <?php endif; ?>
        </ul>
    </div>
</div>
