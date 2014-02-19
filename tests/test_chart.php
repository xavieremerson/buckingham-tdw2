<?
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

$clnt_name = db_single_val("select trim(clnt_name) as single_val from int_clnt_clients where clnt_code = '".$clnt."' limit 1");

$str_brok_months = trailing_12_months();

$arr_brok_months = explode(',',$str_brok_months);

//show_array($arr_brok_months);

foreach($arr_brok_months as $k=>$v) {
	$arr_mon_year = explode('^',$v);
	$arr_dates = get_commission_month_dates($arr_mon_year[0], $arr_mon_year[1]);

	$query_qc = "SELECT trad_advisor_code, sum(trad_quantity) as quant, sum(trad_commission) as comm 
								                FROM mry_comm_rr_trades 
																WHERE trad_advisor_code = '".$clnt."'
																AND trad_trade_date between '".$arr_dates[0]."' and '".$arr_dates[1]."' 
																GROUP BY trad_advisor_code";
	$result_qc = mysql_query($query_qc) or die(tdw_mysql_error($query_qc));
	while($row_qc = mysql_fetch_array($result_qc)) {
		$arr_data[$arr_mon_year[0].substr($arr_mon_year[1],2,2)] = $row_qc["quant"]."^".$row_qc["comm"];
		$arr_months[] = $arr_mon_year[0]."\n".substr($arr_mon_year[1],2,2);
		$arr_comm[] = round($row_qc["comm"],0);
	}
}

//fill array to 12 values
$arr_comm_new = array();
for ($i=0;$i<13;$i++) {
$arr_comm_new[$i] = ($arr_comm[$i] == '') ? 0 : $arr_comm[$i];
}

//create x-axis labels
foreach ($arr_brok_months as $k=>$v) {
$arr_temp = explode("^",$v);
$arr_months_new[] = $arr_temp[0]."\n".substr($arr_temp[1],2,2);
}

//show_array($arr_data);

# The data for the bar chart
$data = array_reverse($arr_comm_new); //array(85, 156, 179.5, 211, 123,12,12,12,12,12,12,12); //
//show_array($data);
# The labels for the bar chart
$labels = array_reverse($arr_months_new); //array("Mon", "Tue", "Wed", "Thu", "Fri");
//show_array($labels);

# Create a XYChart object of size 250 x 250 pixels
$c = new XYChart(600, 260, 0xEEEEEE, 1, 1);

# Add a title to the chart using 18pts Times Bold Italic font
$c->addTitle("Trailing 12 Months : ".$clnt_name." (".$clnt.")", "times.ttf", 14);

# Set the plotarea at (30, 20) and of size 200 x 200 pixels
$c->setPlotArea(50, 20, 500, 200, $c->linearGradientColor(60, 40, 60, 280, 0xeeeeff,
    0x000000), -1, 0x0000ff, 0x0000ff);

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


/*$

$query_dos = "SELECT * 
							FROM mry_dos_commission order by clnt_code";
$result_dos = mysql_query($query_dos) or die(tdw_mysql_error($query_dos));
while($row_dos = mysql_fetch_array($result_dos)) {
//$total_dos = $total_dos + $row_dos["clnt_commission"];
//$grand_total_dos = $grand_total_dos + $row_dos["clnt_commission"];
	if (in_array($row_dos["clnt_code"],$arr_clients)) {
	//do nothing
	} else {
	$total_dos = $total_dos + $row_dos["clnt_commission"];
	$grand_total_dos = $grand_total_dos + $row_dos["clnt_commission"];
?>
<tr>
	<td><?=$row_dos["clnt_code"]?></td>
	<td align="right"><?=number_format($row_dos["clnt_commission"],2)?></td>
</tr>
<?
	}

*/


















































exit;
# The value to display on the meter
$value = 6.5;

# Create an AugularMeter object of size 200 x 100 pixels with rounded corners
$m = new AngularMeter(200, 100);
$m->setRoundedFrame();

# Set meter background according to a parameter
if ($_REQUEST["img"] == "0") {
    # Use gold background color
    $m->setBackground(goldColor(), 0x000000, -2);
} else if ($_REQUEST["img"] == "1") {
    # Use silver background color
    $m->setBackground(silverColor(), 0x000000, -2);
} else if ($_REQUEST["img"] == "2") {
    # Use metallic blue (9898E0) background color
    $m->setBackground(metalColor(0x9898e0), 0x000000, -2);
} else if ($_REQUEST["img"] == "3") {
    # Use a wood pattern as background color
    $m->setBackground($m->patternColor2(dirname(__FILE__)."/wood.png"), 0x000000, -2)
        ;
} else if ($_REQUEST["img"] == "4") {
    # Use a marble pattern as background color
    $m->setBackground($m->patternColor2(dirname(__FILE__)."/marble.png"), 0x000000,
        -2);
} else {
    # Use a solid light purple (EEBBEE) background color
    $m->setBackground(0xeebbee, 0x000000, -2);
}

# Set the meter center at (100, 235), with radius 210 pixels, and span from -24 to
# +24 degress
$m->setMeter(100, 235, 210, -24, 24);

# Meter scale is 0 - 100, with a tick every 1 unit
$m->setScale(0, 10, 1);

# Set 0 - 6 as green (99ff99) zone, 6 - 8 as yellow (ffff00) zone, and 8 - 10 as red
# (ff3333) zone
$m->addZone(0, 6, 0x99ff99, 0x808080);
$m->addZone(6, 8, 0xffff00, 0x808080);
$m->addZone(8, 10, 0xff3333, 0x808080);

# Add a title at the bottom of the meter using 10 pts Arial Bold font
$m->addTitle2(Bottom, "OUTPUT POWER LEVEL\n", "arialbd.ttf", 10);

# Add a semi-transparent black (80000000) pointer at the specified value
$m->addPointer($value, 0x80000000);

# output the chart
header("Content-type: image/png");
print($m->makeChart2(PNG));
?>