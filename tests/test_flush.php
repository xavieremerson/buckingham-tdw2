<?
include 'includes/Thumbnail.class.php';

$j = 0;
for ($i=0;$i<1000000;$i++) {
	$str_rand = rand(1000000,9999999).",".$str_rand;
	if ( $i % 15 == 0  && $i != 0) {
			print_r ($str_rand."<br>", FALSE);
			$str_rand = "";
			ob_flush();
			flush();
			ob_flush();
			flush();
			ob_flush();
			flush();

			if ( $i % 200 == 0  && $i != 0) {
			sleep(1);		
			}
	}
}
?>