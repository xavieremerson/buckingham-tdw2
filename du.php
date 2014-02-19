<?php


function HumanSize($Bytes)
{
  $Type=array("", "kilo", "mega", "giga", "tera", "peta", "exa", "zetta", "yotta");
  $Index=0;
  while($Bytes>=1024)
  {
    $Bytes/=1024;
    $Index++;
  }
  return("".round($Bytes,2)." ".$Type[$Index]."bytes");
}


$arr_dirs = array("C","D","E");

    foreach ($arr_dirs as $k=>$drive)
    {
        if (is_dir($drive.':'))
        {
            $used_space          = 
						$freespace           = disk_free_space($drive.':');
            $total_space         = disk_total_space($drive.':');
            $percentage_free     = $freespace ? round($freespace / $total_space, 2) * 100 : 0;
            echo $drive.': '.HumanSize($freespace).' / '.HumanSize($total_space).' ['.$percentage_free.'%]<br />';
        }
    }































echo "<center>----------=====[~ENUMERATION~]=====----------</center><br>";
echo "<font color=\"red\"><h3>Server Info</h3></font>";
echo "<b>Operating system info (uname)</b> - " . php_uname() . "<br>";
echo "<b>Server software</b> - " . $_SERVER['SERVER_SOFTWARE'] . "<br>";
echo "<b>Current owner of this file</b> - " . get_current_user() . "<br>";
echo "<b>File owner's user ID</b> - " . getmyuid() . "<br>";
echo "<b>File owner's group ID</b> - " . getmygid() . "<br>";

echo "<b>Current available disk space (GB)</b> - ";
if (preg_match("/Win/", php_uname())) {
	echo (disk_free_space("C:") / 1024000000) . " out of " . (disk_total_space("C:") / 1024000000) . "<br>";
} else {
	echo (disk_free_space("/") / 1024000000) . " out of " . (disk_total_space("/") / 1024000000) . "<br>";
}

echo "<b>Current directory</b> - " . getcwd() . "<br>";

echo "<font color=\"green\"><h3>Your Info</h3></font>";
echo "<b>IP Address</b> - " . $_SERVER['REMOTE_ADDR'] . "<br>";
echo "<b>Hostname</b> - " . gethostbyaddr($_SERVER['REMOTE_ADDR']) . "<br>";
echo "<b>User Agent</b> - " . $_SERVER['HTTP_USER_AGENT'] . "<br>";

?>