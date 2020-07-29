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