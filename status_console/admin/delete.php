<?php
include "connect.php";
session_start();
include "var.php";
if(isset($_SESSION['adminname']))
{
  if(!isset($_POST['ID'])&&!isset($_GET['ID']))
  {
    print "No user specified, please specify a user";
  }
  else
  {
     $ID=$_GET['ID'];
     if($_POST['submit'])
     {
       $ID=$_POST['ID'];
       $getemail="SELECT * from ch_chatters where ID='$ID'";
       $getemail2=mysql_query($getemail) or die("Could not grab user email");
       $getemail3=mysql_fetch_array($getemail2);
       $headers = "From: $adminemail\r\nReply-To: $getemail3[email]\r\n";
       mail("$getemail3[email]", "Chat account deletion", "
       Your Chat account has been deleted from $sitename
       ",$headers);
       $deluser="DELETE from ch_chatters where ID='$ID'";
       mysql_query($deluser) or die("Could not query");
       print "Account deleted";
       print '<body onload="reload();">';
      
     }
     else
     {
       print "Are you sure you want to delete this user?<br>";
       print "<form action='delete.php' method='post'>";
       print "<input type='hidden' name='ID' value='$ID'>";
       print "<input type='submit' name='submit' value='submit'></form>";
     }
  
  }
}
else
{
  print "Not logged in as Admin";
}

?>
<script language="javascript">
function reload()
{
  opener.location.reload();
}
</script>