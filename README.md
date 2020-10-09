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
It'll also setup a 2GB Swapfile with an initally swappiness of 10. \
If there are still memory issues, a further MySQL optimization has been included below. \

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
