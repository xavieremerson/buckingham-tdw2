<link rel="stylesheet" href="style.css" type="text/css">
<?php //iframe for chat module
set_time_limit(0);
include "connect.php";
include "admin/var.php";

$getrows="SELECT * from ch_messages";
$getrows2=mysql_query($getrows);
$getrows3=mysql_num_rows($getrows2);
$s=$getrows3-30;
if($s<0)
{
  $s=0;
}
$id=0;
while(!connection_aborted())
{
$id++;
/*
print "<script type='text/javascript'>";
print "window.location.hash = 'm$id';";
print "</script>";
*/
$getmessage="Select * from ch_messages order by time ASC limit $s,$getrows3";
$getmessage2=mysql_query($getmessage) or die("Could not get messages");
  while($getmessage3=mysql_fetch_array($getmessage2))
  {
    if($getmessage3[registered]==1)
    {
      print "<font color='$registeredcolor'>$getmessage3[poster]</font>: $getmessage3[message]<br />";
      $s++;
       print "<A name='m$id'>";
    }
    else if($getmessage3[registered]==0)
    {
      print "<font color='$unregisteredcolor'>Unregistered</font>: $getmessage3[message]<br />";
      $s++;
       print "<A name='m$id'>";
    }  
   
  }
 
 if($s>=$getrows3)
 { 
  //print "<A name='bottom'>"; 
  flush();
  sleep(5); 
 }

}


?>




  



