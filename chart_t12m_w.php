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


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Trailing 12 Months Chart for <?=$clnt?></title>
<style type="text/css">
<!--
.lbl {
	font-family: Arial, Helvetica, sans-serif;
	font-size: 11px;
	color: #000099;
}
.dat {
	font-family: Arial, Helvetica, sans-serif;
	font-size: 11px;
	color: #333333;
}
-->
</style>
</head>
<body>
<img src="chart_t12m_img.php?clnt=<?=$clnt?>" border="0" />
<?
	$clnt_name = db_single_val("select trim(clnt_name) as single_val from int_clnt_clients where clnt_code = '".$clnt."' limit 1");
	//xdebug("clnt_name",$clnt_name);
	$str_brok_months = trailing_12_months();
	//xdebug("str_brok_months",$str_brok_months);
	$arr_brok_months = explode(',',$str_brok_months);
	//print_r($arr_brok_months);


	foreach($arr_brok_months as $k=>$v) {
		$arr_mon_year = explode('^',$v);
		$arr_dates = get_commission_month_dates($arr_mon_year[0], $arr_mon_year[1]);
	
		$query_qc = "SELECT trad_advisor_code, sum(trad_quantity) as quant, sum(trad_commission) as comm 
																	FROM mry_comm_rr_trades 
																	WHERE trad_advisor_code = '".$clnt."'
																	AND trad_trade_date between '".$arr_dates[0]."' and '".$arr_dates[1]."' 
																	GROUP BY trad_advisor_code";
		//xdebug("query_qc",$query_qc);
		$result_qc = mysql_query($query_qc) or die(tdw_mysql_error($query_qc));
		
		//check if there is data, if not then assign zero to the array container.
		if (mysql_num_rows($result_qc)==0) {
					$arr_all_data_points[$v] = 0;
		} else {
				while($row_qc = mysql_fetch_array($result_qc)) {
					//xdebug("q/c",$row_qc["quant"]."^".$row_qc["comm"]);
					$arr_data[$arr_mon_year[0].substr($arr_mon_year[1],2,2)] = $row_qc["quant"]."^".$row_qc["comm"];
					$arr_months[] = $arr_mon_year[0]."\n".substr($arr_mon_year[1],2,2);
					$arr_comm[] = number_format(round($row_qc["comm"],0),0,'.',',');
					$arr_all_data_points[$v] = $row_qc["comm"];
				}
		}
	}


	//print_r($arr_all_data_points);
	//print_r($arr_data);
	//print_r($arr_comm);
	
	//This is a new method for filling the array to 12 appropriate values
	foreach ($arr_all_data_points as $k=>$v) {
		$arr_comm_new[] = ($v == 0) ? '--' : number_format(round($v,0),0,'.',',');
	}


	//create x-axis labels
	foreach ($arr_brok_months as $k=>$v) {
	$arr_temp = explode("^",$v);
	$arr_months_new[] = $arr_temp[0]." ".substr($arr_temp[1],2,2);
	}

	# The data for the bar chart
	$d = array_reverse($arr_comm_new); //array(85, 156, 179.5, 211, 123,12,12,12,12,12,12,12); //
	//show_array($data);
	# The labels for the bar chart
	$l = array_reverse($arr_months_new); //array("Mon", "Tue", "Wed", "Thu", "Fri");
	//show_array($labels);

echo '<table width="600">';
echo "<tr class='lbl'><td>$l[0]</td><td>$l[1]</td><td>$l[2]</td><td>$l[3]</td><td>$l[4]</td><td>$l[5]</td><td>$l[6]</td><td>$l[7]</td><td>$l[8]</td><td>$l[9]</td><td>$l[10]</td><td>$l[11]</td><td>$l[12]</td></tr>";
echo "<tr class='dat'><td>$d[0]</td><td>$d[1]</td><td>$d[2]</td><td>$d[3]</td><td>$d[4]</td><td>$d[5]</td><td>$d[6]</td><td>$d[7]</td><td>$d[8]</td><td>$d[9]</td><td>$d[10]</td><td>$d[11]</td><td>$d[12]</td></tr>";

?>
</table>
</body>
</html>
