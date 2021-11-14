<?php
//remember, API endpoints should only echo/output precisely what you want returned
//any other unexpected characters can break the handling of the response
$response = ["message" => "There was a problem saving your score"];
http_response_code(400);
$contentType = $_SERVER["CONTENT_TYPE"];
error_log("Content Type $contentType");
if ($contentType === "application/json") {
    $json = file_get_contents('php://input');
    $data = json_decode($json, true)["data"];
} else if ($contentType === "application/x-www-form-urlencoded") {
    $data = $_POST;
}

error_log(var_export($data, true));
if (isset($data["score"]) && isset($data["data"]) && isset($data["nonce"])) {
    session_start();
    $reject = false;
    if (!isset($_SESSION["nonce"]) || empty($_SESSION["nonce"]) || $data["nonce"] != $_SESSION["nonce"]) {
        error_log("Invalid nonce, possible duplicated post");
        $reject = true;
    }
    unset($_SESSION["nonce"]);
    if (!$reject) {
        require_once(__DIR__ . "/../../../lib/functions.php");
        $score = (int)se($data, "score", 0, false);
        $calced = 0;
        $data = $data["data"]; //anti-cheating
        $duck_value = (int)se($_SESSION, "duck_value", 10, false);
        $lastDate = null;
        $data_count = count($data);
        $duplicate_dates = 0;
        //anti-cheating checks (some TODOs may not be implemented and are there for example as to things you may need to consider)
        //1) calculate expected score vs passed score
        //2) ensure each record is older than the previous
        //3) check number of duplicate dates for records (it's only possible to have the same date for all records if only 1 shot was fired during the whole game)
        //4) TODO: ensure sufficient time elapsed between records
        //5) TODO: pass in location data and validate position trajectory (may be overkill)
        foreach ($data as $r) {
            $date = DateTime::createFromFormat("U", $r["ts"]);
            if (!$lastDate || $date >= $lastDate) {
                if ($date === $lastDate) {
                    $duplicate_dates++;
                }
                $lastData = $date;
                $ducks = (int)$r["d"];
                $calced += $ducks * $duck_value;
                if ($calced > $score) {
                    $reject = true;
                    error_log("Calced score is greater than provided score");
                    break;
                }
            } else {
                $reject = true;
                error_log("Invalid ts validation for game activity");
                break;
            }
        }
        if ($calced != $score) {
            $reject = true;
            error_log("Invalid calculated score");
        }
        if ($duplicate_dates >= $data_count) {
            error_log("Too many duplicate dates");
            $reject = true;
        }
        if (!$reject) {
            http_response_code(200);
            $user_id = get_user_id();
            save_score($score, $user_id, true);
            //purchase feature to pay to earn points (free play doesn't earn)
            if (se($_SESSION, "gen_points", false, false)) {
                $p = ceil($score / 100);
                unset($_SESSION["gen_points"]); //remove flag
                change_bills($p, "win", -1, get_account_balance(), "You won $p bills!");
                $response["message"] = "You won $p bills!";
            } else {
                $response["message"] = "Score Saved!";
            }
            error_log("Score of $score saved successfully for $user_id");
        } else {
            $response["message"] = "AntiCheat Detection Triggered. Score rejected.";
        }
    }
}
echo json_encode($response);
