<?

/*
This program essentially does data updates and maintenance on a daily basis,
example, a client code has a missing client name....
*/
?>
<?
ini_set('max_execution_time', 7200);

include('../includes/dbconnect.php');
include('../includes/global.php'); 
include('../includes/functions.php'); 

//initiate page load time routine
$time=getmicrotime(); 

//Populate missing client name






//show page load time
	echo " ". sprintf("%01.7f",((getmicrotime()-$time)/1000))." s."; 						
?>