<?

	$fp = fopen ("functions.php", "r"); 
	while (!feof ($fp)) { 
		
		$content = fgets( $fp, 4096 ); 
		
		echo $content . "<br>";
			ob_flush();
			flush();
			sleep(1);		
	}
	fclose ($fp); 
?>