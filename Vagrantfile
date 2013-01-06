# -*- mode: ruby -*-
# vi: set ft=ruby :

Vagrant::Config.run do |config|
  config.vm.box = "precise32"
  config.vm.box_url = "http://files.vagrantup.com/precise32.box"

  config.vm.customize ["modifyvm", :id, "--memory", 768]

  config.vm.network :hostonly, "33.33.33.100"

  config.vm.provision :puppet, :options => "--verbose" do |puppet|
    puppet.manifests_path = "vagrant/manifests"
    puppet.manifest_file = "up.pp"
  end

#  config.vm.share_folder("v-root", "/vagrant", ".", :nfs => true, :create => true)
  config.vm.share_folder("v-root", "/vagrant", ".", :create => true)
end
