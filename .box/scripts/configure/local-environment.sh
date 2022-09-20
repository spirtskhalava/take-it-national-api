echo 'START ----> Configuring local environment <----'

echo '----> Go to the root of project'
cd /var/www

echo '----> Init local environment'
#[ -f /var/www/.env ] && rm /var/www/.env
#cp /var/www/environments/local/.env /var/www/.env

echo '----> Migrate database'
vendor/bin/phinx migrate -e local

echo 'END ----> Configuring local environment <----'
