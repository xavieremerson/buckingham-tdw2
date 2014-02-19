@echo off
cls
title Processing Trades
ECHO Processing Trades
D:
cd D:\tdw\tdw\auto\bloomberg\client_orders\
php -c c:\php\php.ini getfiles.php > log.wri
REM php -c c:\php\php.ini trades_u.php > log_tu.wri
php -c c:\php\php.ini orders_update.php > log_tu.wri
REM php -c c:\php\php.ini check_post_approval.php > logtemp.wri  
php -c c:\php\php.ini check_post_approval_sweep.php > logtemp.wri  
exit