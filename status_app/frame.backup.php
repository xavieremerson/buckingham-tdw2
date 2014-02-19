<link rel="stylesheet" href="style.css" type="text/css">

<?php //iframe for chat module
set_time_limit(0);
include "connect.php";
//include "admin/var.php";

$getrows="SELECT * from smes_messages";
$getrows2=mysql_query($getrows);
$getrows3=mysql_num_rows($getrows2);
$s=$getrows3-30;
if($s<0)
{
  $s=0;
}
$id=0;
while(!connection_aborted())
{
$id++;
//$getmessage="Select * from smes_messages order by time ASC limit $s,$getrows3";
$getmessage="Select * from smes_messages order by time DESC limit 5";
$getmessage2=mysql_query($getmessage) or die("Could not get messages");
  while($getmessage3=mysql_fetch_array($getmessage2))
  {
    if($getmessage3[registered]==1)
    {
      $out_message .= "<font color='$registeredcolor'>$getmessage3[poster]</font>: $getmessage3[message]<br />";
      $s++;
      $out_message .= "<A name='m$id'>";
    }
    else if($getmessage3[registered]==0)
    {
      $out_message .= "<font color='$unregisteredcolor'>Unregistered</font>: $getmessage3[message]<br />";
      $s++;
      $out_message .= "<A name='m$id'>";
    }  
  
  }
	
	 //print $out_message;
	?>
	<div id="disp_val"></div>
	<script type='text/javascript'>
	window.disp_val.value = '<?=$out_message?>';
	</script>
	<?
 
 if($s>=$getrows3)
 { 
  //print "<A name='bottom'>"; 
  flush();
  sleep(2); 
 }

}


?>




  



