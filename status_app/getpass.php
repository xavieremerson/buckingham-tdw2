<?php //module for password retrieval
 include 'connect.php';
 if(isset($_POST['submit']))
 {
   $getpassword=$_POST['getpassword'];
   $passkey="SELECT * from ch_chatters where email='$getpassword'";
   $passkey2=mysql_query($passkey) or die("Blah");
   $passkey3=mysql_fetch_array($passkey2);
   if(!$passkey3)
   {
     print "We have no player with that e-mail address";
   }
   else 
   {
        $day=date("U");
        srand($day);
        $thenum=RAND(1000000,2000000);
        $encnum=md5($thenum);
        $email=$passkey3[email];
        $ID=$passkey3[ID];
        $newpass="UPDATE ch_chatters set password='$encnum' where ID='$ID'";
        mysql_query($newpass) or die(mysql_error());
        mail("$email", "Password", "Your password has been set to $thenum");
        print "Password sent";
    }
   
 
 }
 else
 {
   print "<form action='getpass.php' method='post'>";
   print "Enter e-mail address: <input type='text' name='getpassword' size='20'>";
   print "<input type='submit' name='submit' value='submit'></form>";
 }

?>