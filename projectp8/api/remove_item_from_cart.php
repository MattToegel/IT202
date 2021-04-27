<?php 
$response = array("status" => 400, "message" => "Error getting cart");
require(__DIR__ . "/../lib/helpers.php");
$results = [];
if(is_logged_in()){
    if(isset($_POST["cart_id"])){
        $db = getDB();
        $query = "DELETE from tfp_cart where id = :cid and user_id = :uid";
        $stmt = $db->prepare($query);
        $r = $stmt->execute([
            ":cid"=>$_POST["cart_id"],
            ":uid"=>get_user_id()
        ]);
        if($r){
            $response["status"] = 200;
            $response["message"] = "success";
        }
        else{
            $response["message"] = "Error: " . var_export($stmt->errorInfo(), true);
        }
    }
    else{
         $response["message"] = "Missing cart id";
    }
}
echo json_encode($response);
?>