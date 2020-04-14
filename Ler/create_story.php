 <?php 
ini_set('display_errors',1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
 //TODO making it dynamically load boostrap if we're not using the routing sample
 if (!isset($container)) {
	 require("bootstrap.php");
 }

if(isset($_POST['save_story'])){
	//TODO validate other data
	$title = $_POST['title'];
	$summary = $_POST['summary'];
	if(isset($_SESSION['user'])){
		$stories_service = $container->getStories();
		$user = $_SESSION['user'];
		$author_id = $user->getId();
		
		$response = $stories_service->create_story($title, $summary, $author_id);
		//echo var_export($response, true);
		Utils::flash($response['message']);
	}
}

?>
<?php
 //init var to empty
 $story ="";
 include(__dir__ . "/partials/story.form.partial.php");
 ?>