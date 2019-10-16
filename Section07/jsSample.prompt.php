<html>
<head>
	<script>
	var variable;//original way to create a variable, scope of this may not always be what it seems
	let variable2;//newer way to declare a variable that maintains expected scope
	//Prompt sample showing another blocking method call that accepts user input
	variable = prompt("What's your name?");//this blocks until user clicks ok
	alert("Hello, " + variable);//this blocks until user clicks ok
	</script>
</head>
<body>
	<p id="myPara">Just showing that we loaded something...</p>
</body>
</html>