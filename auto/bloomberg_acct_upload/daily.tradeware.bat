@echo off
cls
title Uploading Accounts to Tradeware
D:
cd D:\tdw\tdw\auto\tradeware\
php -c c:\php\php.ini cron_accounts.php > log.wri
exit


