 <?php 
ini_set('display_errors',1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
 //TODO making it dynamically load boostrap if we're not using the routing sample
 if (!isset($container)) {
     require(__DIR__ . "/../bootstrap.php");
 }
 if(isset($_GET['story'])){
     //we need a story id for a new arc so if it's present it's new
     $story_id = $_GET['story'];
 }
 if(isset($_GET['arc'])){
     //this should only be set during edit
     //we don't need a story id since arc already exists and therefore should be assigned
 }
//TODO add a variable to check if we are editing vs creating so we can reuse this
if(isset($_POST['save_arc'])){
	//TODO validate other data
	$title = $_POST['title'];
	$summary = $_POST['summary'];
	if(isset($_SESSION['user'])){
		$arcs_service = $container->getArcs();
		$user = $_SESSION['user'];
		$author_id = $user->getId();
		
		$response = $arcs_service->create_arc($title, $summary, $author_id);
		echo var_export($response, true);
	}
}

?>
<form method="POST">
	<div class="form-group">
		<label for="title">Title:</label>
        <input class="form-control" type="text" min="1" name="title" id="title"/>
	</div>
	<div class="form-group">
		<label for="summary">Summary:</label>
        <textarea class="form-control" min="1" name="summary" id="summary"></textarea>
	</div>
	<div>
		<input class="btn btn-primary" type="Submit" name="save_arc" value="Save"/>
	</div>
</form>