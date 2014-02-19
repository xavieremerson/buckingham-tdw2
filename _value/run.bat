echo off
cls
title Estimate Collation
D:
echo Processing Estimates from Yahoo and Jovus
cd D:\tdw\tdw\estimates
php -c c:\php\php.ini index_auto.php
echo Processing Complete