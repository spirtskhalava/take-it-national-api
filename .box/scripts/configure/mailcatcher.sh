echo 'START ----> Configuring mailcatcher <----'

sudo iptables -I INPUT -j ACCEPT
/usr/local/bin/mailcatcher --http-ip=0.0.0.0  >/dev/null 2>&1

echo 'END ----> Configuring mailcatcher <----'
