# -*- mode: ruby -*-
# vi: set ft=ruby :

$vagrantBoxIp = "192.168.56.10"
$vagrantBoxName = "seoturbobooster"
$vagrantHosts = "api.seoturbobooster.local"

def is_windows?
  return ENV.key?('windir')
end

def running_in_admin_mode?
  (`reg query HKU\\S-1-5-19 2>&1` =~ /ERROR/).nil?
end

abort("Please run vagrant as an administrator.") if !running_in_admin_mode? && is_windows?

Vagrant.configure("2") do |config|
	config.vm.box = "bento/ubuntu-20.04"
  config.vm.hostname = $vagrantBoxName
  config.vm.define $vagrantBoxName

  config.vm.network "forwarded_port", guest: 1080, host: 1080
  config.vm.network "private_network", ip: $vagrantBoxIp

  config.vm.synced_folder ".", "/var/www", :mount_options => ["dmode=777", "fmode=777"]

  config.vm.provider "virtualbox" do |vb|
    vb.name = $vagrantBoxName
    vb.customize ["modifyvm", :id, "--memory", 4096]
  end

  config.vm.provision "shell", path: ".box/scripts/provision/provision.sh", privileged: false
  config.vm.provision "shell", path: ".box/scripts/provision/install-composer.sh", privileged: false
  config.vm.provision "shell", path: ".box/scripts/provision/install-mailcatcher.sh", privileged: false

  config.vm.provision "shell", path: ".box/scripts/configure/nginx.sh", run: "always", privileged: true
  config.vm.provision "shell", path: ".box/scripts/configure/mailcatcher.sh", run: "always", privileged: true
  config.vm.provision "shell", path: ".box/scripts/configure/php.sh", run: "always", privileged: true
  config.vm.provision "shell", path: ".box/scripts/configure/mysql.sh", run: "always", privileged: true
  config.vm.provision "shell", path: ".box/scripts/configure/composer.sh", run: "always", privileged: false
  config.vm.provision "shell", path: ".box/scripts/configure/motd.sh", run: "always", privileged: true
  config.vm.provision "shell", path: ".box/scripts/configure/symlinks.sh", run: "always", privileged: false
  config.vm.provision "shell", path: ".box/scripts/configure/local-environment.sh", run: "always", privileged: false

  config.vm.provision "shell", run: "always", privileged: false, inline: <<-EOF
  echo "Your vagrant machine is loaded at hosts config: #{$vagrantBoxIp} #{$vagrantHosts}"
  echo "Please add this configuration in your hosts file located on /etc/hosts on Linux od Windows/System32/drivers/etc/hosts on Windows."
  echo "Mailcatcher is available at http://localhost:1080."
  echo "More details about box are available at .box directory located in project root."
EOF
end