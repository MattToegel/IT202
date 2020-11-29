<?php
//since API is 100% server, we won't include navbar or flash
require_once(__DIR__ . "/../lib/helpers.php");
if (!is_logged_in()) {
    die(header(':', true, 403));
}

if(isset($_POST["itemId"])){
    $itemId = (int)$_POST["itemId"];
    $db = getDB();
    $stmt = $db->prepare("SELECT name, price from F20_Products where id = :id");
    $stmt->execute([":id"=>$itemId]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    if($result) {
        $name = $result["name"];
        $price = $result["price"];
        $stmt = $db->prepare("INSERT INTO F20_Cart (user_id, product_id, price) VALUES(:user_id, :product_id, :price) ON DUPLICATE KEY UPDATE quantity = quantity +1, price = :price");
        $r = $stmt->execute([":user_id"=>get_user_id(), ":product_id"=>$itemId, ":price"=>$price]);
        if ($r) {
            $response = ["status" => 200, "message" => "Added $name to cart"];
            echo json_encode($response);
            die();
        }
        else{
            $response = ["status" => 400, "message" => "There was an error adding $name to cart"];
            echo json_encode($response);
            die();
        }
    }
    else{
        $response = ["status" => 404, "error" => "Item $itemId not found"];
        echo json_encode($response);
        die();
    }
}
else{
    $response = ["status" => 400, "error" => "An unexpected error occurred, please try again"];
    echo json_encode($response);
    die();
}
?>
