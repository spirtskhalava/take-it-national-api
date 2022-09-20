echo 'START ----> Configuring message of the day <----'

[ -f /etc/motd ] && sudo unlink /etc/motd
sudo ln -s /var/www/.box/motd.txt /etc/motd

echo 'END ----> Configuring message of the day <----'
