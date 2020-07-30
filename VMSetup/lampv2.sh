#!/bin/bash
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

sudo fallocate -l 2G /swapfile
sudo chmod 600 /swapfile
sudo mkswap /swapfile
sudo swapon /swapfile
echo '/swapfile none swap sw 0 0' | sudo tee -a /etc/fstab

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
 
 #tuning mysql
 sudo echo "[mysqld]" > /etc/mysql/my.cnf
sudo echo "port = 3306" >> /etc/mysql/my.cnf
sudo echo "socket = /var/lib/mysql/mysql.sock" >> /etc/mysql/my.cnf
sudo echo "skip-locking" >> /etc/mysql/my.cnf
sudo echo "set-variable = key_buffer=16K" >> /etc/mysql/my.cnf
sudo echo "set-variable = max_allowed_packet=1M" >> /etc/mysql/my.cnf
sudo echo "set-variable = thread_stack=64K" >> /etc/mysql/my.cnf
sudo echo "set-variable = table_cache=4" >> /etc/mysql/my.cnf
sudo echo "set-variable = sort_buffer=64K" >> /etc/mysql/my.cnf
sudo echo "set-variable = net_buffer_length=2K" >> /etc/mysql/my.cnf

sudo systemctl restart mysql
 
 
 sudo apt install -y git nano
