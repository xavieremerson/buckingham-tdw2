<?
include('../../../includes/dbconnect.php');
include('../../../includes/functions.php'); 
include('../../../includes/global.php'); 


function restrict_end($restrict_start) {
	

}







exit;
for($i=0;$i<50;$i++) {

	$arr_rands = array();
	$arr_chosen = array();

	for($j=0;$j<5;$j++) {

		$val = rand(1,56);
		if ($j > 0) {
			$exit = 1;
			while ($exit > 0) {
				if (in_array($val, $arr_chosen)) {
					$val = rand(1,56);
					echo "val reassigned...".$i."/".$j."/".$k."<br>";
					$arr_chosen[$j] = $val;
					$exit = rand(1,99999);
					echo "exit val ...".$exit."<br>";
				} else {
					$arr_chosen[$j] = $val;
					$exit = 0;
					echo "exit val ...".$exit."<br>";
				}
			}
		} else {
			$arr_chosen[$j] = $val;
		}
	
	}
	$arr_rands = $arr_chosen;

sort($arr_rands);
echo $arr_rands[0]." - ".$arr_rands[1]." - ".$arr_rands[2]." - ".$arr_rands[3]." - ".$arr_rands[4]." / ". rand(1,46)."<br>";
}
?>