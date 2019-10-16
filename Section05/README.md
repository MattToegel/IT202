#Section 5 - Creating a migration/restore feature

1. Navigate to your project directory
2. Create a new folder for your sql files ```mkdir sql```
3. Go into your sql folder ```cd sql```
4. Create a new .sql folder using an easily sortable prefix (i.e., 1_create_table_testusers.sql)
	1. ```nano 1_create_table_testusers.sql```
5. Copy and paste the text between the double quotes from your "create table" query variable from Section4
```sql
create table if not exists `TestUsers`(
		`id` int auto_increment not null,
		`username` varchar(30) not null unique,
		`pin` int default 0,
		PRIMARY KEY (`id`)
		) CHARACTER SET utf8 COLLATE utf8_general_ci
```
6. You'll be creating new files for any structural changes in your database, do not use this for data manipulation like insert, update, delete as the sample doesn't handle extra parameters
7. Navigate back to initDB.php
	1. You may want to create a backup or create a new filename if you want to persist the previous example
		1. ```mv initDB.php initDB.php.backup```
		2. or ```nano initDB_new_name.php```
8. In the initDB.php file (or new file if you created a new one) add the necessary error logging from before, require config.php, and the connection string with try/catch block
9. We'll be adding a loop that reads all sql files from the sql directory and stores them into an associative array [Note: the comments are just included in case you want to uncomment the lines and see how they work]
```php
foreach(glob("sql/*.sql") as $filename){
		//echo $filename;
		$sql[$filename] = file_get_contents($filename);
		//echo $sql[$filename];
	}
```
10. Next we want to sort the array based on the key (this is why we prefixed our sql files with numbers as we want the scripts to run in a precise order later on)
```php
ksort($sql);//the parameter is the array
```
11. Next we connect to the database as normal
```php
$db = new PDO($conn_string, $username, $password);
```
12. Then we loop through the sorted array and attempt to run each of the loaded sql statements (and output any errors if they occur)
```php
foreach($sql as $key => $value){
		echo "<br>Running: " . $key;
		$stmt = $db->prepare($value);
		$result = $stmt->execute();
		$error = $stmt->errorInfo();
		if($error && $error[0] !== '00000'){
			echo "<br>Error:<pre>" . var_export($error,true) . "</pre><br>";
		}
		echo "<br>$key result: " . ($result>0?"Success":"Fail") . "<br>";
	}
```
13. After this should be the catch block you had before. If there's any exiting code comment it out for now.
14. Run it in the browser and we should see it load our create table script and show the appropriate output message.
15. As mentioned before, instead of editing the name script, we'll be adding new scripts for any structural changes.
Example, but we don't need to add this:
```sql
--we'd create a new script like 2_alter_table_testusers_add_column_lastlogin.sql
ALTER TABLE `TestUsers` add last_login datetime NOT NULL AFTER `pin`;
```
16. The purpose of this is so we can easily restore our database structure if it gets deleted/corrupted, or if we need to migrate to another MySQL instances easily. We can just copy the config.php, initDB.php, and sql folder contents and update the connection string, then run the initDB.php script.
