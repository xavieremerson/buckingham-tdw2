<?php
include "connect.php";
$password=$_POST['password'];
$pass2=$_POST['pass2'];
$chatter=$_POST['chatter'];
$email=$_POST['email'];

if(!$_POST['chatter'])
{
  print "You did not select a username";
}
else if(!$_POST['email'])
{
  print "You did not enter an e-mail address";
}
else
{
if ($password==$pass2)
{
  
  $ischatter="SELECT * from ch_chatters where chatter='$chatter'";
  $ischatter2=mysql_query($ischatter) or die("Could not query chatter table");
  $ischatter3=mysql_fetch_array($ischatter2);
  if($ischatter3 || strlen($chatter)>15 || $ischatter3=="Unregistered" )
  {
     print "There is already a chatter of that name or the name you specified is over 15 letters or you entered a reserved name";
  }
  else
  {
    $isaddress="SELECT * from ch_chatters where email='$email'";
    $isaddress2=mysql_query($isaddress) or die("not able to query for password");
    $isaddress3=mysql_fetch_array($isaddress2);
    if($isaddress3)
    {
      print "There is already a chatter with that e-mail address";
    }
    else
    {
      $password=md5($password);
      $SQL = "INSERT into ch_chatters(chatter, password,email) VALUES ('$chatter','$password','$email')"; 
      mysql_query($SQL) or die("could not register");
      print "registration successful.<br>";
      print "Click here to <A href='login.php'>Login</a>";
    }
  }
}

else
{
  print "You suck, your passwords didn't match";
}
}
?>


