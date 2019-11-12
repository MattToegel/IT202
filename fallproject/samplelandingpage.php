<?php
session_start();
?>
<html>
<head>
<script
  src="https://code.jquery.com/jquery-3.4.1.js"
  integrity="sha256-WpOohJOqMqqyKL9FccASB9O0KwACQJpFTUBLTYOVvVU="
  crossorigin="anonymous"></script>
<script>
$(document).ready(function(){
	var nav = ["Home", "About", "Logout"];
	let ul = $("<ul>");
	$("body").append(ul);
	nav.forEach(function(item, index){
			let ele = $("<a>");
			ele.attr("href", "?page="+item);
			ele.text(item);
			ul.append($("<li>").append(ele[0]));
	});
	
	 /*$.ajax({
			url: "ajax/get.php", 
			method: "POST", 
			data: {"type":"login", "username":"bob", "password":"1234"}, 
			success: function(result){
					console.log(result);
					alert(result);
					result = JSON.parse(result);
					alert("Status: " + result.status);
			},
			fail: function(jqXHR, textStatus){
				console.log(jqXHR, textStatus);
			}
		});*/
});
</script>
</head>
<body>
Hello there, <?php echo $_SESSION['user']['name'];?>
</body>
</html>
