<?php
$response = array("status"=>400, "message"=>"Error saving score");
if(isset($_POST["survey"])){
    require(__DIR__ . "/../lib/helpers.php");
    //PHP already converts the JSON to an array for us in this case
    //if it didn't we'd need to decode as shown below
    //$survey = json_decode($_POST["survey"], true);
    $survey = $_POST["survey"];

    //create survey
    $db = getDB();
    $query = "INSERT INTO tfp_surveys (title, description, visibility, user_id) VALUES (:t, :d, :v, :u)";
    $stmt = $db->prepare($query);
    $stmt->execute([
        ":t"=>$survey["title"],
        ":d" => "TBD",
        ":v" => 0,
        ":u"=>get_user_id()
    ]);
    $survey_id = $db->lastInsertId();
    $test = "";
    foreach($survey["questions"] as $q){
        $test .= $q["question"];

        $query = "INSERT INTO tfp_questions (question, survey_id) values (:q, :s)";
        $stmt = $db->prepare($query);
        $stmt->execute([
            ":q"=>$q["question"],
            ":s"=>$survey_id]
        );
        $question_id = $db->lastInsertId();

        foreach($q["answers"] as $a){
            $test .= $a["answer"];
            $query = "INSERT INTO tfp_answers (answer, question_id) values (:a, :q)";
            $stmt = $db->prepare($query);
            $stmt->execute([
                ":q"=>$question_id,
                ":a"=>$a["answer"]
             ]);
        }
    }
    echo $test;
   // echo var_export($_POST["survey"], true);
}