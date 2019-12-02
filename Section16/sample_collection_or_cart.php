<?php

function get_items(){
	require("config.php");
	$conn_string = "";
	$db = new PDO($conn_string, $username, $password);
	$query = "Select id, name, description from SomeTable LIMIT 10";
	$stmt = $db->prepare($query);
	$r = $stmt->execute();
	return $stmt->fetchAll();
}
?>

<?php //fetch 'em
$rows = get_items();
//check to add to cart
handle_add_to_cart();
?>

<?php foreach($rows as $index => $row):?>
	<div><?php echo $row["name"]; ?> - <?php echo $row['description'];?></div>
	<form id="form_<?php echo $row['id'];?>" method="POST">
		<input type="hidden" name="id" value="<?php echo $row['id'];?>"/>
		<input type="submit" value="Add Item to Cart?"/>
	</form>
<?php endforeach; ?>


<?php
function handle_add_to_cart(){
	if(isset($_POST['id'])){
		$id = $_POST['id'];
		require("config.php");
		$conn_string = "";
		session_start();
		$user = $_SESSION["user"];
		$user_id = $user["id"];
		$cart_id = $_SESSION["current_collection"];
		$db = new PDO($conn_string, $username, $password);
		$sel = "SELECT MAX(cart_id) as cart_id from Cart";
		$stmt = $db->prepare($sel);
		$r = $stmt->execute();
		$result = $stmt->fetch(PDO::FETCH_ASSOC);
		$cart_id = 1;
		if($result){
			$card_id = $result['cart_id'] + 1;
		}
		$query = "INSERT INTO Cart (product_id, user_id, cart_id) VALUES (:prod, :user, :cart)";
		$stmt = $db->prepare($query);
		$stmt->bindValue(":prod", $id);
		$stmt->bindValue(":user", $user_id);//todo get from session
		$stmt->bindValue(":cart", $cart_id);//todo determine cart id
		$r = $stmt->execute();//sample using execute to bind vs bindValue: array(":prod"=>$id, ":user"=>$user_id, etc));
		echo ($r > 0)"Successfully added $id to cart":"Failed to add $id to cart";
	}
}