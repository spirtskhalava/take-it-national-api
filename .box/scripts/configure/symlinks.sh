echo 'START ----> Configuring symlinks <----'

[ -f ~/.bash_aliases ] && sudo unlink ~/.bash_aliases
sudo ln -s /var/www/.box/bash/.bash_aliases ~/.bash_aliases

source ~/.bashrc
source ~/.bash_aliases

echo 'END ----> Configuring symlinks <----'
