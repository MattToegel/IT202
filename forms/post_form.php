<p>Run me in the browser from your server to try</p>
<form method="POST">
  <label>Type Something</label>
  <!-- we need the name attribute so our data gets submitted correctly-->
  <input name="text" />
  <!-- this is a special type that lets the form submit-->
  <input type="submit"/>
  <!-- this is a special type that clears the form-->
  <input type="reset"/>
</form>

<?php
//PHP has a few magic variables that'll be populated...magically
//we be using $_GET, $_POST, and $_REQUEST
//this will be include in each example to see how the data will be sent to the server

//only gets data from GET type submission
if(isset($_GET)){
 echo "GET: " . var_export($_GET, true); 
}
//only gets data from POST type submission
if(isset($_POST)){
 echo "POST: " . var_export($_POST, true); 
}
//doesn't care if it's GET or POST
if(isset($_REQUEST)){
 echo "REQUEST: " . var_export($_REQUEST, true); 
}
?>
