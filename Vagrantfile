# -*- mode: ruby -*-
# vi: set ft=ruby :

# LOAD OR SET REQUIRED ENVIRONMENT VARIABLES
if File.file?('.env')
	load '.env'
else
    DOMAIN_NAME="manevia.test"
	IP_ADDRESS='192.168.33.10'
	WEB_ROOT='/var/www/html'
end

# CONFIGURE VAGRANT
Vagrant.configure("2") do |config|
    config.vm.box = "bento/ubuntu-18.04"
    config.vm.network "private_network", ip: "#{IP_ADDRESS}"
    config.vm.hostname = "#{DOMAIN_NAME}"
    config.vm.define "#{DOMAIN_NAME}"
    config.vm.synced_folder ".", WEB_ROOT, :mount_options => ["dmode=777", "fmode=666"]
	#config.vm.synced_folder ".", WEB_ROOT, :nfs => {:mount_options => ["dmode=777","fmode=666"]} # Optional NFS. Make sure to remove other synced_folder line above
    config.vm.provision "shell", inline: "sudo /bin/bash #{WEB_ROOT}/cli/build.sh"
end