<?php
//TODO making it dynamically load boostrap if we're not using the routing sample
if (!isset($container)) {
    require(__DIR__ . "/../bootstrap.php");
}
$arc = array();
if(isset($_GET['arc'])){
    $arc_id = Utils::get($_GET, "arc", -1);
    if($arc_id == -1){
        Utils::flash("Error finding arc.");
        die();//TODO more gracefully handle this
    }
    $user = Utils::getLoggedInUser();
    //TODO for later feature: if user logged in bookmark arc
    $user_id = -1;//anonymous user
    if($user) {
        $user_id = $user->getId();
    }
    $arcs_service = $container->getArcs();
    $result = $arcs_service->get_arc($arc_id);
    if(Utils::get($result, "status", "error") == 'success'){
        $arc = Utils::get($result, "arc");
        $arc_visibility = Utils::get($arc, "visibility", Visibility::draft);
        $story_id = Utils::get($arc, "story_id", -1);
        if($arc_visibility == Visibility::draft){
            Utils::flash("Sorry, that arc is in draft mode and can't be viewed");
            if($story_id > -1){
                die(header("Location: index.php?story/view&story=$story_id"));
            }
            else{
                die(header("Location: index.php"));
            }
        }
        else if($arc_visibility == Visibility::private && $user_id == -1){
            Utils::flash("Sorry, that arc is private, you must be logged in to view");
            if($story_id > -1){
                die(header("Location: index.php?story/view&story=$story_id"));
            }
            else{
                die(header("Location: index.php"));
            }
        }
        $result = $arcs_service->get_decisions($arc_id);
        if(Utils::get($result, "status", "error") == 'success'){
            $decisions = Utils::get($result, "decisions", array());
        }
    }
}
?>
<div class="container-fluid text-center">
    <h3><?php Utils::show($arc, "title");?></h3>
    <div class="content p-3">
        <pre class="text-break" style="overflow: auto; white-space:pre-wrap;"><?php echo htmlspecialchars_decode(Utils::get($arc,"content"));?></pre>
    </div>
    <div class="container justify-content-center text-center bg-light p-1 mt-3">
        <?php if(isset($decisions) && count($decisions) > 0 && Utils::get($decisions[0], "next_arc_id", -1) > -1):?>
            <h5>Pick your path</h5>
        <?php else:?>
            <h5>The End</h5>
        <?php endif; ?>
        <ul class="navbar-nav">

            <?php foreach($decisions as $d):?>
                <?php if(Utils::get($d, "next_arc_id") > 0):?>

            <li class="nav-item m-2">
                <a class="btn btn-primary" href="index.php?arc/view&arc=<?php Utils::show($d, "next_arc_id");?>">
                    <?php Utils::show($d, "content");?>
                </a>
            </li>
                <?php endif; ?>
            <?php endforeach;?>
        </ul>
    </div>
</div>
