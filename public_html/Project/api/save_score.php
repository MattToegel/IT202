<?php
require_once(__DIR__ . "/../../../lib/functions.php");
error_log("save_score received data: " . var_export($_REQUEST, true));

//handle the potentially incoming post request
$score = (int)se($_POST, "score", 0, false);
$level = (int)se($_POST, "level", 0, false);
$rescued = (int)se($_POST, "rescued", 0, false);
//if data is valid pass it to save_score
$standalone_enabled = false; //I'm just blocking standalone since I'm using server-side score handling
if ($score > 0 && $level >= 1 && $standalone_enabled) {
    save_score($score, $level, $rescued);
}
//This demo will be setup to demonstrate a front end game
//vs one where the logic is mostly done on the back end
function save_score($score, $level, $rescued, $echo = true)
{
    $response = ["status" => 400, "message" => "Unhandled error"];
    http_response_code(400);
    if (session_status() != PHP_SESSION_ACTIVE) {
        session_start();
    }
    if (is_logged_in()) {
        //todo save
        $db = getDB();
        $stmt = $db->prepare("INSERT INTO RM_Scores(score, level, rescued, user_id) VALUES (:s, :l, :r, :uid)");
        try {
            $stmt->execute([":s" => $score, ":l" => $level, ":r" => $rescued, ":uid" => get_user_id()]);
            //give points
            if ($score >= 5000) {
                $gems = floor($score / 5000);
                give_gems($gems, "game-reward", -1, 
                get_user_account_id(), 
                "Game Stats: Score ($score) 
                Level ($level) Rescued ($rescued)");
            }
            $response["status"] = 200;
            $response["message"] = "Saved Score";
            http_response_code(200);
        } catch (PDOException $e) {
            error_log("Error saving score: " . var_export($e, true));
            $response["message"] = "Error saving score details";
        }
    } else {
        $response["message"] = "Not logged in";
        http_response_code(403);
    }
    if ($echo) {
        echo json_encode($response);
        die();
    }
    return $response;
}
