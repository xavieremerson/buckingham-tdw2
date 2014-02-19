<?php 
//get TDW functions
require_once("../../includes/functions.php");
require_once("../../includes/dbconnect.php");
require_once("../../includes/global.php");

$qry_data = "select * from eml_research_compliance where eml_trade_date = '".$vd."' and eml_isactive = 1";
$result_data = mysql_query($qry_data) or die(tdw_mysql_error($qry_data));
while($row_data = mysql_fetch_array($result_data)) {
	$str_eml_file = $row_data['eml_html_file'];
	$str_eml_file = str_replace("<font color='blue'>Research Dissemination</font>", "<font color='blue'>Research Dissemination Compliance Mailbox</font>",$str_eml_file);
	$str_eml_file = str_replace("<font color='blue'>Investment Update</font>", "<font color='blue'>Investment Update Compliance Mailbox</font>",$str_eml_file);
	$str_eml_file = str_replace("<font color='blue'>QuickNote</font>", "<font color='blue'>QuickNote Compliance Mailbox</font>",$str_eml_file);
	echo $str_eml_file;	
	
	
	//echo $row_data['eml_html_file'];
}
?> 