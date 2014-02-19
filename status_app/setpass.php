<?php
//change passwords module for chipmunk chat
include "connect.php";
session_start();
?>
<?php
if(isset($_SESSION['chatter']))
{
  $chatter=$_SESSION['chatter'];
  if(isset($_POST['submit']))
  {
    $password=$_POST['password'];
    if(strlen($password)<1)
    {
      print "You need to enter a password";
    }
    else
    {
     
      $password=md5($password);
      $updatepass="update ch_chatters set password='$password' where chatter='$chatter'";
      mysql_query($updatepass) or die("Could not change password");
      print "Password change, please <A href='login.php'>Login</a>";
    }
   
  }
  else
  {
     print "Change your password:";
     print "<form action='setpass.php' method='post'>";
     print "Type new password: <input type='password' name='password' size='15'><br>";
     print "<input type='submit' name='submit' value='submit'></form>";

  }
}
else
{
  print "You are not logged in";
}
?>
