echo 'START ----> Configuring Composer <----'

echo '----> Upgrading Composer to latest version'
composer self-update

echo '----> Moving to project root'
cd /var/www

echo '----> Installing Composer packages'
composer install

echo 'END ----> Configuring Composer <----'
