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

	//create x-axis labels
	foreach ($arr_yr_total_vals as $k=>$v) {
		$arr_x_labels[] = $k; 
	}

	//create y-axis values
	foreach ($arr_yr_total_vals as $k=>$v) {
		$arr_y_values[] = number_format(round(($v/1000),0),0,"",",")."K";
	}

	# The data for the bar chart
	$data = $arr_y_values; //array(85, 156, 179.5, 211, 123,12,12,12,12,12,12,12); //
	//show_array($data);
	# The labels for the bar chart
	$labels = $arr_x_labels; //array("Mon", "Tue", "Wed", "Thu", "Fri");
	//show_array($labels);
	
	# Create a XYChart object of size 250 x 250 pixels
	$c = new XYChart(600, 260, 0xEEEEEE, 1, 1);
	
	# Add a title to the chart using 18pts Times Bold Italic font
	$c->addTitle("Annual Revenue since ".$arr_x_labels[0]." : ".$clnt_name." (".$clnt.")", "arial.ttf", 11);
	
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