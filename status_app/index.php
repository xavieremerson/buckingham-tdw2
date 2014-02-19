<?php //main chat module
include "connect.php";
include "admin/var.php";
print "<center>";
?>
<link rel="stylesheet" href="style.css" type="text/css">
<iframe marginwidth="0" marginheight="0" width="468" height="60" scrolling="no" frameborder="0" src="http://rcm.amazon.com/e/cm?t=arcadeportal&p=13&o=1&l=bn1&browse=1000&mode=books&f=ifr">


<MAP NAME="boxmap-p13"><AREA SHAPE="RECT" COORDS="379, 50, 460, 57" HREF="http://rcm.amazon.com/e/cm/privacy-policy.html?o=1" ><AREA COORDS="0,0,10000,10000" HREF="http://www.amazon.com/exec/obidos/redirect-home/arcadeportal" ></MAP><img src="http://rcm-images.amazon.com/images/G/01/rcm/468x60.gif" width="468" height="60" border="0" usemap="#boxmap-p13" alt="Shop at Amazon.com">


</iframe><br><br>
<?php
print "<A href='register.php'>Register</a>&nbsp;&nbsp;&nbsp;";
print "<A href='login.php'>Login</a><br><br>";
print "<table class='maintable'><tr><td valign='top'>";
print "<iframe src='frame.php'  class='maintable' width='350' height='400'></iframe></td>";
print "<td valign='top'><iframe src='whoonline.php' width='200 height='250'></iframe></td></tr></table>";
print "<br><br>";
print "<iframe src='post.php' width='350' height='150' frameborder='0'></iframe><br><br>";

if($guestpost=="no")
{
  print "<font color='red'>*Only Registered members can Post</font>";
}

print "</center><br><br>";
print "<center><font size='1'>Powered by © <A href='http://www.chipmunk-scripts.com'>Chipmunk Chat</a></font></center>";

?>
