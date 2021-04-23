<?php require_once(__DIR__ . "/partials/nav.php"); ?>
<?php
if (!is_logged_in()) {
    flash("You must be logged in to take a survey");
    die(header("Location: login.php"));
}
?>

<?php
$survey_id = safe_get($_GET, "id", -1);
if (isset($_POST["submit"])) {
    echo "<pre>" . var_export($_POST, true) . "</pre>";
    $index = 0;
    $params = [];

    $query = "INSERT INTO tfp_responses (question_id, answer_id, user_id, survey_id) VALUES ";
    foreach ($_POST as $key => $value) {
        if (is_numeric($key)) {
            if (count($params) > 0) {
                $query .= ",";
            }
            $query .= "(:q$index, :a$index, :uid, :sid)";
            $params[":q$index"] = $key;
            $params[":a$index"] = $value;
        }
        $index++;
    }
    $params[":uid"] = get_user_id();
    $params[":sid"] = $survey_id;
    /*echo "<pre>" . var_export($query, true) . "</pre>";
    echo "<pre>" . var_export($params, true) . "</pre>";*/
    $db = getDB();
    $stmt = $db->prepare($query);
    $r = $stmt->execute($params);
    if($r){
        flash("Response recorded successfully! Thank you!");
        //TODO redirect somewhere
    }
    else{
        flash("Error Recording response: " . var_export($stmt->errorInfo(), true));
    }
}
?>

<?php
$results = [];
if ($survey_id > -1) {
    $query = "SELECT q.id, s.title, q.question, q.id as qid, a.answer, a.id as aid from tfp_surveys as s JOIN  tfp_questions as q on s.id = q.survey_id JOIN tfp_answers as a ON q.id = a.question_id where s.id = :sid";
    $db = getDB();
    $stmt = $db->prepare($query);
    $r = $stmt->execute([":sid" => $survey_id]);
    if ($r) {
        $results = $stmt->fetchAll(PDO::FETCH_GROUP); //PDO::FETCH_ASSOC);

        //echo "<pre>" . var_export($results, true) . "</pre>";
    } else {
        echo var_export($stmt->errorInfo(), true);
    }
}
?>


<div class="container-fluid">
    <div class="h3 text-center">
        <?php
        /*foreach($results as $r){
        safer_echo($r[0]["title"]);
        break;
    }*/
        safer_echo($results[array_key_first($results)][0]["title"]);
        ?>
    </div>
    <div class="d-flex justify-content-center">
        <form method="POST">
            <?php foreach ($results as $r) : ?>

                <div class="h4"><?php echo $r[array_key_first($r)]["question"]; ?></div>
                <div class="row">
                    <div class="col-12">
                        <div class="btn-group-vertical">
                            <?php foreach ($r as $a) : ?>
                                <input type="radio" class="btn-check" name="<?php echo $a["qid"]; ?>" id="<?php echo $a['aid']; ?>" value="<?php echo $a['aid']; ?>" autocomplete="off">
                                <label class="btn btn-secondary" for="<?php echo $a['aid']; ?>"><?php echo $a['answer']; ?></label>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
            <input type="submit" name="submit" value="Submit Answers" class="btn btn-primary" />
        </form>
    </div>
</div>

<?php require(__DIR__ . "/partials/flash.php");