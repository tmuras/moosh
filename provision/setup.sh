#!/bin/bash

echo "Provisioning Moodle moosh DEV box with PHP7"
PASSWORD=mypassword

sudo su
apt-get update
debconf-set-selections <<< "mysql-server mysql-server/root_password password $PASSWORD"
debconf-set-selections <<< "mysql-server mysql-server/root_password_again password $PASSWORD"
apt-get -y install mysql-server apache2 libapache2-mod-php php php7.0-mysql php-xml php-curl php-zip php-gd php-mbstring php-soap php-xmlrpc php-intl php-mysql php-cli curl git mc vim

sudo debconf-set-selections <<< "phpmyadmin phpmyadmin/dbconfig-install boolean true"
sudo debconf-set-selections <<< "phpmyadmin phpmyadmin/app-password-confirm password $PASSWORD"
sudo debconf-set-selections <<< "phpmyadmin phpmyadmin/mysql/admin-pass password $PASSWORD"
sudo debconf-set-selections <<< "phpmyadmin phpmyadmin/mysql/app-pass password $PASSWORD"
sudo debconf-set-selections <<< "phpmyadmin phpmyadmin/reconfigure-webserver multiselect apache2"
sudo apt-get -y install phpmyadmin

# Run apache as user vagrant
sed 's/APACHE_RUN_USER=www-data/APACHE_RUN_USER=vagrant/' -i /etc/apache2/envvars
sed 's/APACHE_RUN_GROUP=www-data/APACHE_RUN_GROUP=vagrant/' -i /etc/apache2/envvars

/etc/init.d/apache2 restart

mkdir -p /opt/data/moodledata
chown vagrant /opt/data/moodledata

echo "create database moodle default character set utf8" | mysql -u root -pmypassword

cd /var/www/html/

echo "Downloading Moodle package"
wget --quiet https://download.moodle.org/download.php/direct/stable31/moodle-latest-31.tgz
tar -xf moodle-latest-31.tgz
cd moodle
cp config-dist.php config.php

sed "s/pgsql';/mysqli';/" -i config.php
sed "s/username';/root';/" -i config.php
sed "s/password';/mypassword';/" -i config.php
sed "s|http://example.com/moodle';|http://192.168.33.10/moodle';|" -i config.php
sed "s|/home/example/moodledata';|/opt/data/moodledata';|" -i config.php

php admin/cli/install_database.php --adminpass=a --fullname="moosh dev" --shortname="moosh dev" --adminemail=noreply@example.com --agree-license

echo "Install composer"
curl -Ss https://getcomposer.org/installer | php
sudo mv composer.phar /usr/bin/composer

echo "Install moosh - finally!"
cd /home/vagrant
rm moosh-src/.gitkeep
git clone https://github.com/tmuras/moosh.git moosh-src
cd moosh-src
composer install
ln -s /home/vagrant/moosh-src/moosh.php /usr/local/bin/moosh

echo "Fix permissions"
chown vagrant:vagrant -R /home/vagrant
chown -R vagrant:vagrant /var/www
chown vagrant -R /opt/data/moodledata

