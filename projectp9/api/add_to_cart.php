<?php
$response = array("status" => 400, "message" => "Error saving score");
if (isset($_POST["itemId"])) {
    require(__DIR__ . "/../lib/helpers.php");
    $itemId = $_POST["itemId"];

    $query = "SELECT name, price, quantity FROM tfp_products where id = :id";
    $db = getDB();
    $stmt = $db->prepare($query);
    $r = $stmt->execute([
        ":id" => $itemId
    ]);
    if ($r) {
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($result) {
            $quantity = (int)safe_get($result, "quantity", 0);
            $price = (int)safe_get($result, "price", 0);
            if ($quantity > 0 && $price > 0) {
                $query = "INSERT INTO tfp_cart(product_id, quantity, user_id, price) VALUES(:pid, 1, :uid, :p) ON DUPLICATE KEY UPDATE quantity = quantity + 1";
                $stmt = $db->prepare($query);
                $r = $stmt->execute([
                    ":pid" => $itemId,
                    ":uid" => get_user_id(),
                    ":p" => $price
                ]);
                if ($r) {
                    $response["status"] = 200;
                    $response["message"] = "Added 1 " . $result["name"] . " to your cart";
                } else {
                    $response["message"] = "Error occurred: " . var_export($stmt->errorInfo(), true);
                }
            } else {
                //TODO add error output
            }
        }
    }
}
echo json_encode($response);
