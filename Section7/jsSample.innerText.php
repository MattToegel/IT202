<html>
<head>
	<script>
		function pageLoaded(){
			//showing order of execution
			var myPara = document.getElementbyId("myPara");
			myPara.innerText = "I was updated by javascript";
		}
	</script>
</head>
<!--Added onload function that's called when the html is loaded-->
<body onload="pageLoaded();">
	<p id="myPara">Just showing that we loaded something...</p>
</body>
</html>
