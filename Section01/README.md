<h2>Section 1 - Hello World</h2>

1. Login to AFS
	1. __ssh yourucid@afsconnect1.njit.edu__
2. Run __pwd__
	1. You should be the last folder as your ucid
3. Move into your public_html
	1. __cd public_html__
	2. __pwd__ should how show your ucid/public_html
4. Create a new file with nano (alternative vi)
	1. __nano index.php__
	2. This should open up an empty editor if you didn't have index.php exist already
5. Add a sample to your index.php
```php
<?php
	echo "Hello World";
?>
```
6. Navigate to web.njit.edu/~yourucid/index.php (you need to specify it directly since the default apache config favors .html over .php)


##Getting Started with git
1. Go to Github.com
2. Create an account and create a new repositoty
3. Navigate to your public_html
4. Clone git repo
	1. __git clone https://github.com/GithubUsername/RepoName.git DesiredFolderName__
		1. Can ommit the DesiredFolderName and it'll default to a folder based on your repo name
5. Go into your repo folder
	1. __cd RepoFolderName__
6. Move your sample
	1. __mv ../index.php .__
		1. Moves up one directory to public_html and moves the index.php to current directory
7. Add and commit your index.php
	1. __git add index.php__
	2. __git commit -m "adding index.php"__
	3. __git push origin master__


###Steps if you cloned your repo outside of public_html (i.e., inside your home directory)
1. Navigate to your home directory
	1. __pwd__ should show your ucid as the last folder
2. Move the git repo folder into public_html
	1. __mv RepoFolderName public_html__
3. Check and fix permissions
	1. __cd RepoFolderName__
	2. __fs la .__
		1. Lists the permissions on the current directory
	3. If http doesn't show as "rl"
		1. __fs sa . http rl__
			1. Sets the permissions for the http user to "rl"
4. index.php should be accessible at web.njit.edu/~yourucid/RepoFolderName/index.php
