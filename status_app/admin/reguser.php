<?php
include "connect.php";
$password=$_POST['password'];
$pass2=$_POST['pass2'];
$adminname=$_POST['adminname'];
if(!$_POST['adminname'])
{
  print "You did not select a username";
}
else if($password!=$pass2)
{
  print "You suck, your passwords didn't match";
}
else
{
     
      $password=md5($password);
      $SQL = "INSERT into ch_admins(adminname, password) VALUES ('$adminname','$password')"; 
      mysql_query($SQL) or die("could not register");
      print "registration successful.<br>";
      print "Click here to <A href='login.php'>Login</a>";

}



?>


