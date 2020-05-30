<form method="POST" onsubmit="return validate_passwords();">
	<label for="email">Email
	<input type="email" id="email" name="email"/>
	</label>
	<label for="p">Password
	<input type="password" id="p" name="password"/>
	</label>
	<label for="pc">Confirm Password
	<input type="password" id="pc" name="cpassword"/>
	</label>
	<input type="submit" name="register" value="Register"/>
</form>
<script>
function validate_passwords(){
	console.log(this);
	return form.password.value == form.cpassword.value;
}
</script>