<html>
	<head>
		<title>My Project - Register</title>
		<script>
			function myFunction(form){
				let num1 = 1.0;
				let num2 = 0.0;
				for(let i = 0; i < 10; i++){
					num2 += 0.1;
				}
				
				console.log("Numbers: ", num1, " + ", num2);
				console.log("They are equals?", num1==num2);
			}
			myFunction(1);
			function findFormsOnLoad(){
				let myForm = document.forms.regform;
				let mySameForm = document.getElementById("myForm");
				console.log("Form by name", myForm);
				console.log("Form by id", mySameForm);
			}
		</script>
	</head>
	<body onload="findFormsOnLoad();">
		<!-- This is how you comment -->
		<form name="sampleForm" id="myForm" method="GET">
			
			
			<input name="textInput" type="text"/>
			<input name="checkboxInput" type="checkbox"/>
			<input name="radioInput" type="radio"/>
			<input name="emailInput" type="email"/>
			<input name="passwordInput" type="password"/>
			<input name="submitInput" type="submit"/>
			<input name="dateInput" type="date"/>
			<input name="numberInput" type="number"/>
			<select name="selectInput">
				<option value="-1">I see you</option>
				<option value="0">I kind of see you?</option>
				<option value="1">It's gone</option>
			</select>
			<textarea name="textAreaInput">
			</textarea>
			<hr>
			<input name="hiddenInput" type="hidden" value="something not so secret"/>
			<hr>
		</form>
	</body>
</html>
<?php 
ini_set('display_errors',1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
echo '$_GET:<br>';
echo "<pre>" . var_export($_GET, true) . "</pre>";
?>