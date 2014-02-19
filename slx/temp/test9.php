						<?
						
	include('includes/functions.php');
		include('includes/dbconnect.php');
 
 
 
 for ($i=0;$i<1000;$i++) {
 
 echo rand(0,9)." ";
 
 }
 
 echo '<br>';
						
						$i = 1;
						while ($i < 90) {

						$previoustime = time() - (60*60*24*$i);
						$previousday = date("Y-m-d", $previoustime);
 
 						if (date("l", $previoustime) == "Sunday") {
						$previoustime = time() - (60*60*24*($i+2));
						$previousday = date("Y-m-d", $previoustime);
						$i = $i+2 + 1;	
 							if ( check_holiday($previousday) == 1) {						
							$previoustime = time() - (60*60*24*($i));
							$previousday = date("Y-m-d", $previoustime);
							$i = $i+1;	
							}
						} elseif (date("l", $previoustime) == "Monday" and check_holiday($previousday) == 1) {
						$previoustime = time() - (60*60*24*($i+3));
						$previousday = date("Y-m-d", $previoustime);
						$i = $i+3 + 1;	
						} else {
						$previousday = "ERROR!";
						$i = $i+1; 						
						}
  						
					 ?>
						<?=date("Y-m-d", time() - (60*60*24*($i-1)))?> => <?=date("m-d-Y", time() - (60*60*24*($i-1)))?> <br>
						
						<?
						}						
						?>