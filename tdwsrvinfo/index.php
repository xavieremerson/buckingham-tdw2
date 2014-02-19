<?php
require_once("config.php");
require_once("parsetemplate.php");

global $cacheStatus;
if($cacheTime)
	{
	if(!file_exists($cacheDir))
		{
		mkdir($cacheDir);
		}
	if(file_exists($cacheDir."/index.html"))
		{
		$cacheFileTime = filemtime($cacheDir."/index.html");
		$cacheTimeLimit = time() - $cacheTime;
		if($cacheFileTime > $cacheTimeLimit)
			{
			$cacheFile = file_get_contents($cacheDir."/index.html");
			echo $cacheFile;
			$cacheStatus = true;
			}
			else
			{
			$cacheFile = fopen($cacheDir."/index.html", "w+");
			rewind($cacheFile);
			$cacheStatus = false;
			}
		}
	}
	
if (!$cacheStatus)
	{
	clearstatcache();
	// OK, we've got to generate some info for you...
	
	// RAM Info
	$template = new ParseTemplate;
	$loadTemplate = $template->loadTemplate($templateToUse);
	if(!$loadTemplate)
		{
		die("FATAL ERROR: could not open template file ".$templateToUse."");
		}
	
	$memoryInfo = explode("\n", shell_exec(".\\exe\\memory.exe"));
	$memory["totalPhysical"] = round($memoryInfo[4] / 1048576, 2);
	$memory["totalFree"] = round($memoryInfo[5] / 1048576, 2);
	$memory["totalUsed"] = $memory["totalPhysical"] - $memory["totalFree"];
	$memory["totalPercentFree"] = round(($memory["totalFree"] / $memory["totalPhysical"]) * 100, 2);
	$memory["totalPercentUsed"] = round(($memory["totalUsed"] / $memory["totalPhysical"]) * 100, 2);
	$template->replace("<!--MEMORY_FREE-->", $memory["totalFree"]);
	$template->replace("<!--MEMORY_USED-->", $memory["totalUsed"]);
	$template->replace("<!--MEMORY_PERCENTFREE-->", $memory["totalPercentFree"]);
	$template->replace("<!--MEMORY_PERCENTUSED-->", $memory["totalPercentUsed"]);
	$template->replace("<!--MEMORY_TOTAL-->", $memory["totalPhysical"]);
	
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
	$template->replace("<!--SERVER_INFO_UPTIME-->", $theuptime);
	
	// CPU's
	$CPU = getenv("NUMBER_OF_PROCESSORS")." * ".getenv("PROCESSOR_IDENTIFIER") ." (Architecture: ".getenv("PROCESSOR_ARCHITECTURE").")";
	$template->replace("<!--CPU_INFO-->", $CPU);
	
	
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
	$totalSent = round($sent / 1048576, 2);
	$template->replace("<!--NETWORK_INFO_RECEIVED-->", $totalReceive);
	$template->replace("<!--NETWORK_INFO_SENT-->", $totalSent);

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
	
	$template->replace("<!--SERVER_INFO_TIME-->", $serverTime);
	$template->replace("<!--SERVER_INFO_HOSTNAME-->", $serverHostName);
	$template->replace("<!--SERVER_INFO_IP-->", $serverIp);
	$template->replace("<!--SERVER_INFO_OS-->", $serverOs[1]);
	$template->replace("<!--SERVER_INFO_SOFTWARE-->", $serverSoftware);
	$template->replace("<!--SERVER_INFO_PROTOCOL-->", $serverProtocol);
	$template->replace("<!--SERVER_INFO_PORT-->", $serverPort);
	$template->replace("<!--SERVER_INFO_ADMIN-->", $serverAdmin);
	
	
	// Disk information
	$i = 0;
	settype($totalDiskSpace, "float");
	while ($i < count($disks))
		{
		$free = round(disk_free_space($disks[$i]) / 1048576, 2);
		$total = round(disk_total_space($disks[$i]) / 1048576, 2);
		$used = $total - $free;
		$percentUsed = round(($used / $total) * 100, 2);
		$percentFree = round(($free / $total) * 100, 2);
		$diskInfo[] = array($free, $used, $total, $percentFree, $percentUsed, $disks[$i]);
		$totalDiskSpace = $totalDiskSpace + $total;
		$i++;
		}
	
	$diskTemplate = $template->getPiece("<!--SECTION_DISKINFO-->", "<!--/SECTION_DISKINFO-->");
	$i = 0;
	$tmp = "";
	while ($i < count($diskInfo))
		{
		$thisDisk = $template->replace("<!--DISK_FREE-->", $diskInfo[$i][0], $diskTemplate[0]);
		$thisDisk = $template->replace("<!--DISK_USED-->", $diskInfo[$i][1], $thisDisk);
		$thisDisk = $template->replace("<!--DISK_SPACE-->", $diskInfo[$i][2], $thisDisk);
		$thisDisk = $template->replace("<!--DISK_PERCENTFREE-->", $diskInfo[$i][3], $thisDisk);
		$thisDisk = $template->replace("<!--DISK_PERCENTUSED-->", $diskInfo[$i][4], $thisDisk);
		$thisDisk = $template->replace("<!--DISK_NAME-->", $diskInfo[$i][5], $thisDisk);
		$tmp .= $thisDisk;
		$i++;
		}
	$template->compileSection($diskTemplate[1], $tmp, $diskTemplate[2]);
	$template->replace("<!--DISK_TOTALDISKSPACE-->", $totalDiskSpace);
	
	if($cacheTime)
		{
		fwrite($cacheFile, $template->display());
		fclose($cacheFile);
		}
	echo $template->display();
	}
?>