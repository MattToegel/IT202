<p>Run me in the browser from your server to try</p>
<form method="POST">
  <label for="myInput">Type Something</label>
  <!-- we need the name attribute so our data gets submitted correctly-->
  <input id="myInput" name="text" />
  <p>Are these concepts easy?</p>

  <label for="radio1">Yes</label>
  
  <input id="radio1" name="easy" type="radio" value="Y"/>
  <label for="radio2">No</label>
  <input id="radio2" name="easy" type="radio" value="N"/>
  
  <p>What languages are you learning?</p>
  <label for="lang1">PHP</label>
  <input id="lang1" type="checkbox" name="lang[]" value="PHP"/>
  <label for="lang2">HTML</label>
  <input id="lang2" type="checkbox" name="lang[]" value="HTML"/>
  <label for="lang3">CSS</label>
  <input id="lang3" type="checkbox" name="lang[]" value="CSS"/>
  <label for="lang4">JS</label>
  <input id="lang4" type="checkbox" name="lang[]" value="JS"/>
  
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
