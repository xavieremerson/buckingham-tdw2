<?
//error_reporting(0);
include('../includes/global.php');
include('../includes/dbconnect.php');
include('../includes/functions.php');
include('config.php');
				 
$data = explode("^",$str_input);

if ($data[0] == 'csus' or 1==1) {

	if ($data[0] == 'csgb') {
		$symbol = substr($data[1], 0, (strlen($data[1])-2) ) . ".L";
	} elseif ($data[0] == 'clus' OR $data[0] == 'ptus') {
		$symbol = str_replace("+","",$data[1]) . ".X";
	} else {
		$symbol = $data[1];
	}

	$str_company_detail = get_company_detail($symbol);
	//echo $str_company_detail . "<br />\n";
	$acd = explode("^", $str_company_detail); 

	if ($acd[1] != '0.00') {
		echo $id."^".$acd[0]."^".$acd[1];
	} else {
	  //Try to get from the table if it is there.
		$db_valprice = db_single_val("select price as single_val from valuation_info where symbol = '".$data[1]."'");
		if ($db_valprice != "") {
			echo $id."^"." "."^".$db_valprice."^"."Previous Price Input found.";
		} else {
			echo $id."^"." "."^".$acd[1]."^"."&nbsp;&nbsp;&nbsp;&nbsp;Previous Price Input not found.";
		}
	}
}
				 
?>