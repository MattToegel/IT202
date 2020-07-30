<?php
include_once(__DIR__."/partials/header.partial.php");
if(Common::is_logged_in()) {
    $survey_id = Common::get($_GET, "survey_id", -1);
    $stats = [];
    if ($survey_id > -1) {
        $result = DBH::get_stats_for_questionnaire($survey_id);
        if(Common::get($result, "status", 400) == 200){
            $stats = Common::get($result, "data", []);
            error_log(var_export($stats, true));
        }
    }
}
?>
<div class="list-group">
    <div class="list-group-item">
    <?php foreach($stats as $question):?>
        <?php echo Common::get($question[0], "question","");?>
        <div class="list-group">
            <?php
                $max = 0;
                foreach($question as $a){
                    if(Common::get($a, "group", 0) == 1){
                        $max = Common::get($a, "total", 0);
                        break;
                    }
                }
            ?>
            <?php foreach($question as $answer):?>
                <div class="list-group-item">
                    <?php echo Common::get($answer, "answer", "");?>
                    <?php echo Common::get($answer, "total", 0);?>
                    Max: <?php echo $max;?>
                </div>
            <?php endforeach;?>
        </div>
    <?php endforeach;?>
    </div>
</div>
