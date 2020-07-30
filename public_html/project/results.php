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
        //not really necessary if the above call is crafted well, but got lazy here
        $result = DBH::get_questionnaire_by_id($survey_id);
        if(Common::get($result, "status", 400) == 200){
            $survey = Common::get($result, "data", []);
            if(count($survey) == 1){
                $survey = $survey[0];
            }
            error_log(var_export($survey, true));
        }
    }
}
?>
<h3><?php echo Common::get($survey, "name", "");?></h3>
<h5><?php echo Common::get($survey, "description", "");?></h5>
<div class="list-group">
    <div class="list-group-item">
    <?php foreach($stats as $question):?>
        <?php if(Common::get($question[0], "question_id", false) || Common::get($question[0], "answer_id", false)):?>
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

                    <input class="form-control" type="range" disabled min="0"
                           max="<?php echo $max;?>"
                           value="<?php echo Common::get($answer, "total", 0);?>"/>
                    Max: <?php echo $max;?>
                </div>
            <?php endforeach;?>
        </div>
        <?php endif;?>
    <?php endforeach;?>
    </div>
</div>
