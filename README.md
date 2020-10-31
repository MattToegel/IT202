# Server Setup Scripts

# lampv2.sh
This is a run-once script (if you run it more than once you'll lose the passwords that get generated for you and will need to redo your whole VM)\
It'll install the following programs and do some performance tuning for small systems (originally targetted at GCE f1 micro instance). 
- MySQL
- Apache2
- git
- nano
- PHP 7.4 and configure FPM
- PHP My Admin

It'll only ask for 1 prompt which is to confirm if the detected user is the correct user you want to setup. \
The user must exist before this script is ran. \
It'll allow them to use a public_html folder in their home directory to host their website content and will generate a DB account/password for them to use locally\
The base installation seems to use approximately ~400MB / 576MB RAM. \
It'll also setup a 2GB Swapfile with an initial swappiness of 10. \
If there are still memory issues, a further MySQL optimization has been included below. \

# lampv3.sh
Updated version based on #2 that attempts to fix some mysql configs to prevent or help mitigate the .idb files from growing too quickly and eating up HD space. \n
Also has some settings to update the log cleaning policy to help keep disk space lower

# my.cnf
This file does a bit more extra tuning of the MySQL install. It's geared towards light traffic and low memory. \
To enable the features run the following commands \
``` cp /etc/mysql/my.cnf ~/my.cnf.backup ``` \
``` wget [raw url to the my.cnf file in this branch] ``` \
``` sudo mv my.cnf /etc/mysql/my.cnf ``` \
``` sudo systemctl restart mysql ``` 

Then you should see the changes take affect. \
With this configuration at idle on a GCE f1 micro instance the memory should be around 230MB / 576MB \
I'll update with further optimizations as necessary. 

# patch.sh
This is to attempt to fix issues from lampv2.sh where some mysql tuning may not be correct and causes the innoDB disk files to grow quicker than expected. Since these files don't normally shrink eventually the VM would run out of storage space. And you can't delete these otherwise your table data is gone. \
Since it's a single file in our setup we also couldn't take advantage of ```sql optimize table name``` to try to clean up the files. \
Due to this we'll rebuild our mysql install (but we also need to redo a few other parts too since the uninstall will mess up other areas). \
To use this script run the following commands: \n
- ```bash wget https://raw.githubusercontent.com/MattToegel/IT202/VMSetup/patch.sh```
- ```bash sudo chmod +x patch.sh```
- ```bash sudo ./patch.sh```
\
This script will do the following, please be sure you're ok with this:
- It'll ask two questions (if the user is the correct user, and if the initial steps were ok) Just hit y for both to confirm and hit enter.
- Attempt to fetch the existing db user, pass, and root pass from our file (otherwise prompts for it)
- Attempts to backup the existing mysql db data (if it fails it should exist so make sure you have enough disk space (about <2MB for our class))
- Stops MySQL
- Uninstalls MySQL related components and phpMyAdmin
- Deletes any residual data (once gone, it's gone)
- Reinstalls MySQL stuff
- Restores the root and current user with the previous passwords
- Fetches the latest my.cnf from this repo
- Backs up and overwrites the my.cnf and reloads mysql
- Attempts to fix debconfig, for some reason that may have gotten corrupted and caused the phpmyadmin to fail in earlier tests
- Reinstalls phpmyadmin and rebuilds the settings for it
- Ensures the previous php settings are set for apache (since uninstalling phpmyadmin seems to uninstall stuff we want :/)
- Finally attempts to restore the backup of your DB data from the generated backup.sql file
