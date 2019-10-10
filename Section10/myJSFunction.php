<html>
<head>
<script>
function myValidation(inputEle, checkValue){
	let name = inputEle.name;
	let vid = "validation." + name;
	let vele = document.getElementById(vid);
	let value = inputEle.value;
	if(value == checkValue){
		if(vele){
			vele.remove();
		}
	}
	else{
		if(!vele){
			vele = document.createElement("span");
			vele.id = vid;
			document.body.appendChild(vele);
		}
		vele.innerText = name + " has an invalid value";
	}
	return false;
}
</script>
</head>

<body>
<form onsubmit="return false;">
<input type="text" name="test" placeholder="Trying it out" 
	onchange="myValidation(this, 'hi');"/>
<input type="number" name="number" onchange="console.log('onchange');myValidation(this, 15);"
onblur="console.log('blur');"
oninput="console.log('oninput');"

/>
</form>
</body>
</html>
