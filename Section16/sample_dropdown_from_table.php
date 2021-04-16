<?php
function get_dropdown_items(){
	require("config.php");
	$conn_string = "mysql:host=$host;dbname=$database;charset=utf8mb4";
	$db = new PDO($conn_string, $username, $password);
	
	$query = "SELECT DISTINCT id,name from SomeTable";
	$stmt = $db->prepare($query);
	$r = $stmt->execute();
	return $stmt->fetchAll();
}
?>


<?php
$items = get_dropdown_items();
?>
<select>
<?php foreach($items as $index=>$row):?>
	<option value="<?php echo $row["id"];?>">
		<?php echo $row['name'];?>
	</option>
<?php endforeach;?>
</select>	
