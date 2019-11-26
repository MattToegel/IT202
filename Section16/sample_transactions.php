<?php
function do_bank_action($account1 = '000000000000', $account2, $amountChange, $type = 'deposit'){
	require("config.php");
	$conn_string = "";
	$db = new PDO($conn_string, $username, $password);
	$a1total = 0;//TODO get total of account 1
	$a2total = 0;//TODO get total of account 2
	$query = "INSERT INTO Transactions(account_source, account_destination, amount, type, total)";
	$query .= "VALUES(:p1a1, :p1a2, :p1change, :type, :a1total),";
	$query .= "VALUES(:p2a1, :p2a2, :p2change, :type, :a2total)";
	
	$stmt = $db->prepare($query);
	$stmt->bindValue(":p1a1", $account1);
	$stmt->bindValue(":p1a2", $account2);
	$stmt->bindValue(":p1change", $amountChange);
	$stmt->bindValue(":type", $type);
	$stmt->bindValue(":a1total", $a1total);
	//flip data for other half of transaction
	$stmt->bindValue(":p2a1", $account2);
	$stmt->bindValue(":p2a2", $account1);
	$stmt->bindValue(":p2change", ($amountChange*-1));
	$stmt->bindValue(":type", $type);
	$stmt->bindValue(":a2total", $a2total);
	$result = $stmt->execute();
	return $result;
}
?>
<form method="POST">
	<input type="text" name="account1" placeholder="Account Number">
	<!-- If our sample is a transfer show other account field-->
	<?php if(isset($_GET['type']) && $_GET['type'] == 'transfer') : ?>
	<input type="text" name="account2" placeholder="Other Account Number">
	<?php endif; ?>
	
	<input type="number" name="amount" placeholder="$0.00"/>
	<input type="hidden" name="type" value="<?php echo $_GET['type'];?>"/>
	
	<!--Based on sample type change the submit button display-->
	<?php switch($_GET['type']):?>
	<?php case 'deposit': ?>
		<input type="submit" value="Deposit"/>
	<?php break; ?>
	<?php case 'withdraw': ?>
		<input type="submit" value="Withdraw"/>
	<?php break; ?>
	<?php case 'transfer': ?>
		<input type="submit" value="Transfer"/>
	<?php break; ?>
	<?php endswitch; ?>
</form>

<?php
if(isset($_POST['type']) && isset($_POST['account1']) && isset($_POST['amount')){
	$type = $_POST['type'];
	$amount = (int)$_POST['amount'];
	switch($type){
		case 'deposit':
			do_bank_action("000000000000", $_POST['account1'], ($amount * -1), $type);
			break;
		case 'withdraw':
			do_bank_action($_POST['account1'], "000000000000", ($amount * -1), $type);
			break;
		case 'transfer':
			//TODO figure it out
			break;
	}
}
?>