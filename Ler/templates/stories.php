 <?php 
ini_set('display_errors',1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
 //TODO making it dynamically load boostrap if we're not using the routing sample
 if (!isset($container)) {
     require(__DIR__ . "/../bootstrap.php");
 }
//TODO check if we have a GET variable so we can pull specific user
 $stories_service = $container->getStories();
//otherwise default to logged in user
if(isset($mystories) && Utils::isLoggedIn()){
	$user = Utils::getLoggedInUser();
	$author_id = $user->getId();

	$result = $stories_service->get_all_user_stories($author_id);
	if($result['status'] == 'success'){
		$stories = $result['stories'];
	}
	else{
		Utils::flash($result['message']);
	}
	//echo var_export($stories, true);
}
else{
	$title = "";
	$author_name = "";
	if(isset($_POST['title'])){
		$title = $_POST['title'];
	}
	if(isset($_POST['author'])){
		$author_name = $_POST['author'];
	}
	$result = $stories_service->get_stories($title, $author_name);
	if($result['status'] == 'success'){
		$stories = $result['stories'];
	}
	else{
		Utils::flash($result['message']);
	}
}

?>
<?php if(!isset($mystories)):?>
 <form method="POST">
	 <div class="form-group">
		 <label>Title</label>
		 <input class="form-control" type="text" name="title" value="<?php Utils::show($_POST, "title");?>"/>
	 </div>
	 <div class="form-group">
		 <label>Title</label>
		 <input class="form-control" type="text" name="author" <?php Utils::show($_POST, "author");?>/>
	 </div>
	 <input class="btn btn-primary" type="submit" value="Filter"/>
 </form>
<?php endif;?>
<?php if (isset($stories)):?>
	<?php foreach($stories as $story):?>
        <?php include(__DIR__.'/../partials/story.partial.php');?>
	<?php endforeach; ?>
<?php endif; ?>