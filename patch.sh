#!/bin/bash
set -e # terminates the script if an error occurs
echo "Fetching non-root user"
user=$(w -shf)
IFS=' '
read -a details <<< "$user"
sshuser=${details[0]}
echo "Found user: $sshuser"
read -p "Is this user correct? [Y / desired username]: " answer
if [[ "$answer" =~ ^([yY][eE][sS]|[yY])$ ]]
then
   echo "Using fetched user"
else
   sshuser=$answer
   echo "Using given user $sshuser"
fi
test=$(cat "/home/$sshuser/.emergency/.privatecreds")
test="${test//$'\n'/ }"

read -a arr <<< "$test"
db_root_password="${arr[1]}"
db_user="${arr[3]}"
db_user_password="${arr[5]}"
r=${#db_root_password}
u=${#db_user}
p=${#db_user_password}
if [[ $u -gt 1 ]] && [[ $r -gt 10 ]] && [[ $p -gt 10 ]]
then
  echo "Retrieved credentials, continuing"
else
  echo "Failed to fetch existing credentials"
  read -p "Enter the original root password: " db_root_password
  read -p "Enter the user for the db (likely ucid): " db_user
  read -p "Enter the original user password: " db_user_password
fi
answer='N'
read -p "Is everything correct?: " answer
if [[ "$answer" =~ ^([yY][eE][sS]|[yY])$ ]]
then
   echo "Proceeding with Patch..."
else
   echo "Terminating Script"
   exit 1
fi


echo "Backing up existing db"
mysqldump -u root -p$db_root_password --all-databases > backup.sql
if test -f "backup.sql"; then
 echo "Backup exists, continuing"
else
 echo "Backup failed, exiting"
 exit 2
fi
echo "Stopping mysql service"
sudo systemctl stop mysql
echo "Removing and purging existing mysql"
sudo apt-get remove --purge -y mysql-server mysql-common default-mysql-client mysql-client phpmyadmin
sudo apt-get autoremove -y
sudo apt-get autoclean -y
sudo rm -rf /etc/mysql
sudo rm -rf /var/lib/mysql


#install mysql
echo "Reinstalling mysql with given credentials"
export DEBIAN_FRONTEND="noninteractive"  
debconf-set-selections <<< "mysql-server mysql-server/root_password password $db_root_password"  
debconf-set-selections <<< "mysql-server mysql-server/root_password_again password $db_root_password"
sudo apt-get install -y mysql-server mysql-common mysql-client
sudo systemctl start mysql
echo "Fetching updated config"
wget https://raw.githubusercontent.com/MattToegel/IT202/VMSetup/my.cnf
echo "Backing up existing my.cnf"
sudo mv /etc/mysql/my.cnf /etc/mysql/my.cnf.backup
echo "Overwritting existing my.cnf"
sudo cat my.cnf > /etc/mysql/my.cnf

sudo systemctl restart mysql 
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
 DB_NAME="${db_user}"
 DB_USER="${db_user}"
 DB_PASS="${db_user_password}"
echo "Creating db user for ${db_user}"
createMysqlDbUser
echo "Restoring phpmyadmin"
sudo /usr/share/debconf/fix_db.pl
debconf-set-selections <<< "phpmyadmin phpmyadmin/dbconfig-install boolean true"
debconf-set-selections <<< "phpmyadmin phpmyadmin/app-password-confirm password $DB_PASS"
debconf-set-selections <<< "phpmyadmin phpmyadmin/mysql/admin-pass password $DB_PASS"
debconf-set-selections <<< "phpmyadmin phpmyadmin/mysql/app-pass password $DB_PASS"
debconf-set-selections <<< "phpmyadmin phpmyadmin/reconfigure-webserver multiselect apache2"
sudo apt install -y phpmyadmin
#disable default php for apache
sudo a2dismod php7.4

#install php fpm
sudo apt install php7.4-fpm

#enable modules
sudo a2enmod proxy_fcgi setenvif

#enable config /etc/apache2/conf-available/php7.4-fpm.conf
sudo a2enconf php7.4-fpm

#enable user directory
sudo a2enmod userdir
echo "Restoring backup"
mysql -u root -p$db_root_password < backup.sql
echo "Patch and restoration complete"
