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
 $author_id = $user->getId();
 $arcs_service = $container->getArcs();
 $arc = array();
 $arc_id = -1;
 $myarcs = array();//used for populating dropdown
 if(isset($_SESSION['myArcs'])){
     $myarcs = $_SESSION['myArcs'];
 }

 //this will be populated if it's an arc/create
 if(isset($_GET['story'])){
     //we need a story id for a new arc so if it's present it's new
     $story_id = $_GET['story'];
 }
 //this will be populated if it's an arc/edit
 if(isset($_GET['arc'])){
     $arc_id = $_GET['arc'];
     $result = $arcs_service->get_arc($arc_id);
     if($result && $result['status'] == 'success'){
         $arc = $result['arc'];
         $story_id = $arc["story_id"];//Need to set story id for pulling related arcs for dropdowns
         //gotta grab story so we can make sure we're the author
         $story_service = $container->getStories();
         $story_result = $story_service->get_user_story($author_id, $story_id);
         if($story_result["status"] != "success"){
             Utils::flash("Only the author can edit this");
             header("Location: index.php?arc/view&arc=$arc_id");
             die();
         }
         $result = $arcs_service->get_decisions($arc_id);
         if($result && $result['status'] == 'success'){
             $decisions = $result['decisions'];
             //echo var_export($decisions, true);
         }
     }
 }
 //populate my arcs
 if(count($myarcs) == 0){
     $result = $arcs_service->get_story_arcs($story_id);
     if($result && $result['status'] == 'success'){
         $myarcs = $result['arcs'];
         $_SESSION['myArcs'] = $myarcs;
     }
 }
//TODO add a variable to check if we are editing vs creating so we can reuse this
if(isset($_POST['save_arc'])){
	//TODO validate other data
	$title = $_POST['title'];
	$content = $_POST['content'];
	$visibility = $_POST['visibility'];
	Utils::flash("Visibility: $visibility" );
    $_decisions = array();
    //fetch our decision pieces and build an array of decision objects
    if(isset($_POST['dcontent']) && isset($_POST['nextarc'])){
        $dc = $_POST['dcontent'];
        $nc = $_POST['nextarc'];
        $total = count($dc);
        $limit = 4;//we only want a max of 4 decisions per arc, typical will be 2 or 3
        for($i = 0; $i < $total; $i++){
            if(count($_decisions) >= 4){
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
    if(isset($arc_id) && $arc_id > -1){
        $response = $arcs_service->update_arc($arc_id, $title, $content, $visibility, $_decisions);
        Utils::flash($response["message"]);
        header("Location: index.php?arc/edit&arc=$arc_id");
        die();
        //echo var_export($response, true);
    }
    else {
        $response = $arcs_service->create_arc($title, $content, $visibility, $_decisions);
        //here we can unset our session of myarcs so it'll refresh with this new arc
        unset($_SESSION['myArcs']);
        Utils::flash($response['message']);
        header("Location: index.php?arc/create&story=$story_id");
        die();
    }
}

?>
<form method="POST">
	<div class="form-group">
		<label for="title">Title:</label>
        <input class="form-control" type="text" min="1" name="title" id="title"
               placeholder="Give it an informative title"
               value="<?php Utils::show($arc,"title");?>"/>
	</div>
	<div class="form-group">
		<label for="content">Content:</label>
        <textarea class="form-control" min="1" name="content" id="content" rows="6" placeholder="Write your plot"><?php
            Utils::show($arc, "content");
            ?></textarea>
	</div>
    <div class="col-xl-3 col-sm-12 form-group" data-toggle="fieldset" id="q-fieldset">
    <button type="button" class="btn btn-primary btn-sm" data-toggle="fieldset-add-row" data-limit="4"
            data-target="#q-fieldset">Add Decision</button>
            <small>Limit: 1-4 decisions; give your readers a choice. You'll need to come back to this page to assign the flow once you create more arcs.</small>
            <?php if(!isset($decisions) || count($decisions) == 0):?>
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
        <?php
        $visibility = Utils::get($arc, "visibility");
        include(__DIR__ . "/../partials/visibility.dropdown.partial.php");?>
    </div>
	<div>
		<input class="btn btn-primary" type="Submit" name="save_arc" value="Save"/>
	</div>
</form>
 <?php if(count($myarcs) > 0):?>
     <div class="alert alert-secondary mt-3">Quick Navigate to Arc</div>
 <?php endif;?>
<nav class="navbar navbar-expand-lg navbar-light bg-light">

    <ul class="navbar-nav">
        <?php foreach($myarcs as $_arc):?>
            <li class="nav-item">
                <a href="index.php?arc/edit&arc=<?php echo $_arc['id'];?>"
                   class="nav-link">
                    <?php echo $_arc['title'];?>
                </a>
            </li>
        <?php endforeach;?>
    </ul>
</nav>
<script src="static/js/page.js"></script>