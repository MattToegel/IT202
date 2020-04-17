 <?php
 //TODO making it dynamically load boostrap if we're not using the routing sample
 if (!isset($container)) {
	 require(__DIR__ . "/../bootstrap.php");
 }
 $isEdit = isset($_GET['id']);
 if($isEdit){
	 $story_id = $_GET['id'];
 }

if(isset($_POST['save_story'])){
	//TODO validate other data
	$title = $_POST['title'];
	$summary = $_POST['summary'];
	if(Utils::isLoggedIn(true)){
		$stories_service = $container->getStories();
		$user = Utils::getLoggedInUser();
		$author_id = $user->getId();
		if($isEdit){
			$response = $stories_service->update_story($story_id, $title, $summary);
		}
		else {
			$response = $stories_service->create_story($title, $summary, $author_id);
		}
		Utils::flash($response['message']);
	}
}
 if(isset($story_id)){
	 //used for edit story
	 $stories_service = $container->getStories();
	 $author_id = Utils::getLoggedInUser(true)->getID();
	 $result = $stories_service->get_user_story($author_id, $story_id);
	 if ($result['status'] == "success"){
		 $story = $result['story'];
	 }
	 else{
	 	Utils::flash("Couldn't find the story: " + $result['message']);
	 }
 }
?>
<?php
 if(!isset($story)) {
	 //init var to empty if our caller doesn't have it defined
	 //this way we don't get exceptions in the partial
	 $story = "";
 }
 //Not really necessary //include(__DIR__ . "/../partials/story.form.partial.php");
 ?>
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
	 </div>
 </form>
