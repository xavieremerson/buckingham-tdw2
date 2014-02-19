<?  
//+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
// outputs chart for trailing 12 months with argument $clnt
//+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++ 

include('../includes/dbconnect.php');
include('../includes/functions.php');
include('../includes/global.php');
require_once("./cd_win32/lib/phpchartdir.php");
include('chart_data_functions.php');

if (!$date_start) {
$date_start = "2009-03-14";
$date_end = "2009-05-12";
$symbol = "AEO";
}



//**********************************************************************************
//**********************************************************************************
# SQL Server Connection Information

						/*$msconnect=mssql_connect("1Z92.168.20.48","BUCKINGHAM_login","BUCKINGHAM_pw");
						$msdb=mssql_select_db("BUCKINGHAM",$msconnect);
						*/
						$msconnect=mssql_connect("192.168.1.78","buckinghamtwo_db","9eFah9fe");
						$msdb=mssql_select_db("BuckinghamTwo",$msconnect);

$arr_mri = array();
$arr_mri_symbols = array();


//Robert Daniel pointed out Missed MRI's, reason was the start date being passed to this program.
//start date needs to be set back by a few months. 3 in this case.

//echo date('m/d/Y',strtotime($date_start) - 7776000);

$new_date_start = date('Y-m-d',strtotime($date_start) - 7776000);
//echo $new_date_start;
//exit;

$ms_qry_mri = "SELECT 
									dbo.ExchangeSecurities.Ticker as CUSIP,
									dbo.Prod_Statuses.DateTime, 
									dbo.Prod_Issuers.IssuerID, 
									dbo.Prod_Issuers.Recommendation, 
									dbo.Prod_Issuers.PreviousRecommendation, 
									dbo.Prod_Issuers.RecommendationAction, 
									dbo.Prod_Issuers.TargetPrice, 
									dbo.Prod_Statuses.StatusTypeID
								FROM ((dbo.Prod_Issuers INNER JOIN dbo.Products ON dbo.Prod_Issuers.ProductID = dbo.Products.ProductID) 
								INNER JOIN dbo.Issuers ON dbo.Prod_Issuers.IssuerID = dbo.Issuers.IssuerID) 
								INNER JOIN dbo.Prod_Statuses ON dbo.Products.ProductID = dbo.Prod_Statuses.ProductID
								INNER JOIN dbo.ExchangeSecurities ON dbo.Issuers.IssuerID = dbo.ExchangeSecurities.SecurityID
								WHERE (((dbo.Issuers.CUSIP)<>'') AND (dbo.Products.CreationDateTime BETWEEN 
									(
												 CAST(FLOOR(CAST(convert(datetime,'".format_date_ymd_to_mdy($new_date_start)."',120) AS float)) as datetime)-1) 
										 AND CAST(FLOOR(CAST(convert(datetime,'".format_date_ymd_to_mdy($date_end)."',120) AS float)) as datetime)
									) 
									AND ((dbo.Prod_Statuses.StatusTypeID)=3))
									AND dbo.ExchangeSecurities.Ticker = '".$symbol."'
								ORDER BY dbo.ExchangeSecurities.Ticker, dbo.Prod_Statuses.DateTime DESC;";	


//xdebug("ms_qry_mri",$ms_qry_mri);
//exit; 
$ms_results_mri = mssql_query($ms_qry_mri);

$v_count_mri = 0;
while ($row_mri = mssql_fetch_array($ms_results_mri)) {
			
			//show_array($row_mri);
			$symbol_mri = $row_mri[0];
			$mri_date = $row_mri[1];
			$rating = $row_mri[3];
			$rating_change = $row_mri[5]; 
			$target = $row_mri[6];
			$rating_previous = $row_mri[4];

			$img_show = '';
			$arr_mri[$v_count_mri] = $symbol_mri."<###>".$mri_date."<###>".$rating."<###>".$rating_change."<###>".$img_show."<###>".$target."<###>".$rating_previous;
			$arr_mri_symbols[$v_count_mri] = $symbol_mri;
			$v_count_mri = $v_count_mri + 1;

}
   
	 
	 $arr_mri = array_reverse($arr_mri);
	 //show_array($arr_mri_symbols);
	 //show_array($arr_mri);

	 //Array of relevant MRI data
	 $arr_recent_mri = array();
	 
   //show_array($arr_mri_symbols);
	 foreach($arr_mri as $key=>$value) {
           if ($key == 0) {
						 $arr_data = explode("<###>",$value);
						 $str_symbol_old = $arr_data[0];
						 $str_date_old = $arr_data[1];
						 $str_rating_old = $arr_data[2];
						 $str_rating_prev_old = $arr_data[6];
						 $str_compare_old = $arr_data[2].$arr_data[5];
						 $str_target_old = $arr_data[5];
						 //xdebug("First Row",'');
					 } else {
							//xdebug("Row Number",$key);	
							$arr_data = explode("<###>",$value);
							$str_symbol_new = $arr_data[0];
							$str_date_new = $arr_data[1];
							$str_rating_new = $arr_data[2];
							$str_rating_prev_new = $arr_data[6];
							$str_compare_new = $arr_data[2].$arr_data[5];
						  $str_target_new = $arr_data[5];
							//show_array($arr_data);
							//Compare with old and then proceed
							
							if ($str_rating_new != '')	{
								if ($str_rating_new != $str_rating_prev_new) {
									$arr_recent_mri[$str_date_new] = date('Y-m-d',strtotime($str_date_new));
								} elseif ($str_target_new != $str_target_old AND $str_target_old != "") {
									$arr_recent_mri[$str_date_new] = date('Y-m-d',strtotime($str_date_new));
								} else {
								  $dummy = "xyz";
								}
								//set old values
									$str_symbol_old = $str_symbol_new;
									$str_date_old = $str_date_new;
									$str_rating_old = $str_rating_new;
									$str_rating_prev_old = $str_rating_prev_new;
									$str_compare_old = $str_compare_new;
						      $str_target_old = $str_target_new;
							} else {
								//don't set old values
								$str_symbol_old = $str_symbol_new;
							}
						}
       }

