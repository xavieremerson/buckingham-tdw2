<?

include('../includes/functions.php');
include('../includes/global.php');
include('../includes/dbconnect.php');


function empty_qry($result) {
	$num_rows = mysql_num_rows($result); 
	if (num_rows > 0) {
	return 1;
	} else {
	return 0;
	}
}
?>
