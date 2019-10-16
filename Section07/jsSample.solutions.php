<html>
<head>
	<script>
	function pageLoaded(){
		//TODO solutions
		//Google/Explore how to create an element and add it to the DOM
		//create a div tag, add "added new element" as the text
		//add it to the DOM body
		//Method #1
		let myDiv = document.createElement('div');
		myDiv.innerText = "Created by JS";
		
		//Method #2 alternatively could have create text node and appended
		/*uncomment this to see
		let text = document.createTextNode("Created by JS");
                myDiv.appendChild(text);
		*/
		document.body.appendChild(myDiv);
		//Method #3 don't do this, but it could be done in "one line"
		document.body.appendChild(document.createElement('div')
			.appendChild(document.createTextNode("Try me")));
		//Method #4
		//document.getElementsByTagName('body')[0].innerHTML += "<div>Hello again</div>";	
	}
	</script>
</head>
<body onload="pageLoaded();">
	<!-- should not see this in code-->
	<div>Created by JS (not really)</div>
	<!--This is a comment -->
	<p id="myPara">Just showing that we loaded something...</p>
</body>
</html>
