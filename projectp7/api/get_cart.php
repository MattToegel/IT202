<?php 
$response = array("status" => 400, "message" => "Error getting cart");
require(__DIR__ . "/../lib/helpers.php");
$results = [];
if(is_logged_in()){
    $db = getDB();
    $query = "SELECT name, p.price, c.quantity, (p.price * c.quantity) as sub FROM tfp_cart c JOIN tfp_products p on c.product_id = p.id WHERE c.user_id = :uid";
    $stmt = $db->prepare($query);
    $r = $stmt->execute([
        ":uid"=>get_user_id()
    ]);
    if($r){
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $response["cart"] = $results;
        $response["status"] = 200;
        $response["message"] = "success";
    }
    else{
        $response["message"] = "Error: " . var_export($stmt->errorInfo(), true);
    }
}
echo json_encode($response);
?>