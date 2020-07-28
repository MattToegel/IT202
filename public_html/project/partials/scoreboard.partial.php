<?php
if(!isset($common)){
    //this shouldn't happen but just making sure wherever this is included we have access to
    //what we need
    require_once(__DIR__ ."/../includes/common.inc.php");
}
if(isset($comp_id)) {
    //the trick here is to set $comp_id in the file that includes this script
    //before this script is ran.
    //this lets us have many scoreboards on 1 page just be alternating between setting $comp_id
    //and including a new instance of this script.
    //alternatively we could pass an array of comp_ids but then we'd have to adjust our processing
    $results = DBH::get_competitions_scoreboard([$comp_id]);
    $board = [];
    if(Common::get($results, "status", 400) == 200){
        $board = Common::get($results, "data", []);
        error_log(var_export($board, true));
    }
}
?>
<?php if(isset($board) && count($board) > 0):?>
<div class="container-fluid">
    <div class="list-group">
        <div class="list-group-item">
            <div class="row">
                <div class="col-4">
                    User
                </div>
                <div class="col-4">
                    Score
                </div>
                <div class="col-4">
                    Rank
                </div>
            </div>
        </div>
        <?php foreach($board as $result):?>
        <div class="list-group-item">
            <div class="row">
                <div class="col-4">
                    <?php echo "user#" . Common::get($result, "user_id", -1);?>
                </div>
                <div class="col-4">
                    <?php echo Common::get($result, "wins", 0);?>
                </div>
                <div class="col-4">
                    <?php echo Common::get($result, "rnk", 0);?>
                </div>
            </div>
        </div>
        <?php endforeach;?>
    </div>
</div>
<?php endif;?>
