#Section 3 - Getting Started with PDO
 
1. Create a new file for running our sample connection
	1. __nano initDB.php__
	2. Insert part1 of the test
```php
<?php
//TODO add error handling

//load the config from the same directory
require('config.php');
echo "Loaded host: " . $host;

?>
```

2. Navigate to the file in your browser assuming the default setup
	1. web.njit.edu/~yourucid/IT202/initDB.php
	2. You should see your host echoed if everything was done correctly
3. Proceed with part2, testing the connection and config details

```php
//this is the same file but with extra code
<?php
//TODO add error handling

//load the config from the same directory
require('config.php');
echo "Loaded host: " . $host;

//new lines below
try{
	$conn_string = "mysql:host=$host;dbname=$database;charset=utf8mb4";
	$db = new PDO($conn_string, $username, $password);
	echo "Connected";
}
catch(Exception $e){
	echo $e->getMessage();
	echo "Something went wrong";
}
```
4. Navigate to the same file as step #2.
	1. You should see "Connected", if not add the following lines under "//TODO add error handling"
```php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
```
5. Rerun the file and see what errors are shown.
	1. Check syntax/typos
	2. Check tags
	3. Check __$username__ and __$password__ are the same that log you into web.njit.edu/mysql/phpMyAdmin
		1. If you need to reset your password: mypassword.njit.edu/db
	4. Ensure items are under public_html/IT202 (or public_html/YourRepoFolderName)
6. Resolve errors and run again.
