<html>
<head>
	<script>
	//intro to console and why float to int comparison is dangerout
	//to open console it should be ctrl+F12, then there should be a tab labeled 'console'
	var myFloat = 0;
	for(var i = 0; i < 10; i++){
		myFloat += 0.1;
	}
	var guess = prompt("What value do you expect?");
	console.log("My float: " + myFloat);
	console.log("My float is equal to 1: " + (myFloat == 1));
	console.log("My float is equal to guess[" + guess +"] :" + (myFloat == guess));
	</script>
</head>
<body>
	<p id="myPara">Just showing that we loaded something...</p>
</body>
</html>
