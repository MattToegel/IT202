<?php
function get_text_with_choices($post_id){
	require("config.php");
	$conn_string = "";
	$db = new PDO($conn_string, $username, $password);
	//Lookup post by id
	$query = "select title, text, parent, primary, secondary, third from `Posts` where id = :post_id";
	$stmt = $db->prepare($query);
	$r = $stmt->execute(array(":post_id"=>$post_id));
	$results = $stmt->fetch(PDO:FETCH_ASSOC);
	return $results;
}
?>

<?php
$post_id = 1;//default to load
if(isset($_POST['choice'])){
	//from form submit (a choice was made);
	$post_id = $_POST['choice'];
}
?>

<?php $row = get_text_with_choices($post_id);?>
<?php if($row): ?>
	<article>
		<h3><?php echo $row['title'];?></h3>
		<p><?php echo $row['text']; ?></p>
		<form method="POST">
			<input type="radio" name="choice" value="<?php echo $row['primary'];?>"/>
			<input type="radio" name="choice" value="<?php echo $row['secondary'];?>"/>
			<input type="radio" name="choice" value="<?php echo $row['third'];?>"/>
			<input type="submit" value="Pick"/>
		</form>
	</article>
<?php endif; ?>

