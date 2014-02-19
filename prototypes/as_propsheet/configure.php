<?

$handle = @fopen('config_tdw.php', "r");
if ($handle) {
	 $i = 0;
   while (!feof($handle)) {
       $lines[] = fgets($handle, 4096);
			 if (substr($lines[$i],0,2) != "<?" AND substr($lines[$i],0,2) != "?>") {
			 	echo $lines[$i];
			 }
			 $i++;
   }
   fclose($handle);
} 


?>