<?php
$imgpath = "/tdw/tdwsrvinfo/templates/template_files/tdw/";
$exepath = "D:\\tdw\\tdw\\tdwsrvinfo\\exe\\";
$memthreshold = 300;
$diskthreshold = 1000;


/**
 * Present a size (in bytes) as a human-readable value
 * 
 * @param int    $size        size (in bytes)
 * @param int    $precision    number of digits after the decimal point
 * @return string
 */
function bytestostring($size, $precision = 0) {
    $sizes = array('YB', 'ZB', 'EB', 'PB', 'TB', 'GB', 'MB', 'kB', 'B');
    $total = count($sizes);

    while($total-- && $size > 1024) $size /= 1024;
    return round($size, $precision).$sizes[$total];
}

//some functions from the root location but need to resolve this later
function temp_tsp($width, $title) {
//align="center" 
echo '<table width="'.$width.'%" cellpadding="0" cellspacing="0" bgcolor="#F7F7F7" class="tblcont">
				<tr>
					<td>
						<table border="0" CELLPADDING="0" CELLSPACING="0">
							<tr>
								<td align="left" nowrap class="tblconthead">&nbsp;&nbsp;&#9658; '.$title.' &nbsp;&nbsp;</td>
								<td nowrap valign=top ><img src="../tdw/images/tables4/r_angle.png"></td>
								<td width="100%">&nbsp;</td>
							</tr>
						</table>
					</td>
				</tr>
				<tr>
				<td valign="top">
					<table width="100%" border="0" cellpadding="4" cellspacing="0">
						<tr>
							<td valign="top">';
}

function temp_tep() {
echo '			  </td>
						</tr>
					</table>
				</td>
			</tr>
		</table>';
}

  require_once("config.php");

  temp_tsp(100,"TDW Server Status");


  //print_r($_SERVER);


	// Generic server information
	$serverTime = date("r");
	$serverHostName = $_SERVER["SERVER_NAME"];
	$serverIp = gethostbyname($serverHostName);
	$serverOs = shell_exec("ver");
	$serverOs = explode("\n", $serverOs);
	$serverSoftware = $_SERVER["SERVER_SOFTWARE"];
	$serverProtocol = $_SERVER["SERVER_PROTOCOL"];
	$serverPort = $_SERVER["SERVER_PORT"];
	$serverAdmin = $_SERVER["SERVER_ADMIN"];

	// Uptime
	$uptimeStamp = filemtime($swapFile);
	$uptime = time() - $uptimeStamp;
	$days = floor($uptime / (24*3600));
	$uptime = $uptime - ($days * (24*3600));
	$hours = floor($uptime / (3600));
	$uptime = $uptime - ($hours * (3600));
	$minutes = floor($uptime /(60));
	$uptime = $uptime - ($minutes * 60);
	$seconds = $uptime;
	$theuptime = $days." days, ".$hours." hours, ".$minutes." minutes and ".$seconds." seconds";
