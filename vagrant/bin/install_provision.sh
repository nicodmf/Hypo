#!/bin/bash

test_db=dev_hypo
test_db_user=dev_hypo
test_db_pass=dev_hypo

db=hypo
db_user=hypo
db_pass=hypo

symfony_version=2.1.6

web_root=/var/www
vagrant_root=/vagrant
vagrant_log_dir=/vagrant/


#Test si vagrant est en mode installation
#ou en mode provisionnement
function test_install(){
	[[ -f /usr/bin/composer ]] && echo "install"
}

#Lance les commandes d'installation
function install(){
	echo "Install"
	#Créé une base de donnée et l'utilisateur
	function create_user_e_db(){
		local db=$1
		local ut=$2
		local ps=$3
		local sql=tmpfile

		mysqladmin -uroot -proot create $db 2>&1
		echo "grant all privileges on $db.* to $ut@localhost identified by '$ps';" > $sql
		mysql -uroot -proot < $sql	2>&1
		rm $sql;
	}
	create_user_e_db $test_db $test_db_user $test_db_pass
	create_user_e_db $db $db_user $db_pass
	
	curl -s http://getcomposer.org/installer | php
	mv ./composer.phar /usr/bin/composer && chmod +x /usr/bin/composer 2>&1

	function create_symfony(){
		cd /var
		service apache2 stop 2>&1
		rm -rf www
		composer create-project symfony/framework-standard-edition $web_root $symfony_version
		cp -rf $vagrant_root/vagrant/resources/webroot/* $web_root
		service apache2 start 2>&1
	}
	echo "Create symfony"
	create_symfony

	#Ajoute les acl au fichier /etc/fstab et remonte la partition
	function add_acl(){
		if [[ "$(cat /etc/fstab|grep acl)" = "" ]]
		then
			fstab=$(cat /etc/fstab| sed -e "s/errors=remount-ro/errors=remount-ro,acl/")
			echo $fstab > /etc/fstab
		fi
		mount -o remount,acl /dev/mapper/precise32-root
		set_acls
	}
	add_acl
}

#Ajoute les acl et configure les droits des fichier
function set_acls(){
	function set_acl(){
		local dir=$1
		local u1=$2
		local u2=$3
		local gr=$4
		[[ ! -d $dir ]] && mkdir -p $dir 2>&1
		setfacl -R -m u:$u1:rwx -m u:$u2:rwx -m g:$gr:rwx $dir
		setfacl -dR -m u:$u1:rwx -m u:$u2:rwx -m g:$gr:rwx $dir
	}
	chown -R vagrant:vagrant $web_root
	set_acl $web_root/app/cache vagrant www-data vagrant 
	set_acl $web_root/app/logs vagrant www-data vagrant 
}

#Lance les povisionnements
function provision(){
	#Copie les fichiers
	#  - à la racine
	cd $vagrant_root
	cp -rf $vagrant_root/vagrant/resources/webroot/* $web_root
	ln -s $vagrant_root $web_root/src/Hypo 2>&1

	#Effectue les mise à jour composer, bases de données et vérifie les droits
	composer -d=$web_root/ update
	php $web_root/app/console assets:install --symlink $web_root/web
	php $web_root/app/console doctrine:schema:update --force
	set_acls
}

[[ ! -d "/var/log/vagrant" ]] && mkdir /var/log/vagrant
[[ "$(test_install)" != "install" ]] && install 2>&1 > $vagrant_log_dir/install.log
provision 2>&1 > $vagrant_log_dir/provision.log