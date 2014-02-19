<?
include('includes/functions.php');
include('includes/dbconnect.php'); 
include('includes/global.php');
require_once 'Spreadsheet/Excel/Writer.php';

//initiate page load time routine
$time=getmicrotime(); 


//get trailing 12 months for each client
function trailing_12_months() {

	//given that TDW starts with NFS Data as of JAN 18th 2006, lets start the date ranges from commission month 2006 Jan,
	//reverse chronological order
	
	$today_bmqy = get_brok_mqy(date('Y-m-d'));
	//$today_bmqy = get_brok_mqy('2007');
	
	$arr_today_bmqy = explode('-',$today_bmqy);

	$str_output_options = "";
	
	for ($i=0; $i<13; $i++) {
		
		$lastmonth = mktime(0, 0, 0, $arr_today_bmqy[0]-$i, "01", $arr_today_bmqy[2]);
		
			if ( $lastmonth < strtotime('2006-01-01')) {
				//do nothing
			} else {
				$putyear = date('Y', $lastmonth);
				$putmonth = date('M', $lastmonth);
				$str_output_options .=	$putmonth.'^'.$putyear.',';			
			}
	}
	$str_output_options = substr($str_output_options,0,strlen($str_output_options)-1);
	return $str_output_options;
}

	if (!$clnt) {
	$clnt = 'COUG';
	} else {
	$clnt = strtoupper(trim($clnt));
	}


//We give the path to our file here
//generate a random filename
$xlfilename = date('Y-m-d_h.i.s.a')."__".md5(rand(1000000000,9999999999)).".xls";
$wkb = new Spreadsheet_Excel_Writer('data/xls/'.$xlfilename);

//FORMATTING IN THE FOLLOWING FILE
include('xl_format_generic.php');

