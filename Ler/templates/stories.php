 <?php 
ini_set('display_errors',1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
 //TODO making it dynamically load boostrap if we're not using the routing sample
 if (!isset($container)) {
     require(__DIR__ . "/../bootstrap.php");
 }
//TODO check if we have a GET variable so we can pull specific user
//otherwise default to logged in user
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
        <?php include(__DIR__.'/../partials/story.partial.php');?>
	<?php endforeach; ?>
<?php endif; ?>