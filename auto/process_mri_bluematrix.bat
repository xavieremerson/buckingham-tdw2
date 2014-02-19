echo off
cls
title Processing MRI from Blue Matrix
D:
echo Processing MRI from Blue Matrix
cd D:\tdw\tdw\auto
php -c c:\php\php.ini process_mri_bluematrix.php > process_mri_bluematrix.wri
echo Processing Complete