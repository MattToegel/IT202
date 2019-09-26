#Section 6 - Working with $_GET

In this sample we'll explore the global $_GET variable.
Any query parameters provided in the url will get populated here.
A query parameter is a key/value pair that appears after the ? in a url.
Multiple pairs are separate by &.
For example: www.example.com/?parameter1=value1&parameter2=value2

1. First we'll create a new script for this sample
```nano handleRequestData.php```
2. Create your standard opening and closing php tags
```php
<?php

?>
```
3. On the line after the opening tag we're just going to output to the browser any and all GET parameters the script received.
```php
echo "<pre>" . var_export($_GET, true) . "</pre>";
```
	1. This line uses the <pre> html tags to preformat the output and tries to pretty-print the array details.
4. Save the file, then navigate to it from your browser. Note the output without any parameters.
5. Enter some query parameters as described above and navigate to the altered url. Note the output.
6. After the output add the following snippet.
```php
if(isset($_GET['name'])){
	echo "<br>Hello, " . $_GET['name'] . "<br>";
}
```  
	1. Here we're checking if a key 'name' exists in the $_GET array. If it does, we're saying hello to the value it refers to.
7. Save and navigate to the url but have the url contain handleRequestData.php**?name=John** then have that url load. Note the output. You should see the array contents plus "Hello, John".
8. After the previous snippet insert the following snippet.
```php
if(isset($_GET['number'])){
	$number = $_GET['number'];
	echo "<br>" . $number . " should be a number...";
	echo "<br>but it might not be<br>";
}
```
	1. In this sample we're checking if the key 'number' exists, if it is we're outputing text. It's important to note the data type will be a string, so just because you pass a number doesn't mean php is reading it as a number.
9. Continue exploring the usage of query parameters in the url.

##Homework
1. Implement adding two or more query parametes together. (i.e., Number1 and Number2) Do proper checks/handling in case the passed values aren't actually numbers.
2. Concatenate two or more parameter's values and echo them.
3. Try passing two parameters with the same name but different values, note and record the results.
4. Try passing a parameter with a value containg various special characters, note and record the results.
