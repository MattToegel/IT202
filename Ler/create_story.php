 <?php 
ini_set('display_errors',1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require("bootstrap.php");

if(isset($_POST['create_story'])){
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
<form method="POST">
	<div>
		<label for="title">Title:</label><input type="text" min="1" name="title" id="title"/>
	</div>
	<div>
		<label for="summary">Summary:</label><input type="text" min="1" name="summary" id="summary"/>
	</div>
	<div>
		<input type="Submit" name="create_story" value="Save"/>
	</div>
</form>