$arr_mri_raw = array();
foreach($arr_recent_mri as $k=>$v) {
$arr_mri_raw[] = $v;
}

//show_array($arr_mri_raw);
//exit;	
//*******************************************************************************************
//*******************************************************************************************

$dataY1 = array();
$dataX1 = array();

$arr_price = hist_prices($symbol, $date_start, $date_end);
foreach($arr_price as $d=>$p) {
	$dataX1[] = format_date_ymd_to_mdy($d);
	$dataY1[] = $p;	
}

$dataX1 = array_reverse($dataX1);
$dataY1 = array_reverse($dataY1);

//print_r($dataX1);

//get min and max value of the Y1 value.
$min_val = min($dataY1);
$max_val = max($dataY1);
$mri_yval = (1.1*$max_val); //$min_val + 

//xdebug("mri_y_val",$min_val ." >> ".$max_val ." >> ".$mri_yval);

//MRI Here
//Something like this should come back from Jovus 

//$arr_mri_raw = array("2009-04-14","2009-04-24", "2009-05-07");  THIS HAS BEEN COMPUTED ABOVE

$dataY3 = array();
$dataX3 = array();
foreach ($dataX1 as $k=>$v) {
	if (in_array(format_date_mdy_to_ymd($v), $arr_mri_raw)) {
		$dataX3[] = $v; //"<*block,angle=45,halign=right*>".
		$dataY3[] = $mri_yval;	
	} else {
		$dataX3[] = $v; //"<*block,angle=45,halign=right*>".
		$dataY3[] = 1.7E+308;	
	}
}


//&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&
//Buy Cover here
$dataY4 = array();
$dataX4 = array();
$dataZ4 = array();

$query_buy = "SELECT oth_trade_date, sum(`oth_quantity`) as qty
								 FROM `oth_other_trades` 
								 where oth_symbol = '".$symbol."' 
									 and (oth_buysell = 'Buy' or oth_buysell = 'Cover') 
									 and oth_trade_date between '".$date_start."' and '".$date_end."'
								 group by `oth_trade_date`";
//xdebug("query_trades",$query_trades);
$result_buy = mysql_query($query_buy) or die(tdw_mysql_error($query_buy));
while($row = mysql_fetch_array($result_buy)) {
		$dataX4[] = $row["oth_trade_date"];
		$dataY4[] = $row["qty"];
		$dataZ4[$row["oth_trade_date"]] = $row["qty"];	
}

/*print_r($dataX4);
print_r($dataY4);
print_r($dataZ4);
*/

$dataY5 = array();
$dataX5 = array();

foreach ($dataX1 as $k=>$v) {
	if (in_array(format_date_mdy_to_ymd($v), $dataX4)) {
		$dataX5[] = $v; //"<*block,angle=45,halign=right*>".
		$dataY5[] = $dataZ4[format_date_mdy_to_ymd($v)];	
	} else {
		$dataX5[] = $v; //"<*block,angle=45,halign=right*>".
		$dataY5[] = 1.7E+308;	
	}
}

//&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&
//Buy Cover here
$dataY6 = array();
$dataX6 = array();
$dataZ6 = array();

$query_sell = "SELECT oth_trade_date, sum(`oth_quantity`) as qty
								 FROM `oth_other_trades` 
								 where oth_symbol = '".$symbol."' 
									 and (oth_buysell = 'Sell' or oth_buysell = 'Short') 
									 and oth_trade_date between '".$date_start."' and '".$date_end."'
								 group by `oth_trade_date`";
//xdebug("query_trades",$query_trades);
$result_sell = mysql_query($query_sell) or die(tdw_mysql_error($query_sell));
while($row = mysql_fetch_array($result_sell)) {
		$dataX6[] = $row["oth_trade_date"];
		$dataY6[] = $row["qty"];
		$dataZ6[$row["oth_trade_date"]] = $row["qty"];	
}

/*print_r($dataX4);
print_r($dataY4);
print_r($dataZ4);
*/

