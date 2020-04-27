 <?php
 //TODO making it dynamically load boostrap if we're not using the routing sample
 if (!isset($container)) {
	 require(__DIR__ . "/../bootstrap.php");
 }
 $isEdit = isset($_GET['story']);
 $story_id = Utils::get($_GET, "story", -1);

if(isset($_POST['save_story'])){
	//TODO validate other data
	$title = Utils::get($_POST, "title");
	$summary = Utils::get($_POST, "summary");
	$vis = Utils::get($_POST, "visibility");
	if(Utils::isLoggedIn(true)){
		$stories_service = $container->getStories();
		$user = Utils::getLoggedInUser();
		$author_id = $user->getId();
		if($isEdit){
		    if($story_id == -1){
		        Utils::flash("Error getting story, please try again");
            }
		    else {
                $response = $stories_service->update_story($story_id, $title, $summary, $vis);
                Utils::flash(Utils::get($response, "message"));
                if (isset($_POST['starting_arc'])) {
                    $starting_arc = Utils::get($_POST, "starting_arc");;
                    $response = $stories_service->set_starting_arc($story_id, $starting_arc);
                    if(Utils::get($response, "status") != 'success') {
                        Utils::flash(Utils::get($response, "message"));
                    }
                    die(header("Location: index.php?story/edit&story=$story_id"));
                }
            }
		}
		else {
			$response = $stories_service->create_story($title, $summary, $author_id, $vis);
            Utils::flash(Utils::get($response, "message"));
            $story_id = Utils::get($response, "story_id", -1);
            if($story_id > -1) {
                die(header("Location: index.php?story/edit&story=$story_id"));
            }
            else{
                die(header("Location: index.php?mystories"));
            }
		}
	}
}
$arc = array();
$story = "";
 if($isEdit){
	 //used for edit story
	 $stories_service = $container->getStories();
	 $author_id = Utils::getLoggedInUser(true)->getID();
	 $result = $stories_service->get_user_story($author_id, $story_id);
	 if (Utils::get($result, "status") == "success"){
		 $story = Utils::get($result, "story");
		 $arcs_service = $container->getArcs();
		 $result = $arcs_service->get_story_arcs($story_id);
		 if(Utils::get($result, "status") == "success"){
		     $arcs = Utils::get($result, "arcs");
         }
	 }
	 else{
	 	Utils::flash("Couldn't find the story: " . $result['message']);
	 }
 }
?>
 <div class="container-fluid">
 <form method="POST">
	 <div class="form-group">
		 <label for="title">Title:</label>
		 <input class="form-control" type="text" min="1" name="title" id="title" value="<?php Utils::show($story,"title");?>"/>
	 </div>
	 <div class="form-group">
		 <label for="summary">Summary:</label>
		 <textarea class="form-control" min="1" name="summary" id="summary"><?php
			 //by placing the opening and closing tags immediately after and before the textarea tags
			 //it prevents "mysterious" whitespace from showing up in our prefill
			 //anything between the textarea tags is read as a default value including whitespace
			 Utils::show($story,"summary");
			 ?></textarea>
	 </div>
     <div class="form-group form-row">
         <div class="col-1">
         <?php
         $visibility = Utils::get($story, "visibility");
         include(__DIR__ . "/../partials/visibility.dropdown.partial.php");?>
         </div>
         <div class="col-3">
             <label for="summary">Starting Arc:</label>
             <select class="form-control" name="starting_arc">
                 <option value="-1">Pick the first arc of the story</option>
                 <?php foreach($arcs as $arc):?>
                    <option
                        <?php if(Utils::get($arc,"id") == Utils::get($story,"starting_arc")) echo "selected";?>
                        value="<?php Utils::show($arc,"id");?>"><?php
                        Utils::show($arc, "title");
                        ?></option>
                 <?php endforeach; ?>
             </select>
         </div>
     </div>
	 <div class="">
		 <?php
		 if(!empty($story)){
			 $submit_button = "Save";
		 }
		 else{
			 $submit_button = "Create";
		 }
		 ?>
		 <input class="btn btn-primary" type="Submit" name="save_story" value="<?php echo $submit_button;?>"/>
         <a class="btn btn-secondary" type="Submit" href="index.php?arc/create&story=<?php echo $story_id;?>">
             Create Arc
         </a>
	 </div>
 </form>
 </div>
 <?php include(__DIR__ . "/../partials/arc.nav.partial.php");?>
