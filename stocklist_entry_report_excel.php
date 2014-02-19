<?
  include('includes/dbconnect.php');
  include('includes/global.php'); 
	include('includes/functions.php');

if ($_POST and $datefrom != "" and $dateto != "") {
//==================================================================================================

$output_filename = date('mdY')."_watchlist.xls";
$fp = fopen($exportlocation.$output_filename, "w");

$str = '<html xmlns="http://www.w3.org/1999/xhtml">
				<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8" /></head>
				<body>';
fputs ($fp, $str);


$str = '<table width="610" border="1" cellspacing="0" cellpadding="0">
					<tr>
						<td width="20"><b>#</b></td>
						<td width="80"><b>Symbol</b></td>
						<td width="80"><b>Date Start</b></td>
						<td width="80"><b>Date End</b></td>
						<td width="100"><b>Entered By</b></td>
						<td width="250"><b>Comment</b></td>
					</tr>';
fputs ($fp, $str);

//bcm_date_added  bcm_cusip  bcm_datetime_start  bcm_datetime_stop  bcm_added_by  bcm_open_end  bcm_comment  bcm_isactive 

$qry_rlist = "SELECT * FROM `bcm_watchlist` 
						 where bcm_datetime_start > '" . format_date_mdy_to_ymd($datefrom) ."' and bcm_datetime_start < '".business_day_forward(strtotime(format_date_mdy_to_ymd($dateto)),1). "'
						 and bcm_isactive = 1 
						 ORDER BY bcm_cusip asc"; //bcm_cusip, bcm_datetime_stop 


						 
$result_rlist = mysql_query($qry_rlist) or die(tdw_mysql_error($qry_rlist));

$count_row = 1;
while ($row = mysql_fetch_array($result_rlist)) {

	if ($row["bcm_datetime_stop"] == "2099-12-31 00:00:00") {
		$str_show_end = "";
	} else {
		$str_show_end = date("m/d/Y h:ia",strtotime($row["bcm_datetime_stop"]));
	}
	
	$str = '<tr>
						<td>'.$count_row.'</td>
						<td>'.$row["bcm_cusip"].'</td>
						<td>'.date("m/d/Y h:ia",strtotime($row["bcm_datetime_start"])).'</td>
						<td>'.$str_show_end.'</td>
						<td>'.get_user_by_id($row["bcm_added_by"]).'</td>
						<td>'.$row["bcm_comment"].'</td>
					</tr>';
	fputs ($fp, $str);

	$count_row++;
}

$str = '</table>
	</body>
</html>';
fputs ($fp, $str);

fclose($fp);

Header("Location: http://192.168.20.63/tdw/fileserve_xls.php?l=data/exports/&f=".$output_filename);
//==================================================================================================
} else {
print_r($_POST);
echo "Date criteria not entered. Please close this window and try again.";
exit;
}


//echo "Location: data/exports/"."EmployeeAccounts_".Date("m-d-Y").".csv";

//*******************************************************************************************
/*
//This works!
//header("Location: data/exports/".$output_filename);

$export_file = $output_filename; //"my_name.xls";
$myFile = "data/exports/".$output_filename;

header('Pragma: public');
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");                  // Date in the past    
header('Last-Modified: '.gmdate('D, d M Y H:i:s') . ' GMT');
header('Cache-Control: no-store, no-cache, must-revalidate');     // HTTP/1.1 
header('Cache-Control: pre-check=0, post-check=0, max-age=0');    // HTTP/1.1
header ("Pragma: no-cache");
header("Expires: 0");
header('Content-Transfer-Encoding: none');
header('Content-Type: application/vnd.ms-excel;');  // This should work for IE & Opera
header("Content-type: application/x-msexcel");      // This should work for the rest
header('Content-Disposition: attachment; filename="'.basename($output_filename).'"');
readfile($myFile);
*/
//**********************************************************************************************
?>