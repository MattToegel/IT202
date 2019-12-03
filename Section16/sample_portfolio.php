Portfolio [id, title, bio, user_id, visits]
Portfolio_Images[id, portfolio_id, image]

<?php
function get_portfolio($user_id){
	$query = "SELECT * from Portfolio as P, Portfolio_Images as PI WHERE
	P.user_id = :user_id AND
	P.id = PI.portfolio_id";
	//...
	return $results;
	
}
function increment_visit($user_id){
	//user_id is the person's portfolio, not the viewer
	$query = "UPDATE Portfolio as P SET visits += 1 WHERE P.user_id = :user_id";
}
?>
<?php
//happens on page load
increment_visit($_GET['user_id']);
?>
<?php $results = get_portfolio($_GET['user_id']);
	if($results):?>
	<grid>
	<?php foreach($results as $index=>$row):?>
		<!--HTML template code-->
		<img src="<?php echo $row['image'];?>"/>
	<?php endforeach;?>
	</grid>
<?php endif;?>


















