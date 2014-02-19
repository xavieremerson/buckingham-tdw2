<?php //User management module
include "connect.php";
include "var.php";
session_start();
if(isset($_SESSION['adminname']))
{
  print "<center>All Usernames are listed in ABC order</center>";
   if(!isset($start)) $start = 0;
  $selectchatter="SELECT * from ch_chatters order by chatter ASC limit $start, 20";
  $selectchatter2=mysql_query($selectchatter) or die("Could not select Chatters");
  print "<center><table border='1' bordercolor='1' bgcolor='#e1e1e1'><tr><td>Username</td><td>Delete User?</td></tr>";
  print "<tr><td colspan='3'>";
 
  $order="SELECT * from ch_chatters";
  $order2=mysql_query($order);
  $d=0;
  $f=0;
  $g=1;
  print "Page:";
  while($order3=mysql_fetch_array($order2))
  {
    if($f%20==0)
    {
      print "<A href='index.php?start=$d'>$g</a> ";
      $g++;
    }
    $d=$d+1;
    $f++;
  }
  print "</td></tr>";
  while($selectchatter3=mysql_fetch_array($selectchatter2))
  {
    print "<tr><td>$selectchatter3[chatter]</td><td><A href=\"javascript:popWin('delete.php?ID=$selectchatter3[ID]',200, 200)\">Delete</a></td></tr>";
  }
  print "</table></center>";
  

}
else
{
  print "You are not logged in as administrator, please <A href='login.php'>Login</a>";
}


?>



<SCRIPT LANGUAGE="Javascript">
		//<!--
		// pop a windoid (Pictures)
		function popWin(url, w, h) 
		{
		 var madURL = url;
		 var x, y, winStr;
		 x=0; y=0;
		 self.name="opener";
		 winStr = "height="+h+",width="+w+",screenX="+x+",left="+x+",screenY="+y+",top="+y+",channelmode=0,dependent=0,directories=0,fullscreen=0,location=0,menubar=0,resizable=0,scrollbars=0,status=0,toolbar=0";
		 lilBaby = window.open(madURL, "_blank", winStr);
		}
		//--> </script>