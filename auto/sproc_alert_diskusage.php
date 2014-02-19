<?
  include('../includes/dbconnect.php');
  include('../includes/global.php');
	include('../includes/functions.php');


// Disk information
$disks				= array("C:","D:","E:");
$i = 0;
settype($totalDiskSpace, "float");

$str_to_email = "";
$str_subject = "";
$threshold = 1000;
while ($i < count($disks)) {
	
	$free = round(disk_free_space($disks[$i]) / 1048576, 2);
	$total = round(disk_total_space($disks[$i]) / 1048576, 2);
	$used = $total - $free;
	$showdiskfree =  bytestostring(disk_free_space($disks[$i]),2);
	$showdisktotal = bytestostring(disk_total_space($disks[$i]),2);
	$showdiskused =  bytestostring((disk_total_space($disks[$i])-disk_free_space($disks[$i])),2);
	$percentUsed = round(($used / $total) * 100, 2);
	$percentFree = round(($free / $total) * 100, 2);
	$diskInfo[] = array($free, $used, $total, $percentFree, $percentUsed, $disks[$i]);
	$totalDiskSpace = $totalDiskSpace + $total;
	
	if ($free < $threshold) {
	echo $free."<br>";
	$str_subject .= " ". $disks[$i] . " less than ".$threshold. "MB left.";
	$str_to_email .= $disks[$i] . " has less than ".$threshold. "MB left.<br>";
	}
	
$i++;	
}


function bytestostring($size, $precision = 0) {
    $sizes = array('YB', 'ZB', 'EB', 'PB', 'TB', 'GB', 'MB', 'kB', 'B');
    $total = count($sizes);

    while($total-- && $size > 1024) $size /= 1024;
    return round($size, $precision).$sizes[$total];
}


if (strlen($str_to_email) > 0) {
		//production
		$arr_recipient[] = 'pprasad@centersys.com';
		$arr_recipient[] = 'pprasad@centersys.com';
		
		foreach ($arr_recipient as $key => $emailval) {
						
						$email_log = '
											<table width="100%" border="0" cellspacing="0" cellpadding="10">
												<tr> 
													<td valign="top">
														<p><a class="bodytext12"><strong>Disk Space Low: Urgent action required.</strong></a></p>			
														<p><a class="bodytext12">'.$str_to_email.'</strong></a></p>
														<p>&nbsp;</p>
														<p>&nbsp;</p>
														<p><a class="bodytext12"><strong>TDW Administrator</strong></a></p></td>
												</tr>
											</table>
												';
						//create mail to send
						$html_body = "";
						$html_body .= zSysMailHeader("");
						$html_body .= $email_log;
						$html_body .= zSysMailFooter ();
						
						$subject = "TDW: Disk Space Low: Urgent action required. ".$str_subject;
						$text_body = $subject;
						
						zSysMailer($emailval, "", $subject, $html_body, $text_body, "") ;
						echo $link . "<br>";
		}
} else {
	echo "Disk Drives OK. TDW Server. ". date('m/d/Y h:i:sa');
}
?>