 <?php 
ini_set('display_errors',1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
 //TODO making it dynamically load boostrap if we're not using the routing sample
 if (!isset($container)) {
     require(__DIR__ . "/../bootstrap.php");
 }
 $arcs_service = $container->getArcs();
 if(isset($_GET['story'])){
     //we need a story id for a new arc so if it's present it's new
     $story_id = $_GET['story'];
 }
 $arc = array();
 if(isset($_GET['arc'])){
     //this should only be set during edit
     //we don't need a story id since arc already exists and therefore should be assigned
     $arc_id = $_GET['arc'];
     $result = $arcs_service->get_arc($arc_id);
     if($result && $result['status'] == 'success'){
         $arc = $result['arc'];
         $result = $arcs_service->get_decisions($arc_id);
         if($result && $result['status'] == 'success'){
             $decisions = $result['decisions'];
             //echo var_export($decisions, true);
         }
     }
     //echo var_export($arc, true);
 }
//TODO add a variable to check if we are editing vs creating so we can reuse this
if(isset($_POST['save_arc'])){
	//TODO validate other data
	$title = $_POST['title'];
	$content = $_POST['content'];
	if(Utils::isLoggedIn()){

		$user = Utils::getLoggedInUser();
		$author_id = $user->getId();
		if(isset($arc_id) && count($arc) > 0){
		    //echo "<br>";
		    //echo var_export($_POST, true);
		    //echo "<br>";
		    $_decisions = array();
		    if(isset($_POST['dcontent']) && isset($_POST['nextarc'])){
		        $dc = $_POST['dcontent'];
		        $nc = $_POST['nextarc'];
		        $i = 0;
		        foreach($dc as $c){
		            $decision = new Decision(null, $c, $arc_id,
                        null, null, true, true, $nc[$i]);
		            array_push($_decisions, $decision);
		            $i++;
                }
            }
		    //echo var_export($_decisions, true);
            $response = $arcs_service->update_arc($arc_id, $title, $content, $_decisions);
        }
		else {
            $response = $arcs_service->create_arc($title, $content, $author_id);
        }
		//echo var_export($response, true);
	}
}

?>
<form method="POST">
	<div class="form-group">
		<label for="title">Title:</label>
        <input class="form-control" type="text" min="1" name="title" id="title"
               value="<?php Utils::show($arc,"title");?>"/>
	</div>
	<div class="form-group">
		<label for="content">Content:</label>
        <textarea class="form-control" min="1" name="content" id="content"><?php
            Utils::show($arc, "content");
            ?></textarea>
	</div>
    <div class="col-xl-3 col-sm-12 form-group" data-toggle="fieldset" id="q-fieldset">
    <button type="button" class="btn btn-primary btn-sm" data-toggle="fieldset-add-row"
            data-target="#q-fieldset">+</button>
        <div data-toggle="fieldset-entry">
            <?php if(!isset($decisions)):?>
                <div class="input-group input-group-sm mb-3">
                    <div class="input-group-prepend">
                        <span class="input-group-text" id="">Decision</span>
                    </div>
                    <textarea class="form-control" min="1" name="dcontent[]" id="0"></textarea>
                    <select name="nextarc[]">
                        <option value="-1">Select an Arc</option>
                        <?php if (isset($myarcs)):?>
                            <?php foreach($myarcs as $arc):?>
                                <option value="<?php Utils::show($arc, "id");?>">
                                    <?php echo Utils::show($arc, "title");?>
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                    <button type="button" class="btn btn-danger btn-sm" data-toggle="fieldset-remove-row" id="q-{{loop.index0}}-remove">-</button>
                </div>
            <?php else: ?>
            <?php $index = 0;
            foreach($decisions as $d):?>
                    <div class="input-group input-group-sm mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text" id="">Decision</span>
                        </div>
                        <textarea class="form-control" min="1" name="dcontent[]" id="0"><?php
                            Utils::show($d, "content");
                            ?></textarea>
                        <select name="nextarc[]">
                            <option value="-1">Select an Arc</option>
                            <?php if (isset($myarcs)):?>
                                <?php foreach($myarcs as $arc):?>
                                <option value="<?php Utils::show($arc, "id");?>">
                                    <?php echo Utils::show($arc, "title");?>
                                </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                        <button type="button" class="btn btn-danger btn-sm" data-toggle="fieldset-remove-row" id="q-{{loop.index0}}-remove">-</button>
                    </div>
            <?php $index++;?>
            <?php endforeach; ?>
            <?php endif;?>
        </div>
    </div>
	<div>
		<input class="btn btn-primary" type="Submit" name="save_arc" value="Save"/>
	</div>
</form>
 <script src="static/js/page.js"></script>