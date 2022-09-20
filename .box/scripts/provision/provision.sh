echo 'START ----> Running provision script <----'

echo '---> Disable interactivity when running apt-get'
sudo sed -i 's/\(dpkg-preconfigure\) --apt/\1 --frontend=noninteractive --apt/' /etc/apt/apt.conf.d/70debconf

echo '---> Set timezone'
sudo timedatectl set-timezone Europe/Sarajevo

sudo apt-get update

echo '---> Install zip and unzip'
sudo apt-get -y install zip
sudo apt-get -y install unzip

echo '---> Add alternative repositories in order to be able to install PHP 7.2'
sudo add-apt-repository -y ppa:ondrej/php
sudo add-apt-repository -y ppa:ondrej/nginx

sudo apt-get update

echo '---> Install PHP 7.2 with extensions'
sudo apt-get -y install php7.2-fpm
sudo apt-get -y install php7.2-common
sudo apt-get -y install php7.2-cgi
sudo apt-get -y install php7.2-mysql
sudo apt-get -y install php7.2-mbstring
sudo apt-get -y install php7.2-curl
sudo apt-get -y install php7.2-gd
sudo apt-get -y install php7.2-xml
sudo apt-get -y install php7.2-xmlrpc
sudo apt-get -y install php7.2-zip

echo '---> Install nginx'
sudo apt-get -y install nginx

echo '---> Delete default nginx configurations'
sudo [ -d /etc/nginx/sites-enabled ] && rm -r /etc/nginx/sites-enabled
sudo [ -d /etc/nginx/sites-available ] && rm -r /etc/nginx/sites-available
sudo [ -d /var/www/html ] && rm -r /var/www/html

echo '---> Install MySQL'
sudo debconf-set-selections <<< 'mysql-apt-config mysql-apt-config/repo-url string http://repo.mysql.com/apt'
sudo debconf-set-selections <<< 'mysql-apt-config mysql-apt-config/dmr-warning note'
sudo debconf-set-selections <<< 'mysql-apt-config mysql-apt-config/preview-component string'
sudo debconf-set-selections <<< 'mysql-apt-config mysql-apt-config/repo-codename select bionic'
sudo debconf-set-selections <<< 'mysql-apt-config mysql-apt-config/repo-distro select ubuntu'
sudo debconf-set-selections <<< 'mysql-apt-config mysql-apt-config/select-preview select Disabled'
sudo debconf-set-selections <<< 'mysql-apt-config mysql-apt-config/select-server select mysql-5.7'
sudo debconf-set-selections <<< 'mysql-apt-config mysql-apt-config/tools-component string mysql-tools'
sudo debconf-set-selections <<< 'mysql-apt-config mysql-apt-config/select-tools select Enabled'

cd /var/www
wget https://dev.mysql.com/get/mysql-apt-config_0.8.12-1_all.deb
sudo DEBIAN_FRONTEND=noninteractive dpkg --install mysql-apt-config_0.8.12-1_all.deb
sudo DEBIAN_FRONTEND=noninteractive dpkg-reconfigure mysql-apt-config
sudo apt-key adv --keyserver keyserver.ubuntu.com --recv-keys 467B942D3A79BD29
sudo apt update
sudo apt-cache policy mysql-server
sudo DEBIAN_FRONTEND=noninteractive apt-get -y -f install mysql-client=5.7* mysql-community-server=5.7* mysql-server=5.7*
rm /var/www/mysql-apt-config_0.8.12-1_all.deb

echo '---> Creating MySQL database and user'
sudo mysql -e "CREATE DATABASE seoturbobooster CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
sudo mysql -e "CREATE USER 'seoturbobooster'@'%' IDENTIFIED BY 'seoturbobooster';"
sudo mysql -e "GRANT ALL PRIVILEGES ON *.* TO 'seoturbobooster'@'%' WITH GRANT OPTION;"
sudo mysql -e "FLUSH PRIVILEGES;"

echo '---> Install PHP xDebug extension'
sudo apt-get -y install php7.2-xdebug

echo '---> Add PHP xDebug extension configuration'
echo "xdebug.remote_enable = on" | sudo tee -a /etc/php/7.2/mods-available/xdebug.ini
echo "xdebug.remote_port = 9000" | sudo tee -a /etc/php/7.2/mods-available/xdebug.ini
echo "xdebug.remote_connect_back = on" | sudo tee -a /etc/php/7.2/mods-available/xdebug.ini
echo "xdebug.idekey = PHPSTORM" | sudo tee -a /etc/php/7.2/mods-available/xdebug.ini
echo "xdebug.show_error_trace = 1" | sudo tee -a /etc/php/7.2/mods-available/xdebug.ini
echo "xdebug.remote_autostart = 0" | sudo tee -a /etc/php/7.2/mods-available/xdebug.ini

echo 'END ----> Running provision script <----'
