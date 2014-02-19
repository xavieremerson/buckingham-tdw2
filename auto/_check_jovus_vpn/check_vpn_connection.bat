@echo off
cls
title Checking VPN Connection (JOVUS)
D:
cd D:\tdw\tdw\auto\_check_jovus_vpn
php -c c:\php\php.ini check_vpn_connection.php > log.txt