 <?php 
ini_set('display_errors',1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
 //TODO making it dynamically load boostrap if we're not using the routing sample
 if (!isset($container)) {
     require("bootstrap.php");
 }

if(isset($_SESSION['user'])){
	$user = $_SESSION['user'];
	$author_id = $user->getId();
	$stories_service = $container->getStories();
	$stories = $stories_service->get_all_user_stories($author_id);
	//echo var_export($stories, true);
}

?>
<?php if (isset($stories) && isset($stories["stories"])):?>
<div>stories arrived</div>
	<?php foreach($stories["stories"] as $story):?>
        <?php include('partials/story.partial.php');?>
	<?php endforeach; ?>
<?php endif; ?>