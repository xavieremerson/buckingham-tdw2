<?
//+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
// outputs chart for trailing 12 months with argument $clnt
//+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

include('includes/dbconnect.php');
include('includes/functions.php');
include('includes/global.php');
require_once("includes/cd_win32/lib/phpchartdir.php");

//get trailing 6 years
function trailing_6_years() {
	
	$cur_year = date('Y');
	$arr_years = array();
	for ($i=1;$i<7;$i++) {
		$arr_years[] = $cur_year - $i;
	}
	$arr_years = array_reverse($arr_years);
	return $arr_years;
}

$arr_trailing_years = trailing_6_years();

//show_array($arr_trailing_years);

if (!$clnt) {
$clnt = 'CAPG';
} else {
$clnt = strtoupper(trim($clnt));
}


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Yearly Revenue Chart for <?=$clnt?></title>
<style type="text/css">
<!--
.lbl {	font-family: Arial, Helvetica, sans-serif;	font-size: 11px;	color: #000099;}
.dat {	font-family: Arial, Helvetica, sans-serif;	font-size: 11px;	color: #333333;
}
-->
</style>
</head>
<body>
<img src="chart_mult_years_img.php?clnt=<?=$clnt?>" border="0" />
<?
	$clnt_name = db_single_val("select trim(clnt_name) as single_val from int_clnt_clients where clnt_code = '".$clnt."' limit 1");

	$arr_yr_total_vals = array();
	foreach($arr_trailing_years as $k=>$val_year) {
		$val_yearly_total = db_single_val("select round(sum(yrt_commission),0) as single_val 
																				from yrt_yearly_total_lookup 
																				where yrt_advisor_code = '".$clnt."'
																				and yrt_year = '".$val_year."'");
		$arr_yr_total_vals[$val_year] = $val_yearly_total;
	}

	//show_array($arr_yr_total_vals);
	
	//Annualized Current Year
	$val_curr_year = db_single_val("select round(sum(trad_commission),0) as single_val
																	from mry_comm_rr_trades 
																	where trad_advisor_code = '".$clnt."' 
																	and trad_trade_date between '".date('Y')."-01-01' and '".date('Y')."-12-31' 
																	and trad_is_cancelled = 0");
																	
	$annualized_cur_year = round(($val_curr_year/date('z'))*365,0);
	
	//xdebug("annualized_cur_year", $annualized_cur_year);	
	
	$arr_yr_total_vals[date('Y')] = $annualized_cur_year;
	//show_array($arr_yr_total_vals);

	//This is a new method for filling the array to 7 annual values
	foreach ($arr_yr_total_vals as $k=>$v) {
		$arr_yr_total_vals_new[] = ($v == 0) ? '--' : number_format(round($v,0),0,'.',',');
	}

	//show_array($arr_yr_total_vals_new);

	//exit;
	//create x-axis labels
	foreach ($arr_yr_total_vals as $k=>$v) {
		$arr_x_labels[] = $k; 
	}

	//create y-axis values
	foreach ($arr_yr_total_vals as $k=>$v) {
		$arr_y_values[] = number_format(round(($v/1000),0),0,"",",")."K";
	}

	# The data for the bar chart
	
	$d = $arr_y_values; //array(85, 156, 179.5, 211, 123,12,12,12,12,12,12,12); //
	//show_array($data); 
	# The labels for the bar chart
	$l = $arr_x_labels; //array("Mon", "Tue", "Wed", "Thu", "Fri");
	//show_array($labels);

echo '<center><table width="600" border="0">';
echo "<tr class='lbl'><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td><td>$l[0]</td><td>$l[1]</td><td>$l[2]</td><td>$l[3]</td><td>$l[4]</td><td>$l[5]</td><td>$l[6]</td><td>$l[7]</td><td>$l[8]</td><td>$l[9]</td><td>$l[10]</td><td>$l[11]</td><td>$l[12]</td></tr>";
echo "<tr class='dat'><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td><td>$d[0]</td><td>$d[1]</td><td>$d[2]</td><td>$d[3]</td><td>$d[4]</td><td>$d[5]</td><td>$d[6]</td><td>$d[7]</td><td>$d[8]</td><td>$d[9]</td><td>$d[10]</td><td>$d[11]</td><td>$d[12]</td></tr>";
echo "</table></center>";
?>
</table>
</body>
</html>
