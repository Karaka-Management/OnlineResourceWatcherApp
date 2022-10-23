#!/bin/bash

. config.sh

echo "#################################################"
echo "Remove old setup"
echo "#################################################"
#rm -r -f ${ROOT_PATH}/app/server/data
#mkdir -p ${ROOT_PATH}/app/server/data

echo "#################################################"
echo "Setup database"
echo "#################################################"
mysql -e 'drop database if exists oms_orw;' -u ${DB_USER} --password="${DB_PASSWORD}"
mysql -e 'create database oms_orw;' -u ${DB_USER} --password="${DB_PASSWORD}"

echo "#################################################"
echo "Setup demo application"
echo "#################################################"
php demoSetup.php