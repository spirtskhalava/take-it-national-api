echo 'START ----> Configuring nginx <----'

sudo [ -d /etc/nginx/sites-enabled ] && rm -r /etc/nginx/sites-enabled
sudo [ -d /etc/nginx/sites-available ] && rm -r /etc/nginx/sites-available

sudo ln -s /var/www/.box/nginx/ /etc/nginx/sites-enabled
sudo ln -s /var/www/.box/nginx/ /etc/nginx/sites-available

sudo service nginx restart

echo 'END ----> Configuring nginx <----'