echo off
cls
title Sequential Script Processing
D:
echo Processing Estimates from Yahoo and Jovus
cd D:\tdw\tdw\estimates
php -c c:\php\php.ini getfiles_yahoo.php
echo Processing Complete