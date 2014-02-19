@echo off 
cls
title Processing Client Revenue Data.
D:
cd D:\tdw\tdw\auto
echo Processing Client Revenue.
echo Data being inserted to database. Table: _client_revenue
php -c c:\php\php.ini sproc_client_revenue.php > sproc_client_revenue.txt
echo Processing Client Revenue By Rep By Tier.
echo Data being inserted to database. Table: _client_revenue_by_rep_tier
php -c c:\php\php.ini sproc_client_revenue_by_rep_tier.php >> sproc_client_revenue.txt