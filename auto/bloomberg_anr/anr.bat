echo off
cls
title Transmitting Research to Bloomberg ANR
d:
cd D:\tdw\tdw\auto\bloomberg_anr
php -c c:\php\php.ini anr.process.pdf.php > log.txt
exit