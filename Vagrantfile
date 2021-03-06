# -*- mode: ruby -*-
# vi: set ft=ruby :

Vagrant.require_version ">= 2.2.2"

PLUGINS = %w(vagrant-hostmanager vagrant-vbguest vagrant-faster vagrant-auto_network) # These will be installed if not installed
REMOVE_PLUGINS =%w(vagrant-hostsupdater vagrant-winnfsd) # These will be removed if installed

PLUGINS.reject! { |plugin| Vagrant.has_plugin? plugin }
REMOVE_PLUGINS.reject! { |plugin| !Vagrant.has_plugin? plugin }


module OS
    def OS.windows?
        (/cygwin|mswin|mingw|bccwin|wince|emx/ =~ RUBY_PLATFORM) != nil
    end

    def OS.mac?
        (/darwin/ =~ RUBY_PLATFORM) != nil
    end

    def OS.unix?
        !OS.windows?
    end

    def OS.linux?
        OS.unix? and not OS.mac?
    end
end

unless PLUGINS.empty?
  print "The following plugins will be installed: #{PLUGINS.join ", "} "
#  unless ['no', 'n'].include? $stdin.gets.strip.downcase
    PLUGINS.each do |plugin|
	  # Exclude windows only plugins from unix hosts
	  if OS.unix? and "#{plugin}" == "vagrant-winnfsd"
	    break;
	  end
      system("vagrant plugin install #{plugin}")
      puts
    end
#  end

   puts "Please run again"
   exit 1
end

unless REMOVE_PLUGINS.empty?
  print "The following plugins will be uninstalled: #{REMOVE_PLUGINS.join ", "} "
#  unless ['no', 'n'].include? $stdin.gets.strip.downcase
    REMOVE_PLUGINS.each do |plugin|
	  # Exclude windows only plugins from unix hosts
      system("vagrant plugin uninstall #{plugin}")
      puts
    end
#  end

   puts "Please run again"
   exit 1
end

$script = <<SCRIPT
cd /vagrant/install

installparams="-f -d --accept"
installparams_old="$installparams"
# NOTE: Expects key to be called id_rsa or id_github. If using a custom name ssh file, also provide .ssh/config with "IdentityFile ~/.ssh/<custom shh name>"
if [ -d "/home/vagrant/.host-ssh" ] && [ "$(ls -A /home/vagrant/.host-ssh/id*)" ]; then
	echo "Adding SSH keys..."
    installparams_old="$installparams -ssh"
    if [ ! -f "/home/vagrant/.ssh/config" ]; then
        sudo -H -u vagrant bash -c 'echo -e "IdentityFile ~/.host-ssh/id_github\nIdentityFile ~/.host-ssh/id_rsa\nIdentityFile ~/.ssh/id_github\nIdentityFile ~/.ssh/id_rsa" >> /home/vagrant/.ssh/config'
    fi
	# chmod 600 /home/vagrant/.ssh/id*
	sudo -H -u vagrant bash -c '$(ssh-agent)  2>/dev/null'
	# attempt ssh authentication and store key signature
	sudo -H -u vagrant bash -c 'ssh -oStrictHostKeyChecking=no git@github.com -T'

	if [ ! -d "/var/www/openeyes/protected" ] ; then
		sudo -H -u vagrant bash -c 'sudo mkdir -p /var/www/openeyes && sudo chmod 777 -R /var/www/openeyes && cd /var/www/openeyes && git clone -b develop git@github.com:openeyes/openeyes.git .'
	fi
else
	if [ ! -d "/var/www/openeyes/protected" ]; then
		sudo -H -u vagrant bash -c 'sudo mkdir -p /var/www/openeyes && sudo chmod 777 -R /var/www/openeyes && cd /var/www/openeyes && git clone -b develop https://github.com/openeyes/openeyes.git .'
	fi
