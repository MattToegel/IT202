<?php
include_once(__DIR__."/partials/header.partial.php");
if(Common::is_logged_in()) {
    $question_id = Common::get($_GET, "questionnaire_id", -1);
    $stats = [];
    if ($question_id > -1) {
        $result = DBH::get_stats_for_questionnaire();
        if(Common::get($result, "status", 400) == 200){
            $stats = Common::get($result, "data", []);
            error_log(var_export($stats, true));
        }
    }
}
?>