?>
<style type="text/css">
<!--
.label { 	font-family: Arial, Helvetica, sans-serif;	font-size: 12px;	font-weight: normal;	color: #000000;}
.label_a { 	font-family: Arial;	font-size: 12px;	font-weight: bold;	color: #000066;}
.value { 	font-family: Arial;	font-size: 12px;	font-weight: normal;	color: #000066;}
-->
</style>
	<table>
		<tr>
			<td>
				<table>
					<tr>
						<td colspan="2"><a class="iltc">General Information</a></td>
					</tr>
					<tr>
						<td class="label">Server time:</td>
						<td class="value"><?=$serverTime?></td>
					</tr>
					<tr>
						<td class="label">Uptime:</td>
						<td class="value"><?=$theuptime?></td>
					</tr>
					<tr>
						<td class="label">Hostname:</td>
						<td class="value"><?=$serverHostName?></td>
					</tr>
					<tr>
						<td class="label">IP Address:</td>
						<td class="value"><?=$serverIp?></td>
					</tr>
					<tr>
						<td class="label">OS:</td>
						<td class="value"><?=$serverOs[1]?></td>
					</tr>
					<tr>
						<td class="label">CPU</td>
						<td class="value"><?=getenv("NUMBER_OF_PROCESSORS")." * ".getenv("PROCESSOR_IDENTIFIER") ." (Architecture: ".getenv("PROCESSOR_ARCHITECTURE").")"?></td>
					<tr>
						<td class="label">Application Server:</td>
						<td class="value"><?=$serverSoftware?></td>
					</tr>
					<tr>
						<td class="label">HTTP Port:</td>
						<td class="value"><?=$serverPort?></td>
					</tr>
					<tr>
						<td class="label">TDW Server Administrator Email:</td>
						<td class="value"><a href="mailto:<?=$serverAdmin?>"><?=$serverAdmin?></a></td>
					</tr>
					<tr>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
					</tr>
					<tr>
						<td colspan="2"><a class="iltc">Memory (RAM)</a></td>
					</tr>
					<?
				/*	$memoryInfo = explode("\n", shell_exec($exepath."memory.exe"));
					$memory["totalPhysical"] = round($memoryInfo[4] / 1048576, 2);
					$showmemtotalPhysical = bytestostring($memoryInfo[4]);
					$memory["totalFree"] = round($memoryInfo[5] / 1048576, 2);
					$showmemtotalFree = bytestostring($memoryInfo[5],2);
					$memory["totalUsed"] = $memory["totalPhysical"] - $memory["totalFree"];
					$showmemtotalUsed = bytestostring(($memoryInfo[4] - $memoryInfo[5]) ,2);
					$memory["totalPercentFree"] = round(($memory["totalFree"] / $memory["totalPhysical"]) * 100, 2);
					$memory["totalPercentUsed"] = round(($memory["totalUsed"] / $memory["totalPhysical"]) * 100, 2);
					if ($memthreshold > $memory["totalFree"]) {
						$strfreemem = 'orange';
					} else {
						$strfreemem = 'green';
					}*/
					
					$msinfo32path = "D:\\tdw\\tdw\\winsysinfo\\msinfo32\\";
					$str_shell = $msinfo32path."msinfo32.exe /categories SystemSummary /report ".$msinfo32path."zzz.txt";
					shell_exec($str_shell);
					//echo $str_shell;
					$fh = fopen($msinfo32path."zzz.txt", 'r');
					$lines = file($msinfo32path."zzz.txt");
					?>
					
					<tr>
						<td class="label"></td>
						<td class="value"><?=$lines[27]?>
						</td>
					</tr>
					<tr>
						<td class="label"></td>
						<td class="value"><?=$lines[26]?>
						</td> 
					</tr>
					<tr>
						<td class="label"></td>
						<td class="value"><?=$lines[29]?>
						</td>
					</tr>
					<tr>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
					</tr>
					<tr>
						<td colspan="2"><a class="iltc">Network Data Transfer</a></td>
					</tr>
					<?
					// Network status
					$network = explode("\n", `netstat -e`);
					$network = explode(" ",$network[4]);
						$cnt = 0;
						for ($i=0;$i<count($network);$i++)
							{
							if ($network[$i]=="") continue;
							if ($cnt==0) $text = ucfirst($network[$i]);
							elseif ($cnt==1) $rec = $network[$i];
							elseif ($cnt==2) $sent = $network[$i];         
							$cnt++;
							}
				
					$totalReceive = round($rec / 1048576, 2);
					$showtotalrec = bytestostring($rec,2);
					$totalSent = round($sent / 1048576, 2);
					$showtotalsent = bytestostring($sent,2);
					?>
					<tr>
						<td class="label">Data Received:</td>
						<td class="value"><?=$showtotalrec?></td>
					</tr>
					<tr>
						<td class="label">Data Sent:</td>
						<td class="value"><?=$showtotalsent?></td>
					</tr>
					<tr>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
					</tr>
				</table>
				<table>
					<tr>
						<td><a class="iltc">LEGEND</a></td>
						<td>&nbsp;</td>
					</tr>
					<tr>
						<td class="label">Space OK :</td>
						<td class="value"><img src="<?=$imgpath?>greenbar_left.gif"><img src="<?=$imgpath?>greenbar_middle.gif" width="100" height="14"><img src="<?=$imgpath?>greenbar_right.gif"></td>
					</tr>
					<tr>
						<td class="label">Space NOT OK (Alert will be sent to Support Team) :</td>
						<td class="value"><img src="<?=$imgpath?>orangebar_left.gif"><img src="<?=$imgpath?>orangebar_middle.gif" width="100" height="14"><img src="<?=$imgpath?>orangebar_right.gif"></td>
					</tr>
					<tr>
						<td class="label">Total Space :</td>
						<td class="value"><img src="<?=$imgpath?>bluebar_left.gif"><img src="<?=$imgpath?>bluebar_middle.gif" width="100" height="14"><img src="<?=$imgpath?>bluebar_right.gif"></td>
					</tr>
				</table>
			</td>
			<td valign="top">
				<table>
					<tr>
						<td colspan="2"><a class="iltc">Hard Drives</a></td>
					</tr>
					<?
					// Disk information
					$i = 0;
					settype($totalDiskSpace, "float");
					while ($i < count($disks))
						{
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
						
					if ($diskthreshold > $free) {
						$strfreedisk = 'orange';
					} else {
						$strfreedisk = 'green';
					}
					?>
					<tr>
						<td class="label_a" colspan="2">Drive <?=$disks[$i]?></td>
					</tr>
					<tr>
						<td class="label">Free Space:</td>
						<td class="value"><img src="<?=$imgpath?><?=$strfreedisk?>bar_left.gif"><img src="<?=$imgpath?><?=$strfreedisk?>bar_middle.gif" width="<?=$percentFree?>" height="14"><img src="<?=$imgpath?><?=$strfreedisk?>bar_right.gif">
								<?=$showdiskfree?> (<?=$percentFree?>%)
						</td>
					</tr>
					<tr>
						<td class="label">Used Space:</td>
						<td class="value"><img src="<?=$imgpath?><?=$strfreedisk?>bar_left.gif"><img src="<?=$imgpath?><?=$strfreedisk?>bar_middle.gif" width="<?=$percentUsed?>" height="14"><img src="<?=$imgpath?><?=$strfreedisk?>bar_right.gif">
								<?=$showdiskused?> (<?=$percentUsed?>%)
						</td>
					</tr>
					<tr>
						<td class="label">Total Space:</td>
						<td class="value"><img src="<?=$imgpath?>bluebar_left.gif"><img src="<?=$imgpath?>bluebar_middle.gif" width="100" height="14"><img src="<?=$imgpath?>bluebar_right.gif"> 
								<?=$showdisktotal?> MB
						</td>
					</tr>
					<tr>
						<td class="label">&nbsp;</td>
						<td class="value">&nbsp;</td>
					</tr>
					<?
						$i++;
						}
					?>
				</table>
			</td>

	</table>
		
	<?
temp_tep();
?>