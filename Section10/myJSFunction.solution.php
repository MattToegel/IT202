<html>
<head>
<script>
function isEmpty(v){
 return (v.trim().length == 0);
}
function isEmail(inputEle){
	if(inputEle.type == "email"){
		return inputEle.value.indexOf('@') > -1;
	}
	
	return true;
	
	
}
function myValidation(inputEle, inputName){
	var isValid = true;
	if(inputName.length > 0){
		let other = document.forms[0][inputName];
		let v1 = inputEle.value;
		let v2 = other.value;
		if(isEmpty(v1)){
			//do error empty
			isValid = false;
			console.log("Value 1 is empty");
		}
		if(isEmpty(v2)){
			//do error empty
			isValid = false;
			console.log("Value 2 is empty");
		}
		if(v1 != v2){
			//do error
			isValid = false;
			console.log("Value 1 and value 2 don't match");
		}
		if(!isEmail(inputEle)){
			//do error email
			isValid = false;
			console.log("First email input is not a valid email");
		}
		if(!isEmail(other)){
			//do error email
			isValid = false;
			console.log("Second email input is not a valid email");
		}
	}
	else{

		let v = inputEle.value;
		if(isEmpty(v)){
			isValid = false;
			console.log("Value is empty (else)");
		}
		if(!isEmail(inputEle)){
			isValid = false;
			console.log("Input is not valid email (else)");
		}
	}
	if(!isValid){
		alert("There's at least 1 problem");
	}

}
</script>
</head>

<body>
<form onsubmit="return false;">
<input type="email" name="email" placeholder="Email"
	onchange="myValidation(this, '');"
/>
<input type="email" name="confirmemail" placeholder="Confirm Email"
	onchange="myValidation(this,'email');"/>
<input type="password" name="password"
	onchange="myValidation(this, '');" />
<input type="password" name="confirmpassword"
	onchange="myValidation(this, 'password');"/>
<input type="text" name="username" placeholder="Username"
	onchange="myValidation(this, '');"/>
<select name="test1">
	<option value="0">0</option>
	<option value="1">1</option>
</select>
<select name="test2" onchange="myValidation(this,'test1');">
	<option value="0">0</option>
	<option value="1">1</option>
</select>
<input type="submit" value"Try it"/>
</form>
</body>
</html>
