<?php

include "connect.php";

if (isset($_POST['submit'])) // name of submit button
{
    $adminname=$_POST['adminname'];
    $password=$_POST['password'];
    $password=md5($password);
    $query = "select * from ch_admins where adminname='$adminname' and password='$password'"; 
    $result = mysql_query($query) or die("No te Gusta") ;
    
    $isAuth = false; //set to false originally
    
    while($row = mysql_fetch_array($result))
    {
      $isAuth=true;
      session_start();
      $_SESSION['adminname']=$adminname;
      
    }  
    
    if($isAuth==true)
    {
                print "logged in successfully<br><br>";
                print "<A href='index.php'>Go to User Management Panel</a>";
    }
    else
    {
       print "Wrong username or password";
    }
}

?>