@echo off
title Uploading Accounts File(s) to Tradeware
echo *******************************************************************
echo *                                                                 *
echo *            ACCOUNTS UPLOAD TO TRADEWARE VIA SFTP                *
echo *                                                                 *
echo *   SERVER: tw-ftp01.tradeware.com (204.193.132.141)              *
echo *   Login:buckcap@tw-ftp01.tradeware.com                          *
echo *   Password: tr8d3ftpb4ck                                        *
echo *                                                                 *
echo *   For technical issues with this utility, send an email to      *
echo *   SUPPORT@CENTERSYS.COM                                         *
echo *                                                                 *
echo *******************************************************************
d:
D:\tdw\tdw\auto\tradeware\psftp.exe buckcap@204.193.132.141 -pw tr8d3ftpb4ck