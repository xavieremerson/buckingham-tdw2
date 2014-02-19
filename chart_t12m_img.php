<?  
//+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
// outputs chart for trailing 12 months with argument $clnt
//+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

include('includes/dbconnect.php');
include('includes/functions.php');
include('includes/global.php');

	require_once("includes/cd_win32/lib/phpchartdir.php");

//get trailing 12 months for each client
function trailing_12_months() {

	//given that TDW starts with NFS Data as of JAN 18th 2006, lets start the date ranges from commission month 2006 Jan,
	//reverse chronological order
	
	$today_bmqy = get_brok_mqy(date('Y-m-d'));
	$arr_today_bmqy = explode('-',$today_bmqy);

	$str_output_options = "";
	
	for ($i=0; $i<12; $i++) {
		
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

	$clnt_name = db_single_val("select trim(clnt_name) as single_val from int_clnt_clients where clnt_code = '".$clnt."' limit 1");
	//xdebug("clnt_name",$clnt_name);
	$str_brok_months = trailing_12_months();
	//xdebug("str_brok_months",$str_brok_months);
	$arr_brok_months = explode(',',$str_brok_months);
	//print_r($arr_brok_months);


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
	foreach ($arr_all_data_points as $k=>$v) {
		$arr_comm_new[] = ($v == 0) ? '--' : number_format(round($v,0),0,'.',',');
		$arr_comm_new_y_val[] = ($v == 0) ? 0 : $v;
	}


	//create x-axis labels
	foreach ($arr_brok_months as $k=>$v) {
	$arr_temp = explode("^",$v);
	$arr_months_new[] = $arr_temp[0]."\n".substr($arr_temp[1],2,2);
	}

	# The data for the bar chart
	$data = array_reverse($arr_comm_new_y_val); //array(85, 156, 179.5, 211, 123,12,12,12,12,12,12,12); //
	//show_array($data);
	# The labels for the bar chart
	$labels = array_reverse($arr_months_new); //array("Mon", "Tue", "Wed", "Thu", "Fri");
	//show_array($labels);
	
	# Create a XYChart object of size 250 x 250 pixels
	$c = new XYChart(600, 260, 0xEEEEEE, 1, 1);
	
	# Add a title to the chart using 18pts Times Bold Italic font
	$c->addTitle("Trailing 12 Months : ".$clnt_name." (".$clnt.")", "arial.ttf", 11);
	
	# Set the plotarea at (30, 20) and of size 200 x 200 pixels
	$c->setPlotArea(50, 20, 500, 200, $c->linearGradientColor(60, 40, 60, 280, 0xffffff, 0x666666), -1, 0x0000ff, 0x0000ff);
	
	# Add a logo to the chart written in CDML as the bottom title aligned to the bottom
	# right
	$c->addTitle2(BottomRight, "<*block,valign=absmiddle*><*block*><*color=FF*><*font=timesbi.ttf,size=10*>TDW");

	# Show the same scale on the left and right y-axes
	$c->syncYAxis();
	
	# Add a bar chart layer using the given data
	//$c->addBarLayer($data, 0xFF6A22);
	
	$barLayerObj = $c->addBarLayer($data, 0x4040FF);
	$barLayerObj->setBarShape(CircleShape);
	
	# Set the labels on the x axis.
	$c->xAxis->setLabels($labels);
	
	# output the chart
	header("Content-type: image/png");
	print($c->makeChart2(PNG));