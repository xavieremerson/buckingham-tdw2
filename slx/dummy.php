<?
include('../includes/dbconnect.php');  
include('../includes/global.php');  
include('../includes/functions.php');  

$arr_advisor = array();
$sql_advisor = "SELECT DISTINCT (`nadd_advisor`) FROM `nfs_nadd` where nadd_advisor not like '%&%' order by nadd_advisor";
$result_advisor = mysql_query($sql_advisor) or die (mysql_error());
$count_advisor = 0;
while ( $row_advisor = mysql_fetch_array($result_advisor) ) {
	if (strlen($row_advisor["nadd_advisor"]) == 4) {
		$arr_advisor[$count_advisor] = $row_advisor["nadd_advisor"];
		$count_advisor++;
	}
}
echo count($arr_advisor);

$arr_symbol = array();
$sql_symbol = "SELECT DISTINCT(`trad_symbol`) FROM `nfs_trades`";
$result_symbol = mysql_query($sql_symbol) or die (mysql_error());
$count_symbol = 0;
while ( $row_symbol = mysql_fetch_array($result_symbol) ) {
		$arr_symbol[$count_symbol] = $row_symbol["trad_symbol"];
		$count_symbol++;
}
echo count($arr_symbol);
//print_r($arr_symbol);
echo "<br>";


$arr_buysell = array();
$arr_buysell[0] = "B";
$arr_buysell[1] = "S";


for ($i=0; $i < 1114; $i++) {
	echo "2006-02-09,".
		  $arr_advisor[rand(0,count($arr_advisor))].
		  ",".
		  $arr_buysell[rand(0,1)].
		  ",".
		  $arr_symbol[rand(0,count($arr_symbol))].
		  ",".
		  rand(8,150).
		  ".".
		  rand(10,99).
		  ",".
		  rand(4000,36000).
		  ",".
		  "0.0".rand(4,5).
		  "<br>"
		  ;
}

foreach ($arr_advisor as $index => $advisor) {
	echo "2006-02-09,".
		  $advisor.
		  ",".
		  rand(2000,50000).
		  ",".
		  rand(50000,1000000).
		  ",".
		  rand(1000000,10000000).
		  ",".
		  rand(50000,1000000).
		  ",".
		  rand(1000000,10000000).
		  "<br>"
		  ;
}

?>

