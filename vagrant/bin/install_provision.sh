#!/bin/bash

test_db=dev_epousemoi_fr
test_db_user=dev_epousemoi_fr
test_db_pass=Zephyr@1

db=dev_epousemoi_fr
db_user=dev_epousemoi_fr
db_pass=Zephyr@1

web_root=/var/www
vagrant_root=/vagrant


#Test si vagrant est en mode installation
#ou en mode provisionnement
function test_install(){
	[[ -f /usr/bin/composer ]] && echo "install"
}

#Lance les commandes d'installation
function install(){
	#Créé une base de donnée et l'utilisateur
	function create_user_e_db(){
		local db=$1
		local ut=$2
		local ps=$3
		local sql=tmpfile

		mysqladmin -uroot -proot create $db
		echo "grant all privileges on $db.* to $ut@localhost identified by '$ps';" > $sql
		mysql -uroot -proot < $sql	
		rm $sql;
	}
	create_user_e_db $test_db $test_db_user $test_db_pass
	create_user_e_db $db $db_user $db_pass
	
	curl -s http://getcomposer.org/installer | php
	mv ./composer.phar /usr/bin/composer && chmod +x /usr/bin/composer 2>&1

	function create_symfony(){
		cd /var
		rm -rf www
		php composer.phar create-project symfony/framework-standard-edition $web_root
		cp -rf $vagrant_root/vagrant/resources/webroot/* $web_root
	}
	create_symfony

	#Ajoute les acl au fichier /etc/fstab et remonte la partition
	function add_acl(){
		fstab=$(cat /etc/fstab| sed -e "s/errors=remount-ro/errors=remount-ro,acl/")
		echo fstab > /etc/fstab
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
	for i in $(ls -a|grep -v vendor|grep -v app| egrep -v "^.$"| egrep -v "^..$"| egrep -v "^.git$"| grep -v "^web$")
	do
		cp -ruv $i $web_root
	done
	#  - du dossier app
	cd $vagrant_root/app
	mkdir $web_root/app 2>&1
	for i in $(ls -a|grep -v logs|grep -v cache| egrep -v "^.$"| egrep -v "^..$")
	do
		cp -ruv $i $web_root/app
	done
	#  - du dossier web
	cd $vagrant_root/web
	mkdir $web_root/web 2>&1
	for i in $(ls -a|egrep -v "^.$"| egrep -v "^..$"| egrep -v "^bundles$")
	do
		cp -ruv $i $web_root/web
	done

	#Effectue les mise à jour composer, bases de données et vérifie les droits
	composer -d=$web_root/ update
	$web_root/app/console assets:install --symlink $web_root/web
	$web_root/app/console doctrine:schema:update --force
	set_acls
}

[[ $(test_install) != "install" ]] && install 2>&1 > $vagrant_root/app/logs/vagrant/install.log
provision 2>&1 > $vagrant_root/app/logs/vagrant/provision.log