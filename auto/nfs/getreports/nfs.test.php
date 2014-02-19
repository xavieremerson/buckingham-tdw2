<?
ini_set('max_execution_time', 1600);

include('nfs.config.inc.php');
include('dbconnect.php');
include('nfs.functions.php');
include('mailer_functions.php');

echo previous_business_day('2007-01-03');
?>