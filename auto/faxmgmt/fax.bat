echo off
cls
title FAX Archiving
d:
cd D:\tdw\tdw\auto\faxmgmt
php -c c:\php\php.ini index.php >> log.txt
exit