<?
$msinfo32path = "D:\\tdw\\tdw\\winsysinfo\\msinfo32\\";
$str_shell = $msinfo32path."msinfo32.exe /categories SystemSummary /report ".$msinfo32path."zzz.txt";
shell_exec($str_shell);
//echo $str_shell;
$fh = fopen($msinfo32path."zzz.txt", 'r');
$lines = file($msinfo32path."zzz.txt");
		
//Get the content of the file into a string
foreach ($lines as $key=>$value)
{
	echo $key.">>".$value."<br>";
}

echo $str_whole;
echo "done...";
?>