fi
sudo -H -u vagrant bash -c 'git config --global core.fileMode false && cd /var/www/openeyes && git config core.fileMode false'
echo "setting permissions to non restrictive for openeyes folder during install..."
sudo chmod 774 -R /var/www/openeyes
echo "running install-system...."
if [ -f "/var/www/openeyes/protected/scripts/install-system.sh" ]; then
  OE_MODE="dev" bash /var/www/openeyes/protected/scripts/install-system.sh || exit 1
else
  bash /vagrant/install/install-system.sh || exit 1
fi
sudo -H -u vagrant INSTALL_PARAMS="$installparams" bash -c 'echo "install-oe will use $INSTALL_PARAMS"'

if [ -f "/var/www/openeyes/protected/scripts/install-oe.sh" ];
  then sudo -H -u vagrant INSTALL_PARAMS="$installparams" OE_MODE="dev" OE_INSTALL_LOCAL_DB="TRUE" DEBIAN_FRONTEND=noninteractive bash -c '/var/www/openeyes/protected/scripts/install-oe.sh $INSTALL_PARAMS' || exit 1
else
  sudo -H -u vagrant INSTALL_PARAMS="$installparams_old" bash -c '/vagrant/install/install-oe.sh $INSTALL_PARAMS' || exit 1
fi
SCRIPT

