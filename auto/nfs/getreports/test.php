<?

echo mktime()."<br>"; 

echo "Filetime for FBNR215A0910.TXT is " . date("m/d/Y",filemtime("\\\\192.168.20.93\\nfs$\\FBNR215A0910.TXT"))."<br>";

echo round(((time() - strtotime(date("m/d/Y",filemtime("\\\\192.168.20.93\\nfs$\\FBNR215A0910.TXT"))))/86400),0) ."<br>";

exit;

//echo date("h:i:s a"); 

?>