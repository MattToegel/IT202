<?php
$response = array("status"=>400, "message"=>"Error saving score");
if(isset($_POST["score"]) && isset($_POST["outcome"])) {
    //we're currently not doing anything with score yet
    //TODO fetch game state data to validate to deter cheating
    require(__DIR__ . "/../includes/common.inc.php");
    if (Common::is_logged_in(false)) {
        $outcome = Common::get($_POST, "outcome", "loss");
        $data = Common::get($_POST, "data", []);
        $data = json_decode($data, true);
        $hashValidation = true;
        $healthValidation = true;
        $dmgValidation = true;
        $ph = null;
        $th = null;
        try {
            foreach ($data as $d) {
                $pt = json_decode($d[0], true);
                $et = json_decode($d[1], true);
                $phealth = (float)$pt["h"];
                $ehealth = (float)$et["h"];
                if (!isset($ph)) {
                    $ph = $phealth;
                }
                if (!isset($th)) {
                    $th = $ehealth;
                }
                if ($phealth > $ph || $ehealth > $th) {
                    $healthValidation = false;
                    error_log("Anti-cheat: invalid health, potential healing");
                    break;
                }
                $pdmg = (float)$pt["d"];
                $edmg = (float)$et["d"];
                if ($edmg < ($pdmg * .45)) {
                    error_log("Anti-cheat: Enemy tank damage nerfed less than allowable offset");
                    $dmgValidation = false;
                    break;
                }
                //TODO add other validations as necessary, got lazy to validate other status, but the above are the important ones
                $hash = $d[2];
                $check = md5($d[0] . $d[1]);

                error_log($hash . " vs " . $check);
                error_log(var_export($pt, true));
                error_log(var_export($et, true));
                if ($hash != $check) {
                    $hashValidation = false;
                    error_log("Anti-cheat: hash mismatch");
                    break;
                }
            }
        }
        catch(Exception $e){
            error_log("Validation failure");
            error_log($e->getMessage());
            $hashValidation = false;
            $healthValidation = false;
            $dmgValidation = false;
        }
        if(!$hashValidation || !$healthValidation || !$dmgValidation || count($data) < 2){
            $outcome = "loss";
        }
        error_log(var_export($data,true));
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