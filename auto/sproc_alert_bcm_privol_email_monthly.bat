echo off
cls
title Sequential Script Processing
D:

echo Processing BCM Price Volume Alert
cd D:\tdw\tdw\auto
php -c c:\php\php.ini sproc_alert_bcm_privol_email_monthly.php > sequential_bcm_privol_monthly.wri
echo Processing Complete

exit