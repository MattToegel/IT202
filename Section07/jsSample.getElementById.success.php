<html>
<head>
	<script>
		function pageLoaded(){
			//showing order of execution
			var myPara = document.getElementById("myPara");
			console.log(myPara);//what value do you get?
			//unlike the failure version of this, you should
			//see data about your paragraph element
		}
	</script>
</head>
<!--Added onload function that's called when the html is loaded-->
<body onload="pageLoaded();">
	<p id="myPara">Just showing that we loaded something...</p>
</body>
</html>
