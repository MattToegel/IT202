<?php
require_once(__DIR__ . "/../../../lib/functions.php");
session_start();
$user_id = get_user_id();
$response = ["message" => "There was a problem using any active effects"];
http_response_code(400);
$activeEffects = [];
if ($user_id > 0 && isset($_POST["nonce"])) {
    $nonce = se($_POST, "nonce", false, false);
    if ($nonce === se($_SESSION, "ae_nonce", null, false)) {

        $db = getDB();
        $stmt = $db->prepare("SELECT item_id FROM BGD_ActiveEffects WHERE user_id = :uid AND uses > 0");

        try {
            $stmt->execute([":uid" => $user_id]);
            $r = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if ($r) {
                $activeEffects = $r;
                $response["active"] = $activeEffects;
                error_log(var_export($activeEffects, true));
                http_response_code(200);
            }
        } catch (PDOException $e) {
            error_log("Fetch Active Effects Error: " . var_export($e->errorInfo, true));
            if ($e->errorInfo[1] === 3819) {
                http_response_code(404);
                $response["message"] = "You don't have any of this item remaining";
                $response["delete"] = $item_id; //tell the UI to remove the item from the grid
            }
        }
        $useActive = true; //for debugging

        if (count($activeEffects) > 0 && $useActive) {
            //use them
            //https://stackoverflow.com/a/60202587
            $query = "UPDATE BGD_ActiveEffects set uses = uses - 1 WHERE item_id in (";
            $query .= str_repeat("?,", count($activeEffects) - 1) . "?)";
            error_log("query " . $query);
            error_log("effects " . var_export($activeEffects, true));
            $stmt = $db->prepare($query);
            try {
                $stmt->execute(array_map(fn ($x) => $x["item_id"], $activeEffects));
                $response["active"] = $activeEffects;
                foreach ($activeEffects as $ae) {
                    $item_id = (int)se($ae, "item_id", 0, false);
                    if ($item_id === -1) {
                        $_SESSION["score_mod"] = 2;
                    } else if ($item_id == -2) {
                        $_SESSION["score_mod"] = 3;
                    }
                }
                http_response_code(200);
            } catch (PDOException $e) {
                error_log("Use Active Effects Error: " . var_export($e->errorInfo, true));
                if ($e->errorInfo[1] === 3819) {
                    http_response_code(404);
                    $response["message"] = "You don't have any of this item remaining";
                    //$response["delete"] = $item_id; //tell the UI to remove the item from the grid
                } else {
                    $response["message"] = "There was a problem using the activated items, please try again";
                }
                //if there was an error marking it as uses, clear the whole list
                $activeEffects = [];
                $response["active"] = $activeEffects;
            }
        }
    }
    unset($_SESSION["ae_nonce"]);
}
error_log("resp" . var_export($response, true));
echo json_encode($response);
