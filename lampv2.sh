#!/bin/bash

#The MIT License (MIT)

#Copyright (c) 2020 Matt Toegel

#Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

#The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

#THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.

#Purpose: This script installs apache2, mysql, and phpmyadmin. It sets up some things automatically for the logged in user.
#It should only require 1 entry from the user to confirm that it found the correct ssh user.
#This script will also apply some configurations to apache2 and mysql to help reduce resource consumption on low mem/low cpu systems.
#It will generate a random password for mysql and creates 


#TL;DR: Not responsible if this borks your existing setup. This script was originally thrown together for my IT202 class to easily get a dev environment
#spun up on any debian based machine (i.e., Google F1 Micro Instance or AWS EC2 t2.micro, etc)
#If you're looking for the Heroku one this isn't it.
#It's not advised to run this script more than once unless you know what you're doing and know how to update things I didn't account for.
#THIS SCRIPT IS NOT FOR PRODUCTION

# Check if running as root  
if [ "$(id -u)" != "0" ]; then  
echo "This script must be run as root" 1>&2  
exit 1  
fi  
function generatePassword()
 {
    echo "$(openssl rand -base64 12)"
 }
#Update refs
sudo apt update -y
#Upgrade packages
sudo apt upgrade -y

#You may adjust this as necessary, 1G is fine, I upped it to 2G
sudo fallocate -l 2G /swapfile
sudo chmod 600 /swapfile
sudo mkswap /swapfile
sudo swapon /swapfile
echo '/swapfile none swap sw 0 0' | sudo tee -a /etc/fstab
sudo sysctl vm.swappiness=10

#install apache2
sudo apt install -y apache2 apache2-utils

#make sure apache2 is started
sudo systemctl start apache2

#enable apache2 on boot
sudo systemctl enable apache2

#install firewall
sudo apt install ufw -y

#open firewall for apache2 (may also need to do this through Provider's UI)
# if so keep these firewall rules in sync
sudo ufw allow in "Apache Full"

#set apache as doc root owner
sudo chown www-data:www-data /var/www/html/ -R

echo "ServerName localhost" > /etc/apache2/conf-available/servername.conf

#enable config file
sudo a2enconf servername.conf

#reload apache2
sudo systemctl reload apache2

#install php and required libs
sudo apt install -y php7.4 libapache2-mod-php7.4 php7.4-mysql php-common php7.4-cli php7.4-common php7.4-json php7.4-opcache php7.4-readline

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


#restart apache
sudo systemctl restart apache2


db_root_password=$(generatePassword)
#install mysql
export DEBIAN_FRONTEND="noninteractive"  
debconf-set-selections <<< "mysql-server mysql-server/root_password password $db_root_password"  
debconf-set-selections <<< "mysql-server mysql-server/root_password_again password $db_root_password"
sudo apt-get install -y mysql-server


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
 
   # Find or Get user to assign to www-data group 
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
 
#setup mysql vars
 BIN_MYSQL=$(which mysql)

 DB_HOST='localhost'
 DB_NAME=$sshuser
 DB_USER=$sshuser
 DB_PASS=$(generatePassword)
 

 
 createMysqlDbUser
 
 CON_STRING="mysql://$DB_USER:$DB_PASS@$DB_HOST:3306/$DB_NAME";
 echo "#DO NOT COMMIT TO REPOSITORY, DO NOT MAKE THIS PUBLIC" > /home/$sshuser/config.ini
 echo "MYSQL_CONNECTION=$CON_STRING" >> /home/$sshuser/config.ini
 sudo chmod 644 /home/$sshuser/config.ini
 
 sudo mkdir /home/$sshuser/.emergency
 sudo chmod 600 /home/$sshuser/.emergency
 echo "DBR: $db_root_password" > /home/$sshuser/.emergency/.privatecreds
 echo "DBUU: $DB_USER" >> /home/$sshuser/.emergency/.privatecreds
 echo "DBUP: $DB_PASS" >> /home/$sshuser/.emergency/.privatecreds
 sudo chmod 600 /home/$sshuser/.emergency/.privatecreds
 
 #setup local dir
 sudo mkdir /home/$sshuser/public_html
 sudo chmod -R 755 /home/$sshuser/public_html
 sudo chown -R $sshuser:www-data /home/$sshuser/public_html
 
 #tuning apache
 sudo echo "StartServers 1" > /etc/apache2/conf-available/low-res.conf
 sudo echo "MinSpareServers 1" >> /etc/apache2/conf-available/low-res.conf
 sudo echo "MaxSpareServers 5" >> /etc/apache2/conf-available/low-res.conf
 sudo echo "ServerLimit 64" >> /etc/apache2/conf-available/low-res.conf
 sudo echo "MaxClients 64" >> /etc/apache2/conf-available/low-res.conf
 sudo echo "MaxRequestsPerChild 4000" >> /etc/apache2/conf-available/low-res.conf
 
 #enable it
 sudo ln -s /etc/apache2/conf-available/low-res.conf /etc/apache2/conf-enabled/low-res.conf
 sudo systemctl restart apache2
 
 #tuning mysql for low mem environment
sudo echo "[mysqld]" > /etc/mysql/my.cnf
sudo echo "innodb_buffer_pool_size=5M" >> /etc/mysql/my.cnf
sudo echo "innodb_log_buffer_size=256K" >> /etc/mysql/my.cnf
sudo echo "max_connections=10" >> /etc/mysql/my.cnf
sudo echo "key_buffer_size=8" >> /etc/mysql/my.cnf
sudo echo "thread_cache_size=1" >> /etc/mysql/my.cnf
sudo echo "host_cache_size=0" >> /etc/mysql/my.cnf
sudo echo "innodb_ft_cache_size=1600000" >> /etc/mysql/my.cnf
sudo echo "innodb_ft_total_cache_size=32000000" >> /etc/mysql/my.cnf

sudo echo "# per thread or per operation settings" >> /etc/mysql/my.cnf
sudo echo "thread_stack=400K" >> /etc/mysql/my.cnf
sudo echo "sort_buffer_size=32K" >> /etc/mysql/my.cnf
sudo echo "read_buffer_size=8200" >> /etc/mysql/my.cnf
sudo echo "read_rnd_buffer_size=8200" >> /etc/mysql/my.cnf
sudo echo "max_heap_table_size=16K" >> /etc/mysql/my.cnf
sudo echo "tmp_table_size=1K" >> /etc/mysql/my.cnf
#updated to 1MB, default is 8MB
sudo echo "bulk_insert_buffer_size=1048576" >> /etc/mysql/my.cnf
sudo echo "join_buffer_size=128" >> /etc/mysql/my.cnf
sudo echo "net_buffer_length=1K" >> /etc/mysql/my.cnf
sudo echo "innodb_sort_buffer_size=64K" >> /etc/mysql/my.cnf

sudo echo "#settings that relate to the binary log (if enabled)" >> /etc/mysql/my.cnf
sudo echo "binlog_cache_size=4K" >> /etc/mysql/my.cnf

sudo systemctl restart mysql

debconf-set-selections <<< "phpmyadmin phpmyadmin/dbconfig-install boolean true"
debconf-set-selections <<< "phpmyadmin phpmyadmin/app-password-confirm password $DB_PASS"
debconf-set-selections <<< "phpmyadmin phpmyadmin/mysql/admin-pass password $DB_PASS"
debconf-set-selections <<< "phpmyadmin phpmyadmin/mysql/app-pass password $DB_PASS"
debconf-set-selections <<< "phpmyadmin phpmyadmin/reconfigure-webserver multiselect apache2"
sudo apt install -y phpmyadmin

 
 sudo apt install -y git nano
