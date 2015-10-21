# -*- mode: ruby -*-
# vi: set ft=ruby :

Vagrant.configure(2) do |config|
  config.vm.box = "ubuntu/trusty64"
  config.vm.box_check_update = true

  config.vm.network :forwarded_port, host: 8888, guest: 80
  config.vm.network :forwarded_port, host: 3333, guest: 3306
  config.vm.network "private_network", ip: "192.168.0.100"
  config.vm.synced_folder "./code/", "/var/www/openeyes", id: "vagrant-root", type: 'nfs'
  config.vm.provider "virtualbox" do |vb|
    vb.memory = "768"
  end
end
