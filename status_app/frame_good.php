<link rel="stylesheet" href="style.css" type="text/css">

<body>


<?
include "connect.php";
$getmessage="Select *,  DATE_FORMAT(time,'%l:%i %p %c/%e/%y') as time_format from smes_messages order by time DESC limit 6";
$getmessage2=mysql_query($getmessage) or die("Could not get messages");
  while($getmessage3=mysql_fetch_array($getmessage2))
  {
    if($getmessage3[registered]==1)
    {
      $out_message .= "<a class='employee'>$getmessage3[poster]: ($getmessage3[time_format])<br> </a><a class='message'> $getmessage3[message]</a><br />";
      $out_message .= "<A name='m$id'>".'<hr size="1" color="#999999">';
      $s++;
    }
    else if($getmessage3[registered]==0)
    {
      $out_message .= "<a class='system'>$getmessage3[poster]: ($getmessage3[time_format])<br> </a><a class='message'>$getmessage3[message]</a><br />";
      $out_message .= "<A name='m$id'>".'<hr size="1" color="#999999">';
     $s++;
    }  
  
  }
	 
	 print $out_message;

?>
	 <form name="xtest" action="<?=$PHP_SELF?>"></form>
</body>


  



