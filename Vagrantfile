# -*- mode: ruby -*-
# vi: set ft=ruby :

VAGRANTFILE_API_VERSION = "2"

Vagrant.configure(VAGRANTFILE_API_VERSION) do |config|

  config.vm.box = "debian/stretch64"

  config.vm.hostname = "cloudmigration.dev"
  config.vm.network "private_network", ip: "192.168.33.123"
  config.ssh.forward_agent = true

  config.vm.synced_folder ".", "/vagrant", type: "nfs"

  config.vm.provider "virtualbox" do |vb|
    vb.customize ["modifyvm", :id, "--memory", "1024"]
    vb.customize ["modifyvm", :id, "--cpus", "2"]
    vb.customize ["modifyvm", :id, "--name", "cloudmigration"]
  end

  # Install all required software
  config.vm.provision "shell", inline: "apt-get update; apt-get upgrade -y"
  config.vm.provision "shell", inline: "apt-get install -y nginx php-fpm php-zip"

  # Config PHP-FPM and NGINX
  config.vm.provision "shell", inline: "cp /vagrant/install/nginx_vhost_silex.conf /etc/nginx/sites-enabled/default; cp /vagrant/install/php_fpm_pool.conf /etc/php/7.0/fpm/pool.d/www.conf; service nginx restart; service php7.0-fpm restart"
  config.vm.provision "shell", inline: "systemctl enable php7.0-fpm ; systemctl enable nginx"

  # Install composer
  config.vm.provision "shell", inline: "cd /var/tmp && wget https://getcomposer.org/download/1.5.6/composer.phar && mv composer.phar /usr/bin/composer && chmod 755 /usr/bin/composer"
end
