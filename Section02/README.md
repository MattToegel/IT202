#Section 2 - .gitignore and config.php

__Objective:__ Create a .gitignore to ignore all files called config.php so we don't mistakenly commit our credentials

Assumes you're already connected to AFS or VM.

1. Create a new files called _.gitignore_ (note: the . prefix turns it into a hidden files in linux)
	1. __nano .gitignore__
	2. on the first line type _config.php_
	3. Save and close the file
2. Create your config file
	1. __nano config.php__
	2. Insert the template below, then save and close the file
```php
<?php
$host="";
$database="";
$username="";
$password="";
?>
```
3. Config git properties (once per git location)
	1. __git config --global user.name "YouGithubUsername"__
	2. __git config --global user.email "YourGithubEmail"__
4. Commit the .gitignore
	1. __git add .gitignore__
	2. __git commit -m "adding .gitignore"__
	3. __git push origin master__
5. Test that the .gitignore is working
	1. __git add config.php__
	2. __git commit -m "testing .gitignore for config.php"__
	3. __git push origin master__
6. It should give you a message similar to everything already being up to date, nothing to commit, or commit was blocked by .gitignore
	1. Check your github account, you should not have config.php pushed
	2. If you committed your config.php first you may need to run the following command
		1. __git rm --cached config.php__
