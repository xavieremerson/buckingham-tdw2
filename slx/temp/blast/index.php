

<?php 
//http://www.google.com/search?hl=en&lr=&ie=UTF-8&q=CenterSys+Group&btnG=Search



for ($i=0;$i<3;$i++){

			$exec_string = "/usr/bin/curl  -o /var/www/html/demo_compliance/temp/blast/xfile".$i.".html ". "http://www.google.com/search?hl=en&lr=&ie=UTF-8&q=CenterSys+Group&btnG=Search";
			echo $exec_string."<br>";
			shell_exec($exec_string);

}
?>   