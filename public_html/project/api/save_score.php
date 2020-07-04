<?php
$response = array("status"=>400, "message"=>"Error saving score");
if(isset($_POST["score"]) && isset($_POST["outcome"])) {
    //we're currently not doing anything with score yet
    //TODO fetch game state data to validate to deter cheating
    require(__DIR__ . "/../includes/common.inc.php");
    if (Common::is_logged_in(false)) {
        $outcome = Common::get($_POST, "outcome", "loss");

        if(Common::is_valid_game(($outcome=="win"))){
            //TODO based on game state calc XP

            //don't feed client data directly into our app/db
            //so we check it and assign a hard coded value
            if($outcome == "win"){
                $gameStatus = "win";
                $xp = 10;
                $points = 1;
            }
            else{
                $gameStatus = "loss";
                $xp = 1;//You learn from losing, right?
                $points = 0;//sorry gotta earn these
            }
            $_SESSION["outcome"] = $gameStatus;
            $user_id = Common::get_user_id();
            //give xp
            $xp_resp = DBH::addXP($user_id, $xp, $gameStatus);
            $xp_response = Common::get($xp_resp, "status", 400) == 200;
            if($points != 0) {//!= 0 lets us have the ability to lose points if it becomes a desired feature
                //only save if we have points to save

                //give points
                $p_resp = DBH::changePoints($user_id, $points);
                $points_response = Common::get($p_resp, "status", 400) == 200;
            }
            else{
                $points_response = true;//force to true since it's pointless to save a change of 0 to DB
                //and we don't want an invalid error recorded or triggered
            }
            if($xp_response && $points_response){
                $response["status"] = 200;
                $response["message"] = "Saved score";
            }
            else{
                if(!$xp_response){
                    error_log("Error saving xp" . var_export($xp_resp, true));
                }
                if(!$points_response){
                    error_log("Error saving points" . var_export($p_resp, true));
                }
            }
        }
        else{
            $response["message"] = "Invalid game";
        }
    }
    else{
        $response["message"] = "Not logged in";
    }
}
else{
    $response["message"] = "Invalid data";
}
echo json_encode($response);