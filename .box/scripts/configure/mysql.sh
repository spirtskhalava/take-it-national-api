echo 'START ----> Configuring MySQL <----'

echo 'Remove existing MySQL files'
[ -f /etc/mysql/mysql.conf.d/mysqld.cnf ] && sudo unlink /etc/mysql/mysql.conf.d/mysqld.cnf
[ -f /etc/mysql/conf.d/mysql.cnf ] && sudo unlink /etc/mysql/conf.d/mysql.cnf

echo 'Copy new MySQL config files'
sudo cp /var/www/.box/configurations/mysql/mysqld.cnf /etc/mysql/mysql.conf.d/mysqld.cnf
sudo cp /var/www/.box/configurations/mysql/mysql.cnf /etc/mysql/conf.d/mysql.cnf

echo 'Setting chmod for MySQL config files'
sudo chmod 0644 /etc/mysql/mysql.conf.d/mysqld.cnf
sudo chmod 0644 /etc/mysql/conf.d/mysql.cnf

echo "Restart MySQL service..."
sudo service mysql restart

echo 'END ----> Configuring MySQL <----'
