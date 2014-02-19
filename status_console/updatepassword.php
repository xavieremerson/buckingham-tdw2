<?php
//update passwords module for chipmunk chat, if you are doing a fresh install, delete this file
include "connect.php";
$getadmin="SELECT * from ch_admins";
$getadmin2=mysql_query($getadmin);
$getadmin3=mysql_fetch_array($getadmin2);
$pass=md5($getadmin3[password]);
$updateadminpass="UPDATE ch_admins set password='$pass'";
mysql_query($updateadminpass) or die("Could not update admin password");
$getuserpass="SELECT * from ch_chatters";
$getuserpass2=mysql_query($getuserpass) or die("Could not get user password");
while($getuserpass3=mysql_fetch_array($getuserpass2))
{
  $password=md5($getuserpass3[password]);
  $updateuser="UPDATE ch_chatters set password='$password' where ID='$getuserpass3[ID]'";
  mysql_query($updateuser) or die("Could not update user");
}
print "passwords updated";
?>