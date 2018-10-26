#!/usr/bin/env bash
#########################################################################################################
# BEFORE RUNNING THIS SCRIPT
##########################################################################################################
#
# 1) Clone repository in your working directory
# 2) Copy .env.example to .env and set environment variables (development only)
# 3) vagrant up (development) OR run this script (production) -> get a cup of coffee
#
# NOTE: Initial server setup:
# https://www.digitalocean.com/community/tutorials/initial-server-setup-with-ubuntu-18-04
#########################################################################################################

#########################################################################################################
# SET DEFAULT ENVIORNEMENT VARIABLES | IMPORT ENVIORNMENT VARIABLES FILE
#########################################################################################################
printf "\nMANEVIA: Setting environment variables...\n\n"

DATABASE_NAME="manevia_db"
DATABASE_PASSWORD="secret"
DATABASE_SESSION_STORE_IN_DB="0"
DOMAIN_NAME="manevia.test"
WEB_ROOT="/var/www/html"
GENERATE_SELF_SIGNED_CERTIFICATE="0"

cd $(dirname "$(readlink -f "$0")")
source "../.env"

#########################################################################################################
# GENERATE LOCALES (FOR GETTEXT)
#########################################################################################################
printf "\nMANEVIA: Generating locales for gettext...\n\n"

sudo locale-gen en_US
sudo locale-gen en_US.UTF-8
sudo locale-gen ht_HT
sudo locale-gen ht_HT.UTF-8
sudo update-locale

#########################################################################################################
#ADD REPOSITORIES AND UPDATE APT-GET
#########################################################################################################
printf "\nMANEVIA: Adding ppa:ondrej/php repository and updating repository database...\n\n"

sudo add-apt-repository ppa:ondrej/apache2
sudo add-apt-repository ppa:ondrej/php
sudo apt-get update

#########################################################################################################
# UNINSTALL MYSQL
#########################################################################################################
printf "\nMANEVIA: Uninstalling MySQL...\n\n"

sudo apt-get remove --purge mysql-server mysql-client mysql-common
sudo apt-get autoremove
sudo apt-get autoclean
sudo rm -rf /var/lib/mysql

#########################################################################################################
# INSTALL & SETUP MARIA DB | RESTART DATABASE
#########################################################################################################
printf "\nMANEVIA: Installing MariaDB...\n\n"

sudo debconf-set-selections <<< "mariadb-server mysql-server/root_password password ${DATABASE_PASSWORD}"
sudo debconf-set-selections <<< "mariadb-server mysql-server/root_password_again password ${DATABASE_PASSWORD}"
sudo DEBIAN_FRONTEND=noninteractive apt-get install -yq mariadb-server mariadb-client

#########################################################################################################
# CREATE DATABASE | CREATE SESSION TABLE IF REQUESTED
#########################################################################################################
printf "\nMANEVIA: Creating manevia_db database...\n\n"

sudo mysql <<< "CREATE DATABASE ${DATABASE_NAME} CHARACTER SET 'utf8mb4' COLLATE 'utf8mb4_unicode_ci';"

if [ ${DATABASE_SESSION_STORE_IN_DB} = "1" ] ; then
sudo mysql <<_EOF_
USE ${DATABASE_NAME};
CREATE TABLE sessions (id varchar(100) NOT NULL default '', data text NOT NULL, expires int unsigned NOT NULL default 0, PRIMARY KEY (id)) ENGINE=InnoDB;
exit
_EOF_
fi

#########################################################################################################
# SECURE MARIA DB
# ALTER USER 'root'@'localhost' IDENTIFIED BY "'${DATABASE_PASSWORD}'"; #MariaDB 10.1.20 and newer
#########################################################################################################
printf "\nMANEVIA: Securing MariaDB...\n\n"

sudo mysql <<_EOF_
USE mysql;
DELETE FROM user WHERE User='root' AND Host NOT IN ('localhost', '127.0.0.1', '::1');
UPDATE mysql.user SET plugin = 'mysql_native_password', Password = PASSWORD("${DATABASE_PASSWORD}") WHERE User = 'root';
DELETE FROM user WHERE User='';
DROP DATABASE IF EXISTS test;
DELETE FROM db WHERE Db='test' OR Db='test\\_%';
FLUSH PRIVILEGES;
exit
_EOF_
sudo service mysql stop
sudo service mysql start

