<?php
function view_item($id){
	require("config.php");
	$conn_string = "";
	$db = new PDO($conn_string, $username, $password);
	//Lookup item by id
	$query = "select id, data_one, data_two, data_three from `Sample` where id = :id";
	$stmt = $db->prepare($query);
	$r = $stmt->execute(array(":id"=>$id));
	$results = $stmt->fetch(PDO:FETCH_ASSOC);
	return $results;
}
function update_item($id, $one, $two, $three){
	require("config.php");
	$conn_string = "";
	$db = new PDO($conn_string, $username, $password);
	//Lookup post by id
	$query = "UPDATE Sample set data_one = :1, data_two = :2, data_three = :3 where id = :id";
	$stmt = $db->prepare($query);
	$r = $stmt->execute(array(
		":id"=>$id,
		":1"=>$one,
		":2"=>$two,
		":3"=>$three));
	return $r > 0;
}
?>

<?php
	//we form was submitted update table
	if(isset($_POST['id'])){
		update_item($_POST['id'], $_POST['data_one'], $_POST['data_two'], $_POST['data_three']);
	}
?>

<?php $row = view_item($id);?>
<?php if($row): ?>
	<!-- create a form to edit our item; pass in the current data to the respective values-->
	<form method="POST">
		<input type="hidden" name="id" value="<?php echo $row['id'];?>"/>
		<input type="text" name="data_one" value="<?php echo $row['data_one'];?>" />
		<input type="text" name="data_two" value="<?php echo $row['data_two'];?>" />
		<input type="text" name="data_three" value="<?php echo $row['data_three'];?>" />
		<input type="submit" value="Update"/>
	</form>
<?php endif; ?>

