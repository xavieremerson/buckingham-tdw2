echo off
cls
title Sequential Script Processing
D:

echo Basic Data Maintenance
cd D:\tdw\tdw\auto
php -c c:\php\php.ini sproc_data_maintenance.php > sequential.wri
echo Processing Complete


echo Processing Trades from Ezecastle
cd D:\tdw\tdw\auto
REM php -c c:\php\php.ini sproc_ezecastle_v3.php >> sequential.wri
php -c c:\php\php.ini sproc_ezecastle_v4.php >> sequential.wri
echo Processing Complete


echo Processing BCM BLACKOUT to create restricted list
cd D:\tdw\etpa\auto\
php -c c:\php\php.ini sproc_bcm_blackout.php > log_blackout.wri
echo Processing Complete


echo Processing Daily Compliance Activity Report (v2)
cd D:\tdw\tdw\auto
REM php -c c:\php\php.ini sproc_dly_rep_dcarv2.php > sequential_dcarv2.wri
php -c c:\php\php.ini sproc_dly_rep_dcarv2_bm.php > sequential_dcarv2.wri
echo Processing Complete


echo Processing BCM Price Volume Alert
cd D:\tdw\tdw\auto
REM php -c c:\php\php.ini sproc_alert_bcm_vol_pct_email.php > sequential_bcm_privol.wri
php -c c:\php\php.ini sproc_alert_bcm_privol_email.php > sequential_bcm_privol.wri
echo Processing Complete

REM exit