#########################################################################################################
# INSTALL REMAINING UBUNTU PACKAGES
#########################################################################################################
printf "\nMANEVIA: Installing remaining Ubuntu packages...\n\n"

sudo DEBIAN_FRONTEND=noninteractive apt-get install -yq \
    apache2 \
    re2c \
    libtool \
    curl \
    make \
    gcc \
    gettext \
    automake \
    git \
    imagemagick \
    locales \
    mcrypt \
    memcached \
    openssl \
    php-redis \
    php7.1 \
    php7.1-cli \
    php7.1-common \
    php7.1-dev \
    php7.1-gettext \
    php7.1-gd \
    php7.1-json \
    php7.1-imagick \
    php7.1-intl \
    php7.1-mcrypt \
    php7.1-mysqlnd \
    php7.1-memcached \
    php7.1-soap \
    php7.1-xdebug \
    php7.1-xml \
    php7.1-curl \
    php7.1-zip \
    php7.1-mbstring \
    poedit \
    redis-server

#########################################################################################################
# APACHE: START | INSTALL MODULES | CREATE SSL DIRECTORY | BACKUP/CREATE CONFIGURATION | GENERATE SELF-SIGNED CERTIFICATE
#########################################################################################################
printf "\nMANEVIA: Setup Apache...\n\n"

sudo service apache2 start

sudo a2enmod rewrite
sudo a2enmod expires
sudo a2enmod headers
sudo a2enmod ssl

sudo mv /etc/apache2/sites-enabled/000-default.conf /etc/apache2/sites-enabled/000-default.conf.old
sudo cp ./build_docs/000-default.conf /etc/apache2/sites-enabled/000-default.conf
sudo sed -i .backup -e "s/\[DOMAIN_NAME]/${DOMAIN_NAME}/g" /etc/apache2/sites-enabled/000-default.conf

if [ ${GENERATE_SELF_SIGNED_CERTIFICATE} = "1" ] ; then
sudo mkdir /etc/apache2/.ssl
sudo openssl req -x509 -nodes -days 7300 -newkey rsa:2048 -keyout /etc/apache2/.ssl/manevia.key -out /etc/apache2/.ssl/manevia.crt <<_EOF_
US
MS
Shelby
Manevia
Framework
manevia.test
manevia@manevia.test
_EOF_
fi

sudo service apache2 restart

#########################################################################################################
# INSTALL & RUN COMPOSER
#########################################################################################################
printf "\nMANEVIA: Installing and running Composer...\n\n"

cd $WEB_ROOT
EXPECTED_SIGNATURE="$(wget -q -O - https://composer.github.io/installer.sig)"
php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
ACTUAL_SIGNATURE="$(php -r "echo hash_file('SHA384', 'composer-setup.php');")"

if [ "$EXPECTED_SIGNATURE" != "$ACTUAL_SIGNATURE" ]
then
    >&2 echo 'ERROR: Invalid installer signature'
    rm composer-setup.php
fi

php composer-setup.php --install-dir=/usr/local/bin --filename=composer --quiet
rm composer-setup.php
php /usr/local/bin/composer install --optimize-autoloader

#########################################################################################################
# CHANGE PERMISSIONS FOR MUSTACHE CACHE DIRECTORY
#########################################################################################################
printf "\nMANEVIA: Setting permissions for Mustache cache directory...\n\n"

sudo chmod 777 $WEB_ROOT/backup/cache

#########################################################################################################
# Configure manevia CLI
#########################################################################################################
printf "\nMANEVIA: Configuring manevia cli...\n"

sudo cp ${WEB_ROOT}/cli/manevia.php /usr/bin/manevia
sudo chmod +x /usr/bin/manevia

printf "\nMANEVIA: **** MANEVIA BUILD SCRIPT COMPLETED ****\n\n"