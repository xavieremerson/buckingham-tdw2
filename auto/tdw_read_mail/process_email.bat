@ECHO OFF
cls
title Processing Emails sent to tdw@buckresearch.com
D:
cd D:\tdw\tdw\auto\tdw_read_mail
ECHO process_email_tdw
php -c c:\php\php.ini process_email_tdw.php
exit


