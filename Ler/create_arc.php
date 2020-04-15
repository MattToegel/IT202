 <?php 
ini_set('display_errors',1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
 //TODO making it dynamically load boostrap if we're not using the routing sample
 if (!isset($container)) {
     require("bootstrap.php");
 }

if(isset($_POST['create_arc'])){
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
	<div>
		<label for="title">Title:</label><input type="text" min="1" name="title" id="title"/>
	</div>
	<div>
		<label for="summary">Summary:</label><textarea min="1" name="summary" id="summary"/>
	</div>
	<div>
		<input type="Submit" name="create_arc" value="Save"/>
	</div>
</form>