<?
include('./includes/functions.php');
include('./includes/generate_pdf.php');
include('./includes/global.php');
include('./includes/dbconnect.php');


echo date('Y-m-d');

$arr_yrs = array();
for ($i=0;$i<3;$i++) {
	$arr_yrs[] = date('Y') - $i;
}
print_r($arr_yrs);
?>