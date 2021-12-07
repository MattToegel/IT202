<?php
require_once(__DIR__ . "/../../partials/nav.php");
is_logged_in(true);
?>
<?php
$response = ["message" => "There was a problem completing your purchase"];
http_response_code(400);
error_log("req: " . var_export($_POST, true));
if (isset($_POST["item_id"]) && isset($_POST["stock"]) && isset($_POST["unit_price"])) {
    $user_id = get_user_id();
    $item_id = (int)se($_POST, "item_id", 0, false);
    $stock = (int)se($_POST, "stock", 0, false);
    $unit_price = (int)se($_POST, "unit_price", 0, false);
    $isValid = true;
    $errors = [];
    if ($user_id <= 0) {
        //invald user
        array_push($errors, "Invalid user");
        $isValid = false;
    }
    if ($stock <= 0) {
        //invalid quantity
        array_push($errors, "Invalid quantity");
        $isValid = false;
    }
    if($isValid){
        //get true price from DB, don't trust the client
        $db = getDB();
        $stmt = $db->prepare("SELECT name,unit_price FROM Products where id = :id");
        $name = "";
        try {
            $stmt->execute([":id" => $item_id]);
            $r = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($r) {
                $unit_price = (int)se($r, "unit_price", 0, false);
                $name = se($r, "name", "", false);
                $stmt = $db->prepare("INSERT INTO Cart(product_id, user_id, desired_quantity, unit_price) VALUES (:p,:u,:q,:c) ON DUPLICATE KEY UPDATE desired_quantity = desired_quantity + :q, unit_price = :c");
                try{
                    $stmt->execute([":p"=>$item_id, ":u"=>$user_id, ":q"=>$stock, ":c"=>$unit_price]);
               
				}
				catch(PDOException $e){
					$v = var_export($e, true);
					flash($v);
					error_log($v);
				} 
			}
		}
        catch(PDOException $e){
            $v = var_export($e, true);
            flash($v);
            error_log($v);
        }
	}		
     else {
        $response["message"] = join("<br>", $errors);
    }
}
$base_query = "SELECT Cart.id, name, description, Cart.unit_price, quantity, image, (quantity * Cart.unit_price) as subtotal FROM Products JOIN Cart on Products.id = Cart.product_id WHERE Cart.user_id = :user_id AND stock > 0 AND quantity > 0 AND visibility > 0";
$db = getDB();
$stmt = $db->prepare($base_query);
$stmt->execute([":user_id"=>get_user_id()]);
$results = $stmt->fetchAll();

echo "<pre>".var_export($results,true)."</pre>";
?>