//xdebug("clnt_name",$clnt_name);
$str_brok_months = trailing_12_months();
//xdebug("str_brok_months",$str_brok_months);
$arr_brok_months = explode(',',$str_brok_months);
//print_r($arr_brok_months);

	//create x-axis labels
	foreach ($arr_brok_months as $k=>$v) {
	$arr_temp = explode("^",$v);
	$arr_months_new[] = $arr_temp[0]." ".substr($arr_temp[1],2,2);
	}
	
	# Column Heading
	$l = array_reverse($arr_months_new); 
	//show_array($labels);
	
	$wks =& $wkb->addWorksheet("12Months");
	$wks->setLandscape ();
	$wks->setFooter ("TDW (Buckingham : Trade Data Warehouse)", $margin=0.5);

	//Apply Row/Column Formatting
	$wks->setColumn(0, 0, 0.2);
	$wks->setColumn(1, 1, 30);
	$wks->setColumn(2, 2, 0.2);
	$wks->setColumn(3, 3, 8);
	$wks->setColumn(4, 4, 8);
	$wks->setColumn(5, 5, 8);
	$wks->setColumn(6, 8, 8);
	$wks->setColumn(9, 9, 8);
	$wks->setColumn(10, 10, 8);
	$wks->setColumn(11, 11, 8);
	$wks->setColumn(12, 12, 8);
	$wks->setColumn(13, 13, 8);
	$wks->setColumn(14, 16, 8);
	$wks->setColumn(17, 17, 8);
	$wks->setColumn(18, 18, 8);
	$wks->setColumn(19, 19, 8);
	$wks->setColumn(20, 20, 8);
	$wks->setColumn(21, 21, 8);
	
	//Write the Column Headings
	$wks->write(1, 1, "Client", $format_title_1);

	$zcount = 3;
	foreach ($l as $k=>$v) {
		$wks->write(1, $zcount, $v, $format_title_1);
		$zcount = $zcount + 1;
	}
		$wks->write(1, $zcount, "Total", $format_title_1);

	//PROCESS THE DATA
	//echo "Processing...";
	$datarow_count = 2;
	$query_clnt = "SELECT distinct(clnt_code) as clnt 
																FROM int_clnt_clients 
																WHERE clnt_isactive  = 1";
	$result_clnt = mysql_query($query_clnt) or die(tdw_mysql_error($query_clnt));
	while($row_clnt = mysql_fetch_array($result_clnt)) {
	
	$clnt = $row_clnt["clnt"];
	$clnt_name = db_single_val("select trim(clnt_name) as single_val from int_clnt_clients where clnt_code = '".$clnt."' limit 1");

	//echo $clnt_name . "<br>";
	//ob_flush();
	//flush();

	foreach($arr_brok_months as $k=>$v) {
		$arr_mon_year = explode('^',$v);
		$arr_dates = get_commission_month_dates($arr_mon_year[0], $arr_mon_year[1]);
	
									//##############################################################################################
									//CHECKS
									//chk_chek_payments_etc
									//auto_id  chek_amount  chek_type  chek_advisor  chek_comments  chek_date  chek_entered_by  chek_entered_datetime  chek_processed  chek_isactive 
									
									$query_checks = "SELECT chek_advisor, sum(chek_amount) as comm 
																								FROM chk_chek_payments_etc 
																								WHERE chek_advisor = '".$clnt."'
																								AND chek_date between '".$arr_dates[0]."' and '".$arr_dates[1]."' 
																								AND chek_isactive = 1
																								GROUP BY chek_advisor";
									//xdebug("query_qc",$query_qc);
									$result_checks = mysql_query($query_checks) or die(tdw_mysql_error($result_checks));
									
									//check if there is data, if not then assign zero to the array container.
									if (mysql_num_rows($result_checks)==0) {
												$arr_all_data_points[$v] = 0;
									} else {
											while($row_checks = mysql_fetch_array($result_checks)) {
												$arr_months[] = $arr_mon_year[0]."\n".substr($arr_mon_year[1],2,2);
												$arr_comm[] = number_format(round($row_checks["comm"],0),0,'.',',');
												$arr_all_data_points[$v] = $row_checks["comm"];
											}
									}
									//##############################################################################################

									//**********************************************************************************************
									//COMMISSIONS
									$query_qc = "SELECT trad_advisor_code, sum(trad_quantity) as quant, sum(trad_commission) as comm 
																								FROM mry_comm_rr_trades 
																								WHERE trad_advisor_code = '".$clnt."'
																								AND trad_trade_date between '".$arr_dates[0]."' and '".$arr_dates[1]."' 
																								AND trad_is_cancelled = 0
																								GROUP BY trad_advisor_code";
									//xdebug("query_qc",$query_qc);
									$result_qc = mysql_query($query_qc) or die(tdw_mysql_error($query_qc));
									
									//check if there is data, if not then assign zero to the array container.
									if (mysql_num_rows($result_qc)==0) {
												//do nothing
												//$arr_all_data_points[$v] = $arr_all_data_points[$v];
									} else {
											while($row_qc = mysql_fetch_array($result_qc)) {
												//xdebug("q/c",$row_qc["quant"]."^".$row_qc["comm"]);
												$arr_data[$arr_mon_year[0].substr($arr_mon_year[1],2,2)] = $row_qc["quant"]."^".$row_qc["comm"];
												$arr_months[] = $arr_mon_year[0]."\n".substr($arr_mon_year[1],2,2);
												$arr_comm[] = number_format(round($row_qc["comm"],0),0,'.',',');
												$arr_all_data_points[$v] = $arr_all_data_points[$v] + $row_qc["comm"];
											}
									}
									//**********************************************************************************************

	}

	//This is a new method for filling the array to 12 appropriate values
	$arr_comm_new = array();
	foreach ($arr_all_data_points as $k=>$v) {
		$arr_comm_new[] = ($v == 0) ? '0' : $v;
	}


	# The data for the bar chart
	$d = array_reverse($arr_comm_new); //array(85, 156, 179.5, 211, 123,12,12,12,12,12,12,12); //
	//show_array($data);

	$wks->write($datarow_count, 1, $clnt_name, $format_text_1);
	
	//print_r($d);
	$col_count = 3;
	foreach ($d as $k=>$v) {
		$wks->writeNumber($datarow_count, $col_count, $v, $format_currency_0);
		$col_count = $col_count + 1;
	}
	$wks->writeFormula($datarow_count, $col_count, "=SUM(D".($datarow_count+1).":P".($datarow_count+1).")", $format_currency_0);

	$datarow_count = $datarow_count + 1;
}
	// We still need to explicitly close the workbook
	$wkb->close();
	
	Header("Location: http://192.168.20.63/tdw/data/xls/".$xlfilename);

/*	//show page load time
	echo "Report generated in ". sprintf("%01.2f",((getmicrotime()-$time)/1000))." s.\n<br>"; 						
?>
<a href="http://192.168.20.63/tdw/data/xls/<?=$xlfilename?>" target="_blank">Click here to download the generated report (File Format: Excel)</a><br />
<?
xdebug("Process completed at :",date('m/d/Y H:i:s a'));
*/
?>