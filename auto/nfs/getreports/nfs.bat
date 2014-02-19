REM @echo off
cls
title Copying NFS Reports to permanent location
D:
cd D:\tdw\tdw\auto\nfs\getreports\
REM php -c c:\php\php.ini nfs.getfiles.php > log.wri
php -c c:\php\php.ini nfs.getfiles.php > log.wri
exit