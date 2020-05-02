 <?php 
ini_set('display_errors',1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
 //TODO making it dynamically load boostrap if we're not using the routing sample
 if (!isset($container)) {
     require(__DIR__ . "/../bootstrap.php");
 }
 //make sure we're logged in
 Utils::isLoggedIn(true);
 $user = Utils::getLoggedInUser();
 $user_id = $user->getId();
 $arcs_service = $container->getArcs();
 $arc_id = Utils::get($_GET, "arc", -1);
 //this will be populated if it's an arc/create
 //we need a story id for a new arc so if it's present it's new
 $story_id = Utils::get($_GET, "story", -1);

//used for populating dropdown
 $arcs = Utils::get($_SESSION, "myArcs$story_id", array());

 //this will be populated if it's an arc/edit
 if($arc_id > -1){
     $result = $arcs_service->get_arc($arc_id);
     if(Utils::get($result,"status", "error") == 'success'){
         $arc = Utils::get($result, "arc", array());
         $story_id = Utils::get($arc, "story_id", -1);//Need to set story id for pulling related arcs for dropdowns
         if($story_id == -1){
             Utils::flash("An error occurred: Somehow our Arc lost its story.");
         }
         //gotta grab story so we can make sure we're the author
         $story_service = $container->getStories();
         $story_result = $story_service->get_user_story($user_id, $story_id);
         if(Utils::get($story_result, "status") != "success"){
             Utils::flash("Only the author can edit this");
             header("Location: index.php?arc/view&arc=$arc_id");
             die();
         }
         $result = $arcs_service->get_decisions($arc_id);
         if(Utils::get($result,"status", "error") == 'success'){
             $decisions = Utils::get($result, "decisions", array());
         }
     }
 }
 else{
     //prevent create form from having problem
     $arc = array();
 }

 //populate my arcs
 if(count($arcs) == 0){
     $result = $arcs_service->get_story_arcs($story_id);
     if($result && Utils::get($result, "status") == 'success'){
         $arcs =  Utils::get($result, "arcs");;
         $_SESSION["myArcs$story_id"] = $arcs;
     }
 }
//TODO add a variable to check if we are editing vs creating so we can reuse this
if(isset($_POST['save_arc_edit']) || isset($_POST['save_arc_new'])){
	//TODO validate other data
	$title =  Utils::get($_POST, "title");
	$content =  htmlspecialchars(Utils::get($_POST, "content"));
	$visibility =  Utils::get($_POST, "visibility");
    $_decisions = array();
    //fetch our decision pieces and build an array of decision objects
    if(isset($_POST['dcontent']) && isset($_POST['nextarc'])){
        $dc = Utils::get($_POST, "dcontent");
        $nc = Utils::get($_POST, "nextarc");
        $total = count($dc);
        $limit = 4;//we only want a max of 4 decisions per arc, typical will be 2 or 3
        for($i = 0; $i < $total; $i++){
            //NOTE: we don't want more than the limit
            if(count($_decisions) >= $limit){
                break;
            }
            $decision = new Decision(
                null,
                $dc[$i],
                $arc_id,
                null,
                null,
                true,
                true,
                $nc[$i]
            );
            array_push($_decisions, $decision);
        }
    }
    if($arc_id > -1){
        $response = $arcs_service->update_arc($arc_id, $title, $content, $visibility, $_decisions);
    }
    else {
        $response = $arcs_service->create_arc($title, $content, $story_id, $visibility, $_decisions);
        $arc_id = Utils::get($response, "arc_id", -1);
        //here we can unset our session of myarcs so it'll refresh with this new arc
        unset($_SESSION["myArcs$story_id"]);
    }
    Utils::flash(Utils::get($response, "message","An error occurred"));
    if(isset($_POST['save_arc_edit'])){
        die(header("Location: index.php?arc/edit&arc=$arc_id"));
    }
    if(isset($_POST['save_arc_new'])){
        die(header("Location: index.php?arc/create&story=$story_id"));
    }
}

?>
<div class="container-fluid">
<form method="POST">
	<div class="form-group">
		<label for="title">Title:</label>
        <input class="form-control" type="text" min="1" name="title" id="title"
               placeholder="Give it an informative title"
               value="<?php Utils::show($arc,"title");?>" required/>
	</div>
	<div class="form-group">
		<label for="content">Content:</label>
        <textarea class="form-control" min="1" name="content" id="content" rows="6" placeholder="Write your plot" required><?php
            Utils::show($arc, "content");
            ?></textarea>
	</div>
    <div class="form-group" data-toggle="fieldset" id="q-fieldset">
    <button type="button" class="btn btn-primary btn-sm mb-3" data-toggle="fieldset-add-row" data-limit="4"
            data-target="#q-fieldset">Add Decision</button>
            <small>Limit: 1-4 decisions; give your readers a choice. You'll need to come back to this page to assign the flow once you create more arcs.</small>
            <?php if(!isset($decisions) || count($decisions) == 0):?>
                <?php //NOTE: we need at least 1 partial for the JS to work?>
                <?php include(__DIR__."/../partials/decision.partial.php");?>
            <?php else: ?>
            <?php $index = 0;
            foreach($decisions as $decision):?>
                <?php //partial expects $decision and $arc_id?>
                <?php include(__DIR__."/../partials/decision.partial.php");?>
            <?php $index++;?>
            <?php endforeach; ?>
            <?php endif;?>
    </div>
    <div class="form-group">
        <div class="col-2">
        <?php
        $visibility = Utils::get($arc, "visibility", Visibility::draft);
        include(__DIR__ . "/../partials/visibility.dropdown.partial.php");?>
        </div>
    </div>
	<div>
		<input class="btn btn-primary" type="Submit" name="save_arc_edit" value="Save & Edit"/>
        <input class="btn btn-primary" type="Submit" name="save_arc_new" value="Save & Create Another"/>
        <a class="btn btn-secondary"
           href="index.php?story/edit&story=<?php echo $story_id;?>">
            Back to Story
        </a>
        <a class="btn btn-danger" href="index.php?arc/delete&arc=<?php echo $arc_id;?>">Delete Arc</a>
	</div>
</form>
</div>
 <?php include(__DIR__ . "/../partials/arc.nav.partial.php");?>
<script src="static/js/page.js"></script>