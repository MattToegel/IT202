#Section4 - Tables and SQL
//TODO fill in later
Adding to initDB.php (assuming you're in your project directory [...ucid/public_html/IT20]

1. Open initDB.php to edit
	1. __nano initDB.php__
2. After successful connection create a new variable called __$query__
3. Enter the SQL string for creating your desired table, sample below:

```sql
create table if not exists `TestUsers`(
	`id` int auto_increment not null,
	`username` varchar(30) not null unique,
	`pin` int default 0,
	PRIMARY KEY (`id`)
) CHARACTER SET utf8 COLLATE utf8_general_ci
```
4. Using your previous new PDO variable prepare the string to be call
	1. Set the result to another variable (this prepares a statement to be used later)
```php
$stmt = $db->prepare($query);
```
5. In the future some may have an extra step, but for now we can immediately execute the query and get the response from the action.
```php
$r = $stmt->execute();
//just to see the value (should be 1)
echo "<br>" . $r . "<br>";
```
6. Test in your browser (should be web.njit.edu/~ucid/IT202/initDB.php) replace ucid with your id and IT202 with your repo name
7. You should see the previous echo messages in addition to "1" showing it successfully ran the query.
8. Login to web.njit.edu/mysql/phpMyAdmin
9. Check that the table was created
	1. May need to click the name of your DB to see a list (this is the icon with your ucid)
10. From that list, drop your newly created table
11. Note that it has been deleted.
12. Rerun the script by visiting its link from step #6
13. Refresh phpMyAdmin and you should see the table again.

##To Do (Classwork/Homework)
1. Create another variable with insert SQL
	1. You may use the UI from phpMyAdmin to craft your insert statement
```php
//Note backticks ` for table/column names and single-quotes ' for string value
//hint: we don't need to specify `id` since it's auto increment (note this in the next steps)
$insert_query = "INSERT INTO `TestUsers`(`username`, `pin`) VALUES ('JohnDoe', 1234)";
$stmt = $db->prepare($insert_query);
$r = $stmt->execute();
```
2. Run the script, record should be inserted.
3. Run the script again, due to unique constraint a duplicate username shouldn't be inserted
4. Replace values with :username and :pin
5. Use PDO binding to dynamically set these values to predefined $user, $pin vars
```php
$user = "JohnDoe";
$pin = 1234;
//DB Insert query
//Bind values
$r = $stmt->execute(...);//hint: something is required here
```
6. Create a new query to select and use binding for the where clause
```
$select_query = "select * from `TestUsers` where username = :username";
```
7. Output result
```php
//previous connection/query prep/etc
$result = $stmt->fetch();
echo "<br><pre>" . var_export($result, true) . "</pre><br>";
```

###Solutions:
Coming soon!
