<?php

include "connect.php";

if (isset($_POST['submit'])) // name of submit button
{
    $chatter=$_POST['chatter'];
    $password=$_POST['password'];
    $password=md5($password);
    $query = "select * from ch_chatters where chatter='$chatter' and password='$password'"; 
    $result = mysql_query($query) or die("No te Gusta") ;
    
    $isAuth = false; //set to false originally
    
    while($row = mysql_fetch_array($result))
    {
      $isAuth=true;
      session_start();
      $_SESSION['chatter']=$chatter;
      
    }  
    
    if($isAuth==true)
    {
      print "logged in successfully<br><br>";
      print "<A href='index.php'>Go to Chat</a>";
      $check="SELECT * from ch_online where sessionname='$chatter'";
      $check2=mysql_query($check) or die("2");
      $check3=mysql_fetch_array($check2);
      if(!$check3)
      {
        $day=date('U');
        $s="INSERT into ch_online (sessionname, time) values ('$chatter','$day' )";
        $s2=mysql_query($s) or die("not queried");
      }
                
    }
    else
    {
       print "Wrong username or password";
    }
}

?>