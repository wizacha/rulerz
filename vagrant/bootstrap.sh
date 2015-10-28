#!/usr/bin/env bash

apt-get update

apt-get install -y curl git php5-cli php5-curl php5-pgsql postgresql sqlite3

# Composer
curl -sS https://getcomposer.org/installer | php
mv composer.phar /usr/local/bin/composer
