<?
include('includes/functions.php');
include('includes/global.php');
include('includes/dbconnect.php');

function gpq ($q, $y) { //get previous quarter

	if ($q == 1) {
		$ret_q = 4;
		$ret_y = $y - 1;
	} else if ($q == 2) {
		$ret_q = 1;
		$ret_y = $y;
	} else if ($q == 3) {
		$ret_q = 2;
		$ret_y = $y;
	} else if ($q == 4) {
		$ret_q = 3;
		$ret_y = $y;
	} else {
		$ret_q = 0;
		$ret_y = 0;
	}

  $arr_return = array($ret_q,$ret_y);
	return $arr_return;
}

$test = gpq (1,2009);
print_r($test);
?>