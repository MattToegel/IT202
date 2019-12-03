<?php
function get_top_level_results(){
	require("config.php");
	$conn_string = "";
	$db = new PDO($conn_string, $username, $password);
	$query = "select title, text, parent, primary, secondary, third from `Posts` where parent is null OR parent == -1";
	$stmt = $db->prepare($query);
	$r = $stmt->execute();
	$results = $stmt->fetchAll();
	return $results;
}
function save_child($parent, $title, $text){
	require("config.php");
	$conn_string = "";
	$db = new PDO($conn_string, $username, $password);
	$query = "insert into Posts (title, text, parent) VALUES(:title, :text, :parent)";
	$stmt = $db->prepare($query);
	$r = $stmt->execute(array(":title"=>$title, ":text"=>$text, ":parent"=>$parent));
	return $r > 0;
}
?>

<?php foreach(get_top_level_results() as $index => $row): ?>
	<article>
		<h3><?php echo $row['title'];?></h3>
		<p><?php echo $row['text']; ?></p>
		<form method="post">
			<input type="text" name="title" placeholder="title"/>
			<input type="text" name="text" placeholder="text"/>
			<input type="hidden" name="parent" value="<?php echo $row['id'];?>"/>
			<input type="submit" value="Reply"/>
		</form>
	</article>
<?php endforeach; ?>

<?php
	if(isset($_POST['parent'])){
		if(save_child($_POST['parent'], $_POST['title'], $_POST['text'])){
			echo "Reply success";
		}
		else{
			echo "Reply failed";
		}
	}
