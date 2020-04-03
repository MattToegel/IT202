 <?php 
ini_set('display_errors',1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require("bootstrap.php");

if(isset($_SESSION['user'])){
	$user = $_SESSION['user'];
	$author_id = $user->getId();
	$stories_service = $container->getStories();
	$stories = $stories_service->get_all_my_stories($author_id);
	//echo var_export($stories, true);
}

?>
<?php if (isset($stories)):?>
<div>stories arrived</div>
	<?php foreach($stories["stories"] as $story):?>
		<div>Title: <?php echo $story['title'];?></div>
		<div>Summary: <?php echo $story['summary'];?></div>
		<div>Author: <?php echo $story['author'];?></div>
	<?php endforeach; ?>
<?php endif; ?>