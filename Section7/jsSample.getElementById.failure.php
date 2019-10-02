<html>
<head>
	<script>
		//showing order of execution
		var myPara = document.getElementById("myPara");
		console.log(myPara);//what value do you get?
		//value should be null since the script runs before
		//the html tag has been applied to the DOM, so the script doesn't find it
	</script>
</head>
<body>
	<p id="myPara">Just showing that we loaded something...</p>
</body>
</html>
