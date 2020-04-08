 <?php 
ini_set('display_errors',1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require("bootstrap.php");

if(isset($_POST['save_story'])){
	//TODO validate other data
	$title = $_POST['title'];
	$summary = $_POST['summary'];
	if(isset($_SESSION['user'])){
		$stories_service = $container->getStories();
		$user = $_SESSION['user'];
		$author_id = $user->getId();
		
		$response = $stories_service->create_story($title, $summary, $author_id);
		echo var_export($response, true);
	}
}

?>
<?php
 //init var to empty
 $story ="";
 include("partials/story.form.partial.php");
 ?>