Vagrant.configure(2) do |config|
  config.vm.box = "generic/ubuntu1804"
  config.vm.box_check_update = false

  config.vm.hostname = "openeyes.vm"

  config.vm.network :forwarded_port, host: 8888, guest: 80
  config.vm.network :forwarded_port, host: 3333, guest: 3306

  # config.vm.network "private_network", type: "dhcp"



  # Prefer Hyper-V before VMWare fusion before VirtualBox
  config.vm.provider "hyperv"
  config.vm.provider "vmware_fusion"
  config.vm.provider "virtualbox"

	# Give VM 1/4 system memory or 768MB, whichever is greater (not used in VirtualBox as that used vagrant-faster plugin)
	if OS.mac?
		# sysctl returns Bytes and we need to convert to MB
		mem = `sysctl -n hw.memsize`.to_i / 1024
	elsif OS.linux?
		# meminfo shows KB and we need to convert to MB
		mem = `grep 'MemTotal' /proc/meminfo | sed -e 's/MemTotal://' -e 's/ kB//'`.to_i
	elsif OS.windows?
		# Windows code via https://github.com/rdsubhas/vagrant-faster
		mem = `wmic computersystem Get TotalPhysicalMemory`.split[1].to_i / 1024
	end

	mem = mem / 1024 / 4

    # Align to nearset 16 mb
    mem = 16*((mem+16-1)/16)

	if mem < 768
		mem = 768
	elsif mem > 3072
		mem = 3072
	end


  # VirtualBox
  config.vm.provider "virtualbox" do |v, override|
    v.gui = true
    v.customize [ "guestproperty", "set", :id, "/VirtualBox/GuestAdd/VBoxService/--timesync-set-threshold", 10000 ]
    v.customize ["modifyvm", :id, "--vram", "56"]
    v.customize ["modifyvm", :id, "--accelerate2dvideo", "on"]
    v.customize ["modifyvm", :id, "--nicspeed1", "1000000"]
    v.customize ["modifyvm", :id, "--nicspeed2", "1000000"]
    v.customize ["setextradata", :id, "VBoxInternal2/SharedFoldersEnableSymlinksCreate/vagrant-root", "1"]
    v.customize ["setextradata", :id, "VBoxInternal2/SharedFoldersEnableSymlinksCreate/var/www/", "1"]
    v.customize ["setextradata", :id, "VBoxInternal2/SharedFoldersEnableSymlinksCreate/vagrant/", "1"]
    v.default_nic_type = "virtio"

    AutoNetwork.default_pool = "172.16.0.0/24"
    override.vm.network "private_network", :auto_network => true


	## set time zone to host
    require 'time'
    offset = ((Time.zone_offset(Time.now.zone) / 60) / 60)
    timezone_suffix = offset >= 0 ? "-#{offset.to_s}" : "+#{offset.to_s}"
    timezone = 'Etc/GMT' + timezone_suffix
    override.vm.provision :shell, :inline => "sudo rm /etc/localtime && sudo ln -s /usr/share/zoneinfo/" + timezone + " /etc/localtime", run: "always"

    # Setup synced folders - MacOS uses nfs and shares www to host. Windows uses VirtualBox default and www foler lives internally (use add-samba-share.sh to share www folder to Windows host)
  	if OS.unix?
  		override.vm.synced_folder ".", "/vagrant", type: 'nfs'
  		override.vm.synced_folder "./www/", "/var/www/", id: "vagrant-root", create: true, type: 'nfs'

  	elsif OS.windows?
      override.vm.synced_folder ".", "/vagrant"
      # Mount ssh certs from host
      override.vm.synced_folder "~/.ssh", "/home/vagrant/.host-ssh" , owner: "vagrant",	group: "vagrant", mount_options: ["fmode=600"]
      override.vm.synced_folder "./dicom", "/home/iolmaster/incoming", create: true, owner: "vagrant", group: "www-data", mount_options: ["fmode=777"]
      override.vm.synced_folder "./www", "/var/www", create: true, owner: "vagrant", group: "www-data", mount_options: ["fmode=777"]
    end

  end

  # VMWare Fusion
  config.vm.provider "vmware_fusion" do |v, override|
    override.vm.box = "puppetlabs/ubuntu-14.04-64-nocm"
    v.vmx["displayname"] = "Openeyes"
    v.vmx["memsize"] = mem.to_s
    v.vmx["numvcpus"] = "1"
  end

  # Hyper-V
  config.vm.provider "hyperv" do |h, override|

    h.vmname = "OpenEyes"
    # h.cpus = 2
    h.memory = 768
    h.maxmemory = mem
    # h.ip_address_timeout = 200
    h.vm_integration_services = {
      guest_service_interface: true,
      heartbeat: true,
      key_value_pair_exchange: true,
      shutdown: true,
      time_synchronization: true,
      vss: true
    }

    h.enable_virtualization_extensions="true"
    h.auto_start_action = "Nothing"
    h.auto_stop_action = "ShutDown"

    override.vm.synced_folder ".", "/vagrant", owner: "vagrant", group: "www-data", mount_options: ["noperm,dir_mode=0775,file_mode=0774,vers=3.0"], disabled: true
    override.vm.synced_folder "~/.ssh", "/home/vagrant/.host-ssh" , owner: "vagrant",	group: "vagrant", mount_options: ["file_mode=0600,vers=3.0"]
    override.vm.synced_folder "./dicom", "/home/iolmaster/incoming", create: true, owner: "vagrant", group: "www-data", mount_options: ["noperm,dir_mode=0666,file_mode=0777,vers=3.0"]
    override.vm.synced_folder "./www", "/var/www", create: true, owner: "vagrant", group: "www-data", mount_options: ["noperm,dir_mode=0774,file_mode=0774,mfsymlinks,vers=3.0"]

    override.vm.network "private_network", type: "dhcp"

    # fixes some DNS issues and makes things faster
    #   - Vagrant overwrites resolv.conf with an internal DNS proxy on each up. which fails to resolve some names!
    override.vm.provision "dns", type: "shell", :inline => "sudo apt install resolvconf && echo -e 'nameserver 1.1.1.1\nnameserver 8.8.8.8' | sudo tee -a /etc/resolvconf/resolv.conf.d/tail && sudo resolvconf -u", keep_color: true

  end



# Copy in ssh keys, then provision
## Question: Should SSH keys also be copied on every up?
  config.vm.provision "shell", inline: $script, keep_color: true

  config.hostmanager.enabled = true
  config.hostmanager.manage_host = true
  config.hostmanager.manage_guest = true
  config.hostmanager.ignore_private_ip = false
  config.hostmanager.include_offline = true

end
