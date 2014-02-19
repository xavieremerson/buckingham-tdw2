<?  
//+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
// outputs chart for trailing 12 months with argument $clnt
//+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

include('includes/dbconnect.php');
include('includes/functions.php');
include('includes/global.php');

	require_once("includes/cd_win32/lib/phpchartdir.php");

function get_tier($amt) {
	if ($amt <= 50000) {
		return 4;
	} elseif ($amt > 50000 && $amt <= 100000) {
		return 3;
	} elseif ($amt > 100000 && $amt <= 200000) {
		return 2;
	} elseif ($amt > 200000) {
		return 1;
	} else {
		return "?";
	}
}


if (!$rep) {
$rep = 'BE';
} else {
$rep = strtoupper(trim($rep));
}

//get trailing 6 years
$cur_year = date('Y');
$arr_trailing_years = array();
for ($i=1;$i<7;$i++) {
	$arr_trailing_years[] = $cur_year - $i;
}

$arr_trailing_years = array_reverse($arr_trailing_years,FALSE);
//show_array($arr_trailing_years);
//exit;

//get user_id for Selected Rep.
$user_id = db_single_val("select ID as single_val from users where Initials = '".$rep."' limit 1");
//get user fullname
$user_fullname = db_single_val("select Fullname as single_val from users where ID = '".$user_id."' limit 1");
//get Rep Number for Selected Rep.
$rep_num = array();
$rep_num[] = db_single_val("select rr_num as single_val from users where Initials = '".$rep."' limit 1");
//get Shared Rep Number for Selected Rep.
$qry = "select srep_rrnum 
				from sls_sales_reps  
				where srep_user_id = '".$user_id."' and srep_isactive = 1";  
$result = mysql_query($qry) or die(tdw_mysql_error($qry));
while ( $row = mysql_fetch_array($result) ) {
	$rep_num[] = $row["srep_rrnum"];
}

$str_rep_rr_nums = " ('".implode("','",$rep_num)."') ";

//Yearly Lookup Data
$qry = "select yrt_year, yrt_advisor_code, yrt_rr, yrt_commission 
				from yrt_yearly_total_lookup where yrt_year >= '".(date('Y')-6)."'
				and yrt_rr in ".$str_rep_rr_nums;  
$arr_rep_clients = array();
$result = mysql_query($qry) or die(tdw_mysql_error($qry));
while ( $row = mysql_fetch_array($result) ) {
	$arr_rep_clients[] = $row["yrt_year"]."^".$row["yrt_advisor_code"]."^".$row["yrt_commission"];
}

$tier_year_client_count = array();
foreach ($arr_rep_clients as $k=>$str_vals) {
	$arr_year_code_amount = explode("^",$str_vals);
		$tier_year_client_count[$arr_year_code_amount[0]][get_tier($arr_year_code_amount[2])]= $tier_year_client_count[$arr_year_code_amount[0]][get_tier($arr_year_code_amount[2])] + 1;
}

//show_array($tier_year_client_count);
	//Annualized Current Year
	$qry_cur_yearly_total = "select trad_advisor_code, round(sum(trad_commission),0) as clnt_revenue
													from mry_comm_rr_trades 
													where trad_trade_date between '".date('Y')."-01-01' and '".date('Y')."-12-31' 
													and trad_is_cancelled = 0
													and trad_rr in ".$str_rep_rr_nums." 
													group by trad_advisor_code
													order by trad_advisor_code";
																	
	$result_cur_yearly_total = mysql_query($qry_cur_yearly_total) or die (tdw_mysql_error($qry_cur_yearly_total));
	$arr_cur_yearly_total = array();
	$arr_cur_yearly_total_actual = array();
	while ( $row =
	 mysql_fetch_array($result_cur_yearly_total) ) {
		$annualized_cur_year = round(($row["clnt_revenue"]/date('z'))*365,0);
		$arr_cur_yearly_total_actual[$row["trad_advisor_code"]] = round(($row["clnt_revenue"]/1000),0);
		$arr_cur_yearly_total[$row["trad_advisor_code"]] = $annualized_cur_year; 
	}
	
	//Annualized Current Year Checks
	$qry_cur_yearly_chk_total = "select chek_advisor, round(sum(chek_amount),0) as clnt_revenue
													from chk_chek_payments_etc  
													where chek_date between '".date('Y')."-01-01' and '".date('Y')."-12-31' 
													and chek_isactive = 1
													and chek_reps_and like '%".$rep."%'
													group by chek_advisor
													order by chek_advisor";
																	
	$result_cur_yearly_chk_total = mysql_query($qry_cur_yearly_chk_total) or die (tdw_mysql_error($qry_cur_yearly_chk_total));
	$arr_cur_yearly_chk_total = array();
	$arr_cur_yearly_chk_total_actual = array();
	while ( $row = mysql_fetch_array($result_cur_yearly_chk_total) ) {
		$annualized_cur_year = round(($row["clnt_revenue"]/date('z'))*365,0);
		$arr_cur_yearly_chk_total_actual[$row["chek_advisor"]] = round(($row["clnt_revenue"]/1000),0);
		$arr_cur_yearly_chk_total[$row["chek_advisor"]] = $annualized_cur_year;
	}

	//Combine Combine and Checks.
	$combined_cur_year = array();
	foreach($arr_cur_yearly_total as $clnt_code=>$amount) {
		$combined_cur_year[$clnt_code] = $amount + $arr_cur_yearly_chk_total[$clnt_code];
	}
	foreach($arr_cur_yearly_chk_total as $clnt_code=>$amount) {
		if (!array_key_exists($clnt_code,$combined_cur_year)) {
			$combined_cur_year[$clnt_code] = $amount;
		} 
	}

	$arr_cur_year_tier_count = array();
	foreach($combined_cur_year as $clnt_code=>$amount) {
			$arr_cur_year_tier_count[get_tier($amount)]	= $arr_cur_year_tier_count[get_tier($amount)]+1;
	}

