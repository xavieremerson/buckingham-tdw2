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
	 //Array of relevant MRI data
	 $arr_recent_mri = array();
	 $arr_mri_support = array();
	 foreach($arr_mri as $key=>$value) {
           if ($key == 0) {
						 $arr_data = explode("<###>",$value);
						 $str_symbol_old = $arr_data[0];
						 $str_date_old = $arr_data[1];
						 $str_rating_old = $arr_data[2];
						 $str_rating_prev_old = $arr_data[6];
						 $str_compare_old = $arr_data[2].$arr_data[5];
						 $str_target_old = $arr_data[5];
					 } else {
							$arr_data = explode("<###>",$value);
							$str_symbol_new = $arr_data[0];
							$str_date_new = $arr_data[1];
							$str_rating_new = $arr_data[2];
							$str_rating_prev_new = $arr_data[6];
							$str_compare_new = $arr_data[2].$arr_data[5];
						  $str_target_new = $arr_data[5];
							//Compare with old and then proceed
							if ($str_rating_new != '')	{
								if ($str_rating_new != $str_rating_prev_new) {
									$arr_recent_mri[$str_date_new] = date('Y-m-d',strtotime($str_date_new));
									$arr_mri_support[date('Y-m-d',strtotime($str_date_new))] = "Rating change from ".$str_rating_prev_new." to ".$str_rating_new."."; 
								} elseif ($str_target_new != $str_target_old AND $str_target_old != "") {
									$arr_recent_mri[$str_date_new] = date('Y-m-d',strtotime($str_date_new));
									$arr_mri_support[date('Y-m-d',strtotime($str_date_new))] = "Target change from ".$str_target_old." to ".$str_target_new."."; 
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
//*******************************************************************************************
//Closing Prices (from Yahoo Finance)
$dataY1 = array();
$dataX1 = array();
$xLabel = array();

$arr_price = hist_prices($symbol, $date_start, $date_end);
foreach($arr_price as $d=>$p) {
	$dataX1[] = format_date_ymd_to_mdy($d);
	$dataY1[] = $p;	
	$xLabel[] = substr(format_date_ymd_to_mdy($d),0,5);
}

$dataX1 = array_reverse($dataX1);
$dataY1 = array_reverse($dataY1);
$xLabel = array_reverse($xLabel);
//*******************************************************************************************
//MRI Here
//Something like this should come back from Jovus 
//$arr_mri_raw = array("2009-04-14","2009-04-24", "2009-05-07");  THIS HAS BEEN COMPUTED ABOVE

//get min and max value of the Y1 value.
$min_val = min($dataY1);
$max_val = max($dataY1);
$mri_yval = (1.1*$max_val); //$min_val + 

$dataY3 = array();
$dataX3 = array();
$data_mri_date = array();
foreach ($dataX1 as $k=>$v) {
	if (in_array(format_date_mdy_to_ymd($v), $arr_mri_raw)) {
		$dataX3[] = $v; //"<*block,angle=45,halign=right*>".
		$dataY3[] = $mri_yval;	
		$data_mri_date[] = str_replace("-","",format_date_mdy_to_ymd($v));
	} else {
		$dataX3[] = $v; //"<*block,angle=45,halign=right*>".
		$dataY3[] = 1.7E+308;	
		$data_mri_date[] = "";
	}
}

//show_array($data_mri_date);
//exit;

//&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&
//Buy here
$dataY4 = array();
$dataX4 = array();
$dataZ4 = array();

$query_buy = "SELECT oth_trade_date, sum(`oth_quantity`) as qty
								 FROM `oth_other_trades` 
								 where oth_symbol = '".$symbol."' 
									 and (oth_buysell = 'Buy') 
									 and oth_trade_date between '".$date_start."' and '".$date_end."'
								 group by `oth_trade_date`";
//xdebug("query_trades",$query_trades);
$result_buy = mysql_query($query_buy) or die(tdw_mysql_error($query_buy));
while($row = mysql_fetch_array($result_buy)) {
		$dataX4[] = $row["oth_trade_date"];
		$dataY4[] = $row["qty"];
		$dataZ4[$row["oth_trade_date"]] = $row["qty"];	
}

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
//Cover here
$dataY11 = array();
$dataX11 = array();
$dataZ11 = array();

$query_buy = "SELECT oth_trade_date, sum(`oth_quantity`) as qty
								 FROM `oth_other_trades` 
								 where oth_symbol = '".$symbol."' 
									 and (oth_buysell = 'Cover') 
									 and oth_trade_date between '".$date_start."' and '".$date_end."'
								 group by `oth_trade_date`";
//xdebug("query_trades",$query_trades);
$result_buy = mysql_query($query_buy) or die(tdw_mysql_error($query_buy));
while($row = mysql_fetch_array($result_buy)) {
		$dataX11[] = $row["oth_trade_date"];
		$dataY11[] = $row["qty"];
		$dataZ11[$row["oth_trade_date"]] = $row["qty"];	
}

$dataY12 = array();
$dataX12 = array();

foreach ($dataX1 as $k=>$v) {
	if (in_array(format_date_mdy_to_ymd($v), $dataX11)) {
		$dataX12[] = $v; //"<*block,angle=45,halign=right*>".
		$dataY12[] = $dataZ11[format_date_mdy_to_ymd($v)];	
	} else {
		$dataX12[] = $v; //"<*block,angle=45,halign=right*>".
		$dataY12[] = 1.7E+308;	
	}
}


//*******************************************************************
//create dummy data
$arr_val_1 = array();
$arr_val_2 = array();
foreach ($dataY5 as $k=>$v) {
	$arr_val_1[] = rand(11111111,99999999);
	$arr_val_2[] = rand(11111111,99999999);
}
//*******************************************************************


//&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&
//Sell here
$dataY6 = array();
$dataX6 = array();
$dataZ6 = array();

$query_sell = "SELECT oth_trade_date, sum(`oth_quantity`) as qty
								 FROM `oth_other_trades` 
								 where oth_symbol = '".$symbol."' 
									 and oth_buysell = 'Sell' 
									 and oth_trade_date between '".$date_start."' and '".$date_end."'
								 group by `oth_trade_date`";
//xdebug("query_trades",$query_trades);
$result_sell = mysql_query($query_sell) or die(tdw_mysql_error($query_sell));
while($row = mysql_fetch_array($result_sell)) {
		$dataX6[] = $row["oth_trade_date"];
		$dataY6[] = $row["qty"];
		$dataZ6[$row["oth_trade_date"]] = $row["qty"];	
}

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
//Short Sell here
$dataY8 = array();
$dataX8 = array();
$dataZ8 = array();

$query_sell = "SELECT oth_trade_date, sum(`oth_quantity`) as qty
								 FROM `oth_other_trades` 
								 where oth_symbol = '".$symbol."' 
									 and oth_buysell = 'Short'
									 and oth_trade_date between '".$date_start."' and '".$date_end."'
								 group by `oth_trade_date`";
//xdebug("query_trades",$query_trades);
$result_sell = mysql_query($query_sell) or die(tdw_mysql_error($query_sell)); 
while($row = mysql_fetch_array($result_sell)) {
		$dataX8[] = $row["oth_trade_date"];
		$dataY8[] = $row["qty"];
		$dataZ8[$row["oth_trade_date"]] = $row["qty"];	
}

/*print_r($dataX4);
print_r($dataY4);
print_r($dataZ4);
*/

$dataY9 = array();
$dataX9 = array();

foreach ($dataX1 as $k=>$v) {
	if (in_array(format_date_mdy_to_ymd($v), $dataX8)) {
		$dataX9[] = $v; //"<*block,angle=45,halign=right*>".
		$dataY9[] = $dataZ8[format_date_mdy_to_ymd($v)];	
	} else {
		$dataX9[] = $v; //"<*block,angle=45,halign=right*>".
		$dataY9[] = 1.7E+308;	
	}
}

//&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&
//New/Events here
$arr_news_events = array();
$arr_news_id = array();
$qry_news = "select auto_id, news_date, news_event from news_events where news_date between '".$date_start."' and '".$date_end."'
             and news_symbol = '".$symbol."' and news_isactive = 1"; 
$result_news = mysql_query($qry_news) or die(tdw_mysql_error($qry_news));
while($row = mysql_fetch_array($result_news)) {
	$arr_news_events[] = $row["news_date"];
	$arr_news_id[$row["news_date"]] = $row["auto_id"];
}

$news_yval = (1.3*$max_val);
$dataY10 = array();
$dataX10 = array();
$data_news_id = array();
foreach ($dataX1 as $k=>$v) {
	if (in_array(format_date_mdy_to_ymd($v), $arr_news_events)) {
		$dataX10[] = $v; //"<*block,angle=45,halign=right*>".
		$dataY10[] = $news_yval;
		$data_news_id[] = $arr_news_id[format_date_mdy_to_ymd($v)];     
	} else {
		$dataX10[] = $v; //"<*block,angle=45,halign=right*>".
		$dataY10[] = 1.7E+308;
		$data_news_id[] = "0";	
	}
}

//show_array($data_news_id);
//&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&
# Create an XYChart object of size 600 x 300 pixels, with a light blue (EEEEFF)
# background, black border, 1 pxiel 3D border effect and rounded corners
$c = new XYChart(800, 450, 0xeeeeff, 0x000000, 1);
$c->setRoundedFrame();

# Set the plotarea at (55, 58) and of size 520 x 195 pixels, with white background.
# Turn on both horizontal and vertical grid lines with light grey color (0xcccccc)
$c->setPlotArea(55, 58, 660, 330, 0xffffff, -1, -1, 0xcccccc, 0xcccccc);

# Add a legend box at (50, 30) (top of the chart) with horizontal layout. Use 9 pts
# Arial Bold font. Set the background and border color to Transparent.
$legendObj = $c->addLegend(50, 30, false, "arialbd.ttf", 9);
$legendObj->setBackground(Transparent);

# Add a title box to the chart using 15 pts Times Bold Italic font, on a light blue
# (CCCCFF) background with glass effect. white (0xffffff) on a dark red (0x800000)
# background, with a 1 pixel 3D border.
$val_company_string = trim(get_company_name($symbol));
if ($val_company_string == $symbol) {
	$use_company_string = $symbol;
} else {
	$use_company_string = $symbol . "  [". $val_company_string . "]";
}
$textBoxObj = $c->addTitle($use_company_string." : From ". format_date_ymd_to_mdy($date_start)." To ".format_date_ymd_to_mdy($date_end), "timesb.ttf", 14); 
$textBoxObj->setBackground(0xfccda0, 0x000000, glassEffect());

# Add a title to the y axis
$c->yAxis->setTitle("Closing Price ($)");

$c->yAxis->setLinearScale((0.7*min($dataY1)), (1.5*max($dataY1)), 0);


# Set the labels on the x axis.
//$c->xAxis->setLabels($labels); //.setFontAngle(45);

$labelsObj = $c->xAxis->setLabels($xLabel);
$labelsObj->setFontAngle(45);

# Display 1 out of 3 labels on the x-axis.
//get the number of X data points and use that to determine the step
$countX = count($dataX1);
$c->xAxis->setLabelStep(round(($countX/10),0));

# Add a title to the x axis
$c->xAxis->setTitle("Trade Dates"); //date('m/d/Y h:ia')

# Add a line layer to the chart
$layer = $c->addLineLayer2();
# Set the default line width to 2 pixels
$layer->setLineWidth(1);

# Add the three data sets to the line layer. For demo purpose, we use a dash line
# color for the last line
$layer->addDataSet($dataY1, 0xff0000, "Closing Price");

# Add a line layer to the chart
$layer2 = $c->addLineLayer2();
$layer2->setLineWidth(0);
$dataSetObj = $layer2->addDataSet($dataY3, 0x00ff00, "MRI");
$dataSetObj->setDataSymbol2(dirname(__FILE__)."/images/mri.png");

$layer2->addExtraField2($data_mri_date); //data_mri_date

# Add a line layer to the chart for News
$layer2a = $c->addLineLayer2();
$layer2a->setLineWidth(0);
$dataSetObj = $layer2a->addDataSet($dataY10, 0x00ff00, "Events");
$dataSetObj->setDataSymbol2(dirname(__FILE__)."/images/event.png");

$layer2a->addExtraField2($data_news_id);

# Add a title to the y axis
$c->yAxis2->setTitle("Quantity");

$layer3 = $c->addLineLayer2();
$layer3->setUseYAxis2();
$layer3->setLineWidth(0);
$dataSetObj = $layer3->addDataSet($dataY5, 0x0000ff, "BCM Buys");
$dataSetObj->setDataSymbol2(dirname(__FILE__)."/images/buy.png");

$layer6 = $c->addLineLayer2();
$layer6->setUseYAxis2();
$layer6->setLineWidth(0);
$dataSetObj = $layer6->addDataSet($dataY12, 0xff0000, "BCM Covers");
$dataSetObj->setDataSymbol2(dirname(__FILE__)."/images/cover.png");
//$layer3->addExtraField2($arr_val_1);
//$layer3->addExtraField2($arr_val_2);

$layer4 = $c->addLineLayer2();
$layer4->setUseYAxis2();
$layer4->setLineWidth(0);
$dataSetObj = $layer4->addDataSet($dataY7, 0xff0000, "BCM Sells");
$dataSetObj->setDataSymbol2(dirname(__FILE__)."/images/sell.png");

$layer5 = $c->addLineLayer2();
$layer5->setUseYAxis2();
$layer5->setLineWidth(0);
$dataSetObj = $layer5->addDataSet($dataY9, 0xff0000, "BCM Shorts");
$dataSetObj->setDataSymbol2(dirname(__FILE__)."/images/short.png");

# Add a custom CDML text at the bottom right of the plot area as the logo
$c->addTitle2(BottomRight, "<*block,valign=absmiddle*><*img=".dirname(__FILE__)."/images/chart_logo.png*> <*block*>");

# Create the image and save it in a temporary location
$chart1URL = $c->makeSession("chart1");

# Client side Javascript to show detail information "onmouseover"
//$showText = "onmouseover='showInfo(\"{xLabel}\", {value}, {field0}, {field1});' ";
$showText = "onmouseover='showInfo(\"{xLabel}\", {field0});' ";

# Client side Javascript to hide detail information "onmouseout"
$hideText = "onmouseout='showInfo(null);' ";

# "title" attribute to show tool tip
//$toolTip = "title='{xLabel}: Buy {value|0}'";

# Create an image map for the chart
//$imageMap = $layer3->getHTMLImageMap('javascript:;', "", "$showText$hideText");
$imageMap = $layer2a->getHTMLImageMap("", "", "$showText$hideText");


$showMRI = "onmouseover='showMRI(\"{field0}\");' ";
$hideMRI = "onmouseout='showMRI(null);' ";
$imageMap .= $layer2->getHTMLImageMap("", "", "$showMRI$hideMRI");

?>
<html>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<style type="text/css">
<!--
.show_news {
	font-family: Verdana, Arial, Helvetica, sans-serif;
	font-size: 11px;
	color: #000099;
}
.show_mri {
	font-family: Verdana, Arial, Helvetica, sans-serif;
	font-size: 14px;
	color: #0000ff;
	font-weight: bold;
	background-color: #FFFF99;
}
-->
</style>
<body topmargin="5" leftmargin="5" rightmargin="0" marginwidth="5" marginheight="5"> 
<img src="getchart.php?<?php echo $chart1URL?>" border="0" usemap="#map1">
<map name="map1">
<?php echo $imageMap?>
</map>
<table width="800" border="0"><tr><td>
<p id="detailInfo" class="show_news" style="margin-left:5" align="justify"></p>
<p id="mriInfo" class="show_mri" style="margin-left:5" align="justify"></p>
</td></tr></table>
<script language="javascript" src="../includes/prototype/prototype.js"></script>
<script>
//
//Client side script function to show and hide detail information. 
//
function showInfo(val_a, val_b) { //a is date, b is auto_id 

	if (val_a == null) {
		$("detailInfo").innerHTML = "";
		$("detailInfo").style.visibility = "hidden";
 	  $("detailInfo").style.display = "none";
		return false;
	}
	var url = 'http://192.168.20.63/tdw/bcm_trend/fetch_events_ajax.php';
	var pars = 'mod_request=news';
  pars = pars + '&auto_id='+ val_b;
  pars = pars + '&rand='+ Math.random();
	
	//alert(pars);

  new Ajax.Request
	(
		url,   
		{     
			method:'get', 
			parameters:pars,    
			onSuccess: 
				function(transport){       
					var response = transport.responseText; 
					//alert(response);      
          $("detailInfo").innerHTML = response;
				},     
			onFailure: 
			function(){ $("detailInfo").innerHTML = "Error accessing News/Events Data."; }
		}
	);

 $("detailInfo").style.visibility = "visible";
 $("detailInfo").style.display = "block";
}

function showMRI(val_a) { //a is date, b is auto_id 
	
	if (val_a == null) {
		$("mriInfo").innerHTML = "";
		$("mriInfo").style.visibility = "hidden";
 	  $("mriInfo").style.display = "none";
		return false;
	}
	
	var strDate = val_a.substring(0,4)+"-"+val_a.substring(4,6)+"-"+val_a.substring(6,8);
	
	var mri_support =new Array(); 
	<?
	foreach($arr_mri_raw as $k=>$v) {
		echo 'mri_support["'.$v.'"]="'.$arr_mri_support[$v].'";'."\n";
	}
	?>  
	$("mriInfo").innerHTML = mri_support[strDate] + " on " + val_a.substring(4,6)+"/"+val_a.substring(6,8)+"/"+val_a.substring(0,4);
  $("mriInfo").style.visibility = "visible";
  $("mriInfo").style.display = "block";
}
</script>