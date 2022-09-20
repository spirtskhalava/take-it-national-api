echo 'START ----> Install mailcatcher <----'

sudo apt-get update
sudo apt-get install -y build-essential software-properties-common
sudo apt-get install -y libsqlite3-dev ruby-dev
sudo gem install mailcatcher --no-document

echo 'END ----> Install mailcatcher <----'
