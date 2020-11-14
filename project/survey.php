<?php require_once(__DIR__ . "/partials/nav.php"); ?>
<?php
if (!is_logged_in()) {
    //this will redirect to login and kill the rest of this script (prevent it from executing)
    flash("You don't have permission to access this page");
    die(header("Location: login.php"));
}
?>
<?php
if (isset($_GET["id"])) {
    $sid = $_GET["id"];
    $db = getDB();
    $stmt = $db->prepare("SELECT q.id as GroupId, q.id as QuestionId, q.question, s.id as SurveyId, s.name as SurveyName, a.id as AnswerId, a.answer FROM F20_Surveys as s JOIN F20_Questions as q on s.id = q.survey_id JOIN F20_Answers as a on a.question_id = q.id WHERE :id not in (SELECT user_id from F20_Responses where user_id = :id and survey_id = :survey_id) and s.id = :survey_id");
    $r = $stmt->execute([":id" => get_user_id(), ":survey_id" => $sid]);
   $name = "";
	$questions = [];
	 if ($r) {
        $results = $stmt->fetchAll(PDO::FETCH_GROUP);
       // echo "<pre>" . var_export($results, true) . "</pre>";
       // echo "<br>";
        foreach ($results as $index=>$group) {
            foreach ($group as $details) {
		$answer = ["answerId"=>$details["AnswerId"], "answer"=>$details["answer"]];
		if(in_array($details["QuestionId"], $questions)){
			//array_push($questions[$details["QuestionId"]["answers"], ["answerId"=>$details["AnswerId"], "answer"=>$details["answer"]]);
		}
		else{
			$questions[$details["QuestionId"]]["question"] = $details["question"];
			$questions[$details["QuestionId"]]["answers"] = [];
		}
		array_push($questions[$details["QuestionId"]]["answers"], $answer);
                //echo "<br>" . $details["question"] . " " . $details["answer"] . "<br>";
            }
        }
			echo "<pre>" . var_export($questions, true) . "</pre>";

    }
    else {
        flash("There was a problem fetching the survey: " . var_export($stmt->errorInfo(), true), "danger");
    }
}
else {
    flash("Invalid survey, please try again", "warning");
    die(header("Location: " . getURL("surveys.php")));
}
?>
<?php require(__DIR__ . "/partials/flash.php"); ?>
