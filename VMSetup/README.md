# Setup your own Virtual Machine
You may use this script to setup your own virtual machine.
I have compiled some online resources to get a basic lamp stack configured in a single script.
For those of you that may not know what LAMP stands for it's Linux, Apache, MySQL, and PHP.

## Setup
1. You must run the script as sudo
```sudo ./lamp.sh```
2. You'll be prompted for some info along the way
	1. Set a root DB password (don't forget it as it's not saved and you won't be using it to login)
		1. Make sure this is a secure password and do not leave blank
	2. It may ask you if you want to use apache2 or lighttpd for phpmyadmin
		1. Choose apache2
	3. It'll ask if you want to configure phpmyadmin with dbconfig-common
		1. Choose Yes (easiest solution)
	4. It'll ask for a password for phpmyadmin DB user
		1. Leave it blank and just continue (this will generate a random password)
	5. It'll try to detect the connected user to setup the rest of the settings for that user, by default it should correctly detect whoever is ssh'ed into the server.
		1. Type 'y' if the user is correct, otherwise type in the correct username
	6. Finally, it'll echo out the user's DB password. Save this as it won't show again and you'd need the root user/password to reset this.
3. Once the setup is done you can access your site (assuming no firewall blocking) at the following locations:
	1. apache: http://public_ip
	2. phpmyadmin: http://public_ip/phpmyadmin
		1. You can login with the username/password created from step 2.6
	3. Your mysql details will be
		1. host = localhost
		2. db = user from step 2.5
		3. user = user from step 2.5
		4. password = password from step 2.6
4. Your user should already have access to the /var/www/html folder and subdirectories/files.
5. Git is already installed for your convience

## Troubleshooting
* I forgot my root password
	* You'll probably have to redo the install
	* `sudo apt-get remove mysql-* phpmyadmin`
	* `sudo apt-get purge mysql-* phpmyadmin`
	* `sudo ./lamp.sh`
* I forgot my user's password
	* If you have root DB access (i.e., you remember the root password) you can login and update your user's password 
		```mysql
		mysql ALTER USER 'user-name'@'localhost' IDENTIFIED BY 'NEW_USER_PASSWORD';
		FLUSH PRIVILEGES;
		```
	* see the first issue if you don't have the password
* I ran the script twice
	* It should be ok as long as you didn't give different options when prompted and you'll probably get an SQL exception when it tries to create the user again (so it's safe to ignore the 'new' password that's generated since the update would have failed)
