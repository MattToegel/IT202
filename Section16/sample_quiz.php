<!--Combined samples all together, separate as needed-->
<?php
ini_set('display_errors',1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
function create_quiz(){
	if(isset($_POST["question"])){
		$questions = $_POST["question"];//should be an array/list for each input element with the same name
		echo var_export($questions, true);
		
		$type = "text";//TODO change/handle different question types
		$total = count($questions);
		require("config.php");
		$conn_string = "mysql:host=$host;dbname=$database;charset=utf8mb4";
		$db = new PDO($conn_string, $username, $password);
		$select = "SELECT MAX(quiz_id) AS quiz_id from Questions;";
		$stmt = $db->prepare($select);
		$r = $stmt->execute();
		$result = $stmt->fetch(PDO::FETCH_ASSOC);
		//echo "Quiz_id: " . var_export($result, true);
		$quiz_id = 1;//set a default in case table is empty, could be done in the select query too
		if($result){
			$quiz_id = $result["quiz_id"] + 1;
		}
		//there should be a question_id that's auto increment, this will map to answers later on
		$query = "INSERT INTO Questions (quiz_id, question, type, expected_answer) VALUES";
		//query building loop
		for($i = 0; $i < $total; $i++){
				$query .= "(:quiz_id, :question$i, :type$i, :expected_answer$i)";
				if($i < $total && ($i+1) != $total){
					//add a comma for multiple inserts in one
					$query .= ",";
				}
		}
		//echo "<pre>" . $query . "</pre>";
		$stmt = $db->prepare($query);
		$stmt->bindValue(":quiz_id", $quiz_id);
		//binding loop
		for($i = 0; $i < $total; $i++){
			$stmt->bindValue(":question$i", $questions[$i]);
			//add input for types, for this example just making everything text
			$stmt->bindValue(":type$i", $type);
			//TODO add expected answer if you want to do code based validations/answer grading
			$stmt->bindValue(":expected_answer$i", "");
		}
		$r = $stmt->execute();
		//if $r = 0 something didn't work correctly, otherwise we created our quiz
		if($r == 0){
			return var_export($stmt->errorInfo());
		}
		else{
			return "Successfully created quiz with id $quiz_id";
		}
	}
}
function submit_response(){
	if(isset($_POST["quiz_id"])){
		$quiz_id = $_POST["quiz_id"];
		$answers = $_POST["answer"];//should be an array/list
		//echo var_export($answers, true);
		
		$question_ids = $_POST["question_id"];//should be an array/list
		//echo var_export($question_ids, true);
		$total = count($answers);
		$user_id = 0;//TODO get user id from session
		$query = "INSERT INTO Answers (quiz_id, question_id, answer, user_id) VALUES";
		//dynamically prepare our query
		for($i = 0; $i < $total; $i++){
			$query .= "(:quiz_id, :question_id$i, :answer$i, :user_id)";
			if($i < $total && ($i+1) != $total){
				//add a comma for multiple inserts in one
				$query .= ",";
			}
		}
		//echo $query;
		require("config.php");
		$conn_string = "mysql:host=$host;dbname=$database;charset=utf8mb4";
		$db = new PDO($conn_string, $username, $password);
		$stmt = $db->prepare($query);
		$stmt->bindValue(":quiz_id", (int)$quiz_id);
		$stmt->bindValue(":user_id", $user_id);
		//loop again to bind our answer/question mapping
		for($i = 0; $i < $total; $i++){
			$stmt->bindValue(":question_id$i", $question_ids[$i]);
			$stmt->bindValue(":answer$i", $answers[$i]);
		}
		$r = $stmt->execute();
		//$r should be the number of rows inserted, 0 if there was an issue
		if($r == 0){
			return var_export($stmt->errorInfo(), true);
		}
		else{
			return "Successfully submitted answers!";
		}
	}
	
}
function get_questions($quiz_id){
	require("config.php");
	$conn_string = "mysql:host=$host;dbname=$database;charset=utf8mb4";
	$db = new PDO($conn_string, $username, $password);
	//table: Quiz [id, type, question, quiz_id]
	$query = "SELECT id, type, question, quiz_id from Questions where quiz_id = :id";
	$stmt = $db->prepare($query);
	$stmt->bindValue(":id", $quiz_id);
	$r = $stmt->execute();
	//get all questions for a particular quiz
	return $stmt->fetchAll();
}
?>
<?php
//try to submit answers if we have a POST variable submitted
echo submit_response();//this will show an error or success

//try to submit quiz creation form if we have a relevant POST variable submitted
echo create_quiz();
?>
<!-- form for creating a quiz-->
<form id="question_form" method="POST">
	<input type="text" name="question[]"/>
	<input type="text" name="question[]"/>
	<input type="text" name="question[]"/>
	<input type="text" name="question[]"/>
	<input type="text" name="question[]"/>
	<!-- dynamically append more input elements if you want more questions-->
	<input type="submit" value="Save Questionnaire"/>
</form>
<!--this can be a separate page that you link to with a quiz_id
i.e., quiz.php?quiz=1-->
<!--form for answering a quiz-->
<form id="answer_form" method="POST">
	<?php
		//try to load a quiz to generate the form
		$quiz_id = 1;//some default for testing
		if(isset($_GET["quiz"])){
			$quiz_id = $_GET["quiz"];
		}
		$questions = get_questions($quiz_id);
		//echo var_export($questions, true);
		if($questions):
	?>
		<input type="hidden" name="quiz_id" value="<?php echo $quiz_id;?>"/>
		<?php foreach($questions as $index => $question):?>
			<input name="question_id[]" type="hidden" value="<?php echo $question["id"]; ?>"/>
			<input name="answer[]" type="<?php echo $question["type"];?>" placeholder="<?php echo $question["question"];?>"/>
		<?php endforeach; ?>
	<?php endif ?>
	<input type="Submit" value="Submit Answers"/>
</form>
