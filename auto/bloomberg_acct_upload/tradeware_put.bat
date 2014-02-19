@echo off
title Uploading Accounts File(s) to Tradeware
echo *******************************************************************
echo *                                                                 *
echo *            ACCOUNTS UPLOAD TO TRADEWARE VIA SFTP                *
echo *                                                                 *
echo *   SERVER: tw-ftp01.tradeware.com (70.42.216.33)                 *
echo *   Login:buckcap@tw-ftp01.tradeware.com                          *
echo *   Password: tr8d3ftpb4ck                                        *
echo *                                                                 *
echo *   For technical issues with this utility, send an email to      *
echo *   SUPPORT@CENTERSYS.COM                                         *
echo *                                                                 *
echo *******************************************************************
d:
REM D:\tdw\tdw\auto\tradeware\psftp.exe buckcap@204.193.132.141 -pw tr8d3ftpb4ck -b D:\tdw\tdw\auto\tradeware\tradeware_ftp_script.txt
D:\tdw\tdw\auto\tradeware\psftp.exe buckcap@70.42.216.33 -pw tr8d3ftpb4ck -b D:\tdw\tdw\auto\tradeware\tradeware_ftp_script.txt
exit