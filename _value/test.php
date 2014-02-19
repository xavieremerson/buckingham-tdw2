<?
ob_start();

for ($i=0;$i<10;$i++) {
	echo $i."\n<br>";
			ob_flush();
			flush();
			ob_flush();
			flush();
			ob_flush();
			flush();
				sleep(1);
}
?>