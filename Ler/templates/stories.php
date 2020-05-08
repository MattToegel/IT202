 <?php 
ini_set('display_errors',1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
 //TODO making it dynamically load boostrap if we're not using the routing sample
 if (!isset($container)) {
     require(__DIR__ . "/../bootstrap.php");
 }
 $stories_service = $container->getStories();
 $showSpecificList = false;
 //determine if we need to fetch a specific list of stories for logged in user
 if(Utils::isLoggedIn()){
     $user = Utils::getLoggedInUser();
     $author_id = $user->getId();
     if(isset($mystories)){
         $result = $stories_service->get_all_user_stories($author_id);
         $showSpecificList = true;
     }
     else if(isset($myprogress)){
         $result = $stories_service->get_my_stories_with_progress($author_id);
         $showSpecificList = true;
     }
     else if(isset($mybookmarks)){
         $showSpecificList = true;
         $favorites_service = $container->getFavorites();
         $result = $favorites_service->get_user_favorites($author_id);
     }
     if($showSpecificList) {
         if (Utils::get($result, "status", "error") == 'success') {
             $stories = Utils::get($result, "stories");
         } else {
             Utils::flash(Utils::get($result, "message"));
         }
     }
 }
 //default to show all stories for filter
if(!$showSpecificList){
	$title = Utils::get($_POST, "title");
	$author_name = Utils::get($_POST, "author");
	$result = $stories_service->get_stories($title, $author_name);
	if(Utils::get($result, "status","error") == 'success'){
		$stories = Utils::get($result, "stories");
	}
	else{
		Utils::flash(Utils::get($result, "message"));
	}
}

?>
<?php if(!$showSpecificList):?>
 <form class="form-inline" method="POST">
	 <div class="form-group m-1">
		 <label class="mr-1" for="title">Title</label>
		 <input id="title" class="form-control" type="text" name="title" value="<?php Utils::show($_POST, "title");?>"/>
	 </div>
	 <div for="author" class="form-group m-1">
		 <label class="mr-1">Author Name</label>
		 <input id="author" class="form-control" type="text" name="author" <?php Utils::show($_POST, "author");?>/>
	 </div>
	 <input class="btn btn-primary" type="submit" value="Search"/>
 </form>
<?php endif;?>
<?php if (isset($stories) && count($stories) > 0 ):?>
	<?php foreach($stories as $story):?>
        <?php
        //used for my stories since there's no join on user table to get name
        //we'll cheatsy it here
        if(isset($user) && !isset($story['username'])){
            if(Utils::get($story, 'author') == $user->getId()){
                $story['username'] = $user->getUsername();
            }
        }
        ?>
        <?php include(__DIR__.'/../partials/story.partial.php');?>
	<?php endforeach; ?>
<?php else:?>
<p class="alert alert-secondary">No stories found, try broadening your search.</p>
<?php endif; ?>