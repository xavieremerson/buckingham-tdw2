echo off
cls
title Sequential Script Processing
D:
echo Processing Analyst/Coverage from Jovus to TDW
cd D:\tdw\tdw\auto
php -c c:\php\php.ini sproc_jovus_analyst_coverage.php >> sequential.wri
echo Processing Complete

echo Processing Coverage Universe from Jovus
cd D:\tdw\tdw\auto
php -c c:\php\php.ini sproc_coverage_universe.php >> sequential.wri
echo Processing Complete

echo Processing Lookup Creation
cd D:\tdw\tdw\auto
php -c c:\php\php.ini sproc_create_lookups.php >> sequential.wri
echo Processing Complete

echo Processing Daily Compliance Report v1
cd D:\tdw\tdw\auto
php -c c:\php\php.ini sproc_dly_rep_dcar.php > sequential_dcar.wri
echo Processing Complete

echo Processing Checks/Payments which are entered into TDW by Back-Office
cd D:\tdw\tdw\auto
php -c c:\php\php.ini sproc_check_brok.php > sequential_checks.wri
echo Processing Complete

echo Bloomberg OATS Auto Archival
cd D:\tdw\tdw\auto\bloomberg
php -c c:\php\php.ini getfiles.php
echo Processing Complete

echo BuckNotes Research Viewer (Updating Company Names)
cd D:\tdw\rv
php -c c:\php\php.ini sproc_update_companynames.php
echo Processing Complete

echo Getting Sector and Industry from Yahoo
cd D:\tdw\tdw\auto
php -c c:\php\php.ini sproc_sec_industry_yahoo.php
echo Processing Complete

echo Getting Sector and Industry from Google
cd D:\tdw\tdw\auto
php -c c:\php\php.ini sproc_sec_industry_google.php
echo Processing Complete

echo Getting Employee Trades (NFS)
cd D:\tdw\tdw\auto
php -c c:\php\php.ini sproc_emp_trades_daily.php
echo Processing Complete

REM exit