//FINAL DATA SET
foreach ($arr_trailing_years as $k=>$yr_val) {
	$data_set_tier_1[] = $tier_year_client_count[$yr_val][1];
	$data_set_tier_2[] = $tier_year_client_count[$yr_val][2];
	$data_set_tier_3[] = $tier_year_client_count[$yr_val][3];
	$data_set_tier_4[] = $tier_year_client_count[$yr_val][4];
}

	$data_set_tier_1[] = $arr_cur_year_tier_count[1];
	$data_set_tier_2[] = $arr_cur_year_tier_count[2];
	$data_set_tier_3[] = $arr_cur_year_tier_count[3];
	$data_set_tier_4[] = $arr_cur_year_tier_count[4];


# The data for the line chart
$data1 = $data_set_tier_1;
$data2 = $data_set_tier_2;
$data3 = $data_set_tier_3;
$data4 = $data_set_tier_4;

# The labels for the line chart
$labels = array(date('Y')-6, date('Y')-5, date('Y')-4, date('Y')-3, date('Y')-2, date('Y')-1, date('Y'));

# Create an XYChart object of size 600 x 300 pixels, with a light blue (EEEEFF)
# background, black border, 1 pxiel 3D border effect and rounded corners
$c = new XYChart(600, 300, 0xeeeeff, 0x000000, 1);
$c->setRoundedFrame();

# Set the plotarea at (55, 58) and of size 520 x 195 pixels, with white background.
# Turn on both horizontal and vertical grid lines with light grey color (0xcccccc)
$c->setPlotArea(55, 58, 520, 195, 0xffffff, -1, -1, 0xcccccc, 0xcccccc);

# Add a legend box at (50, 30) (top of the chart) with horizontal layout. Use 9 pts
# Arial Bold font. Set the background and border color to Transparent.
$legendObj = $c->addLegend(50, 30, false, "arialbd.ttf", 9);
$legendObj->setBackground(Transparent);

# Add a title box to the chart using 15 pts Times Bold Italic font, on a light blue
# (CCCCFF) background with glass effect. white (0xffffff) on a dark red (0x800000)
# background, with a 1 pixel 3D border.
$textBoxObj = $c->addTitle("Client Tier Numbers by Year for ".$user_fullname, "timesbi.ttf", 15);
$textBoxObj->setBackground(0xccccff, 0x000000, glassEffect());

# Add a title to the y axis
$c->yAxis->setTitle("# of Clients");

# Set the labels on the x axis.
$c->xAxis->setLabels($labels);

# Display 1 out of 3 labels on the x-axis.
$c->xAxis->setLabelStep(1);

# Add a title to the x axis
$c->xAxis->setTitle("Trailing 6 Years");

# Add a line layer to the chart
$layer = $c->addLineLayer2();

# Set the default line width to 2 pixels
$layer->setLineWidth(4);

# Add the three data sets to the line layer. For demo purpose, we use a dash line
# color for the last line
$layer->addDataSet($data1, 0x02d202, "Tier 1");
$layer->addDataSet($data2, 0x8cdc8c, "Tier 2");
$layer->addDataSet($data3, 0xf89616, "Tier 3");
$layer->addDataSet($data4, 0xff0000, "Tier 4");

//$layer->addDataSet($data2, $c->dashLineColor(0x3333ff, DashLine), "Server #3");

# output the chart
header("Content-type: image/png");
print($c->makeChart2(PNG));

exit;


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