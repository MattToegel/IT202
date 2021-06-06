<h1>Form Example</h1>
<h3><a href="index.php">Back</a></h3>
<form>
    <fieldset>
        <legend>Checkboxes</legend>
        <input type="checkbox" name="cb[]" value="val1" />
        <input type="checkbox" name="cb[]" value="val2" />
        <input type="checkbox" name="cb[]" value="val3" />
    </fieldset>
    <fieldset>
        <legend>Radio</legend>
        <input type="radio" name="r" value="val1" />
        <input type="radio" name="r" value="val2" />
        <input type="radio" name="r" value="val3" />
    </fieldset>
    <input type="submit" name="submit" value="Submit as GET" />
    <input type="submit" name="submit" formmethod="post" value="Submit as POST" />
</form>
<?php
echo '$_GET<br>';
if (isset($_GET["submit"])) {
    echo "<pre>" . var_export($_GET, true) . "</pre>";
}
echo '$_POST<br>';
if (isset($_POST["submit"])) {
    echo "<pre>" . var_export($_POST, true) . "</pre>";
}
echo '$_REQUEST<br>';
if (isset($_REQUEST["submit"])) {
    echo "<pre>" . var_export($_REQUEST, true) . "</pre>";
}

?>