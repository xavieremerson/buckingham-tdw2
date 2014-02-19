<?php //posting module for chatbox
session_start();
include "connect.php";
include "admin/var.php";
print "<link rel='stylesheet' href='style.css' type='text/css'>";
if(isset($_SESSION['chatter']))
{
  $chatter=$_SESSION['chatter'];
  if(isset($_POST['submit'])||$_POST['messagetext'])
  {
    $chatter=$_SESSION['chatter'];
    $messagetext=$_POST['messagetext'];
    $messagetext=htmlspecialchars($messagetext);
    $messagetext=badwords($messagetext);
    $day=date("U");
    $insertregistered="INSERT into ch_messages (poster,message, registered, time) values('$chatter','$messagetext','1','$day')";
    mysql_query($insertregistered) or die("Could not insert message");
    if(isset($_SESSION['chatter']))
    {
      $sel="SELECT * from ch_online where sessionname='$chatter'";
      $sel2=mysql_query($sel) or die(mysql_error());
      $sel3=mysql_fetch_array($sel2);
      if(!$sel3)
      {
        $insertion="INSERT into ch_online (sessionname, time) values('$chatter','$day')";
        mysql_query($insertion) or die(mysql_error());
      }
      else
      {
        $update="update ch_online set time='$day' where sessionname='$chatter'";
        mysql_query($update) or die("no");
      }
    }
    print "<form action='post.php' method='post'>";
    print "Type in message:<br><input type='text' name='messagetext' size='40'><br>";
    print "<input type='submit' name='submit' value='submit'></form>";
   
  }
  else
  {
    print "<form action='post.php' method='post'>";
    print "Type in message:<br><input type='text' name='messagetext' size='40'><br>";
    print "<input type='submit' name='submit' value='submit'></form>";
    
  }

}
else
{
  if($guestpost=='Yes')
  {
    if(isset($_POST['submit'])||$_POST['messagetext'])
    {
      $day=date("U");
      $messagetext=htmlspecialchars($messagetext);
      $messagetext=badwords($messagetext);
      $insertunregistered="INSERT into ch_messages (message, registered,time) values('$messagetext','0','$day')";
      mysql_query($insertunregistered) or die("Could not insert the message");
      print "<form action='post.php' method='post'>";
      print "Type in message:<br><input type='text' name='messagetext' size='40'><br>";
      print "<input type='submit' name='submit' value='submit'></form>";
    } 
    else
    {
      print "<form action='post.php' method='post'>";
      print "Type in message:<br><input type='text' name='messagetext' size='40'><br>";
      print "<input type='submit' name='submit' value='submit'></form>";
    }

  }
  else
  {
    print "Guests are not allowed to post ";
  }
}
?>
    
    
<?
 function badwords($post)  //function for filtering out bad words
 {
 $badwords=array( 

    
    'FUCK'=>"$$$$",
    'Fuck'=>"%$%#",
    'fUck'=>"^&^%",
    'fuck'=>"@#$#",
    'shit'=>"@$#@",
   
   
   
    );

   $post=str_replace(array_keys($badwords), array_values($badwords), $post);
    return $post;
 }

 



