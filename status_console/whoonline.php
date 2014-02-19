
<?php //iframe for chat module
include "connect.php";
include "admin/var.php";
print "Registered Online:<br><br>";
$now=date('U');
$deldate=$now-900;
$delinactive="DELETE from ch_online where time<'$deldate'";
mysql_query($delinactive) or die(mysql_error());
$getmessage="Select * from ch_online";
$getmessage2=mysql_query($getmessage) or die("Could not get messages");
  while($getmessage3=mysql_fetch_array($getmessage2))
  {
      print "$getmessage3[sessionname]<br>";

  }

flush();
print "<META HTTP-EQUIV = 'Refresh' Content = '60; URL =whoonline.php'>";




?>




  