$dataY7 = array();
$dataX7 = array();

foreach ($dataX1 as $k=>$v) {
	if (in_array(format_date_mdy_to_ymd($v), $dataX6)) {
		$dataX7[] = $v; //"<*block,angle=45,halign=right*>".
		$dataY7[] = $dataZ6[format_date_mdy_to_ymd($v)];	
	} else {
		$dataX7[] = $v; //"<*block,angle=45,halign=right*>".
		$dataY7[] = 1.7E+308;	
	}
}
//&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&
/*print_r($dataX5);
print_r($dataY5);
exit;
*/
# The data for the line chart
//$data0 = array(42, 49, 33, 38, 51, 46, 29, 41, 44, 57, 59, 52, 37, 34, 51, 56, 56, 60, 70, 76, 63, 67, 75, 64, 51);
//$data1 = array(50, 55, 47, 34, 42, 49, 63, 62, 73, 59, 56, 50, 64, 60, 67, 67, 58, 59, 73, 77, 84, 82, 80, 84, 98);
//$data2 = array(36, 28, 25, 33, 38, 20, 22, 30, 25, 33, 30, 24, 28, 15, 21, 26, 46, 42, 48, 45, 43, 52, 64, 60, 70);

# The labels for the line chart
//$labels = array("0", "1", "2", "3", "4", "5", "6", "7", "8", "9", "10", "11", "12", "13", "14", "15", "16", "17", "18", "19", "20", "21", "22", "23", "24");

# Create an XYChart object of size 600 x 300 pixels, with a light blue (EEEEFF)
# background, black border, 1 pxiel 3D border effect and rounded corners
$c = new XYChart(800, 400, 0xeeeeff, 0x000000, 1);
$c->setRoundedFrame();

# Set the plotarea at (55, 58) and of size 520 x 195 pixels, with white background.
# Turn on both horizontal and vertical grid lines with light grey color (0xcccccc)
$c->setPlotArea(55, 58, 660, 260, 0xffffff, -1, -1, 0xcccccc, 0xcccccc);

# Add a legend box at (50, 30) (top of the chart) with horizontal layout. Use 9 pts
# Arial Bold font. Set the background and border color to Transparent.
$legendObj = $c->addLegend(50, 30, false, "arialbd.ttf", 9);
$legendObj->setBackground(Transparent);

# Add a title box to the chart using 15 pts Times Bold Italic font, on a light blue
# (CCCCFF) background with glass effect. white (0xffffff) on a dark red (0x800000)
# background, with a 1 pixel 3D border.
$textBoxObj = $c->addTitle($symbol." : From ". format_date_ymd_to_mdy($date_start)." To ".format_date_ymd_to_mdy($date_end), "timesb.ttf", 15); 
$textBoxObj->setBackground(0xdddddd, 0x000000, glassEffect());

# Add a title to the y axis
$c->yAxis->setTitle("Closing Price ($)");

# Set the labels on the x axis.
//$c->xAxis->setLabels($labels); //.setFontAngle(45);

$labelsObj = $c->xAxis->setLabels($dataX1);
$labelsObj->setFontAngle(45);


//$c->xAxis->setFontAngle(90); 

# Display 1 out of 3 labels on the x-axis.
//get the number of X data points and use that to determine the step
$countX = count($dataX1);
$c->xAxis->setLabelStep(round(($countX/10),0));

# Add a title to the x axis
$c->xAxis->setTitle("Trade Dates");

# Add a line layer to the chart
$layer = $c->addLineLayer2();

# Set the default line width to 2 pixels
$layer->setLineWidth(1);

# Add the three data sets to the line layer. For demo purpose, we use a dash line
# color for the last line

$layer->addDataSet($dataY1, 0xff0000, "Closing Price");

//$layer->addDataSet($data1, 0x008800, "Server #2");
//$layer->addDataSet($data2, $c->dashLineColor(0x3333ff, DashLine), "Server #3");


# Add a line layer to the chart
$layer2 = $c->addLineLayer2();
$layer2->setLineWidth(0);
$dataSetObj = $layer2->addDataSet($dataY3, 0x00ff00, "MRI");
$dataSetObj->setDataSymbol2(dirname(__FILE__)."/images/mri.png");

# Add a title to the y axis
$c->yAxis2->setTitle("Quantity");

$layer3 = $c->addLineLayer2();
$layer3->setUseYAxis2();
$layer3->setLineWidth(0);
$dataSetObj = $layer3->addDataSet($dataY5, 0x0000ff, "BCM Buys");
$dataSetObj->setDataSymbol2(dirname(__FILE__)."/images/buy.png");

$layer4 = $c->addLineLayer2();
$layer4->setUseYAxis2();
$layer4->setLineWidth(0);
$dataSetObj = $layer4->addDataSet($dataY7, 0xff0000, "BCM Sells");
$dataSetObj->setDataSymbol2(dirname(__FILE__)."/images/sell.png");

# output the chart
header("Content-type: image/png");
print($c->makeChart2(PNG));
?>