@echo off
cls
title Processing Trades
ECHO Processing Trades
D:
cd D:\tdw\tdw\auto\bloomberg\client_orders_trades\
php -c c:\php\php.ini getfiles.php > log.wri
php -c c:\php\php.ini sproc_bloomberg_trades.php >> log.wri
REM exit