<?php require_once(__DIR__ . "/partials/nav.php"); ?>
<?php
if (!is_logged_in()) {
    flash("You must be logged in to take a survey");
    die(header("Location: login.php"));
}
?>

<?php
$survey_id = safe_get($_GET, "id", -1);
$results = [];
if ($survey_id > -1) {
    $query = "SELECT q.question, a.id, a.answer,  count(r.answer_id) as picks from tfp_questions q JOIN tfp_answers a ON q.id = a.question_id LEFT JOIN tfp_responses r on r.answer_id = a.id where q.survey_id = :sid group by a.id, a.answer, q.question";
    $db = getDB();
    $stmt = $db->prepare($query);
    $r = $stmt->execute([":sid" => $survey_id]);
    if ($r) {
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if (!$results) {
            $results = [];
        }
    }
}
?>

<table>
    <thead>
        <th>Question</th>
        <th>Answer</th>
        <th>Picks</th>
    </thead>
    <tbody>
        <?php if ($results && count($results) > 0) : ?>
            <?php foreach ($results as $r) : ?>
                <tr>
                    <td><?php echo safe_get($r, "question", ""); ?></td>
                    <td><?php echo safe_get($r, "answer", ""); ?></td>
                    <td><?php echo safe_get($r, "picks", ""); ?></td>
                </tr>
            <?php endforeach; ?>
        <?php else : ?>
            <tr>
                <td colspan="100%">No survey results...</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>