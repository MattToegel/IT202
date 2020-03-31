#!/bin/bash  
   
 #######################################  
 # Bash script to install an LAMP stack in ubuntu  
 # Author: Subhash (serverkaka.com)  
 # Source: https://www.serverkaka.com/2018/12/install-lamp-stack-in-ubuntu-using-shell-script.html
 # Modified By: Matt Toegel 
  
 # Check if running as root  
 if [ "$(id -u)" != "0" ]; then  
   echo "This script must be run as root" 1>&2  
   exit 1  
 fi  
   
 # Ask value for mysql root password   
 read -s -p 'db_root_password [secretpasswd]: ' db_root_password  
 echo  
   
 # Update system  
 sudo apt-get update -y  
   
 ## Install APache  
 sudo apt-get install apache2 apache2-doc apache2-mpm-prefork apache2-utils libexpat1 ssl-cert -y  
   
 ## Install PHP  
 apt-get install php libapache2-mod-php php-mysql -y  
   
 # Install MySQL database server  
 export DEBIAN_FRONTEND="noninteractive"  
 debconf-set-selections <<< "mysql-server mysql-server/root_password password $db_root_password"  
 debconf-set-selections <<< "mysql-server mysql-server/root_password_again password $db_root_password"  
 apt-get install mysql-server -y  
   
 # Enabling Mod Rewrite  
 sudo a2enmod rewrite  
 sudo php5enmod mcrypt  
   
 ## Install PhpMyAdmin  
 sudo apt-get install phpmyadmin -y  
   
 ## Configure PhpMyAdmin  
 echo 'Include /etc/phpmyadmin/apache.conf' >> /etc/apache2/apache2.conf  
   
 # Set Permissions  
 sudo chown -R www-data:www-data /var/www  
   
 # Restart Apache  
 sudo service apache2 restart  

 #Matt's changes
 function generatePassword()
 {
    echo "$(openssl rand -base64 12)"
 }
 # Install git
 echo "Installing git"
 apt-get install git -y
 echo "Done installing git"
 # Find or Get user to assign to www-data group 
 echo "Fetching non-root user"
 user=$(w -shf)
 IFS=' '
 read -a details <<< "$user"
 sshuser=${details[0]}
 echo "Found user: $sshuser"
 read -p "Is this user correct? Y or desired username" answer
 if [[ "$answer" =~ ^([yY][eE][sS]|[yY])$ ]]
 then
    echo "Using fetched user"
 else
    sshuser=$answer
    echo "Using given user $sshuser"
 fi
 sudo usermod -a -G www-data $sshuser
 echo "Added $sshuser to www-data group"
 echo $(groups $sshuser)
 #setup MySQL DB and User (non-root)
 #function modified from https://stackoverflow.com/a/44343801
 function createMysqlDbUser()
 {
    SQL1="CREATE DATABASE IF NOT EXISTS ${DB_NAME};"
    SQL2="CREATE USER '${DB_USER}'@'%' IDENTIFIED BY '${DB_PASS}';"
    SQL3="GRANT ALL PRIVILEGES ON ${DB_NAME}.* TO '${DB_USER}'@'%';"
    SQL4="FLUSH PRIVILEGES;"

    if [ -f /root/.my.cnf ]; then
        $BIN_MYSQL -e "${SQL1}${SQL2}${SQL3}${SQL4}"
    else
        # If /root/.my.cnf doesn't exist then it'll ask for root password
        #_arrow "Please enter root user MySQL password!"
        #read rootPassword
        $BIN_MYSQL -h $DB_HOST -u root -p$db_root_password -e "${SQL1}${SQL2}${SQL3}${SQL4}"
    fi
 }
 #setup mysql vars
 BIN_MYSQL=$(which mysql)

 DB_HOST='localhost'
 DB_NAME=$sshuser
 DB_USER=$sshuser
 DB_PASS=$(generatePassword)
 echo "Creating DB and user for $sshuser"
 createMysqlDbUser
 echo "New user password is $DB_PASS";
 
