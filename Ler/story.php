 <?php
 //TODO making it dynamically load boostrap if we're not using the routing sample
 if (!isset($container)) {
	 require("bootstrap.php");
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
	 //used for edit store
	 $stories_service = $container->getStories();
	 $author_id = Utils::getLoggedInUser(true)->getID();
	 $result = $stories_service->get_user_story($author_id, $story_id);
	 if ($result['status'] == "success"){
		 $story = $result['story'];
	 }
 }
?>
<?php
 if(!isset($story)) {
	 //init var to empty if our caller doesn't have it defined
	 //this way we don't get exceptions in the partial
	 $story = "";
 }
 include(__dir__ . "/partials/story.form.partial.php");
 ?>