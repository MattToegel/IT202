<?php
include_once(__DIR__."/partials/header.partial.php");

if(Common::is_logged_in()){
    //this will auto redirect if user isn't logged in
}
if(isset($_GET["s"])){
    $questionnaire_id = $_GET["s"];
}
else{
    Common::flash("Not a valid survey", "warning");
    die(header("Location: surveys.php"));
}
//TODO: Note, internally calling them questionnaires (and for admin), user facing they're called surveys.
$response = DBH::get_questionnaire_by_id($questionnaire_id);
$available = [];
if(Common::get($response, "status", 400) == 200){
    $available = Common::get($response, "data", []);
}
?>
<div>
    <div class="list-group">
        <?php foreach($available as $s): ?>
            <div class="list-group-item">
                <?php foreach($s as $question):?>
                    <?php var_export($question)?>
                <?php endforeach;?>
            </div>
        <?php endforeach; ?>
    </div>
</div>
