
class developer {
  package { 'nfs-kernel-server':
    ensure => present,
  }

  service { 'nfs-kernel-server':
    ensure => running,
    enable => true,
    hasstatus => true,
    require => Package['nfs-kernel-server'],
  }

  package { 'git-core':
    ensure  => present
  }

  package { 'subversion':
    ensure  => present
  }

  package { 'curl':
    ensure => present
  }

  exec { 'vagrant.bin.bash.commands':
    command => 'bash /vagrant/vagrant/bin/install_provision.sh',
  }
}
