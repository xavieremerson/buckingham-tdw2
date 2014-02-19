<?php
include("phpchartdir.php");
include("includes/global.php");
include('includes/dbconnect.php');
include('includes/functions.php');

#The data for the line chart
//EMPLOYEE TRADES
//$data0 = array(42, 49, 33, 38, 51, 46, 29, 41, 44, 57, 59, 52, 37, 34, 51, 56,
 //   56, 60, 70, 76, 63, 67, 75, 64, 51);
$data_emp = array();	

//CUSTOMER TRADES
//$data1 = array(50, 55, 47, 34, 42, 49, 63, 62, 73, 59, 56, 50, 64, 60, 67, 67,
  //  58, 59, 73, 77, 84, 82, 80, 84, 98);
$data_cust = array();
	
//EXCEPTIONS
//$data2 = array(36, 28, 25, 33, 38, 20, 22, 30, 25, 33, 30, 24, 28, 15, 21, 26,
    //46, 42, 48, 45, 43, 52, 64, 60, 70);
$data_exceptions = array();
$data_dates = array();

$tdate = business_day_backward(strtotime("now()"), 66);
$query_data = "SELECT * FROM gdat_graph_data WHERE gdat_trade_date >= '".$tdate."' ORDER BY gdat_trade_date ASC";
$result_data = mysql_query($query_data) or die(mysql_error());

//echo "<BR>" . $query_data. "<BR>";
$i = 0;
while($row_data = mysql_fetch_array($result_data))
{
    
	$data_emp[] = $row_data["gdat_emp_trades"];
	$data_cust[] = $row_data["gdat_cust_trades"];
	$data_exceptions[] = $row_data["gdat_exceptions"];
	
	$data_dates[] = str_replace('-','/',substr($row_data["gdat_trade_date"],5,5));
}

/*
for($i=0; $i < 66 ; $i++)
{
	echo "data_emp ". $data_emp[$i] . "<BR>";
	echo "data_cust ". $data_cust[$i] . "<BR>";
	echo "data_exceptions ". $data_exceptions[$i] . "<BR>";
	
	echo "<BR><BR>";

}
*/

$labels = array();
#The labels for the line chart
for($j = 0; $j < count($data_dates); $j++)
{
	if($j%8 == 0)
	{
		$labels[] = $data_dates[$j];
	}
	else
	{
		$labels[] = "";
	}
}

//echo '<br>COUNT OF LABELS ' . count($labels);
//$labels = array(0, "", "", "", 6, "", "", "", 12, "", "", "", 18, "", "", "", 24, "", "", "",30, "", "", "", 36, "", "", "", 42, "", "", "", 48, "", "", "", 54, "", 
  //  "", "", 60, "", "", "", 66, "", "", "", 72, "", "", "", 78, "", "", "", 84, "", "", "", 90, "",  "", "", 96, "", "");

#Create a XYChart object of size 500 x 300 pixels, with a pale yellow (0xffff80)
#background, a black border, and 1 pixel 3D border effect
$c = new XYChart(490, 230, 0xeeeeee, 0xeeeeee, 0);

#Set the plotarea at (55, 45) and of size 420 x 210 pixels, with white
#background. Turn on both horizontal and vertical grid lines with light grey
#color (0xc0c0c0)
$c->setPlotArea(45, 20, 430, 160, 0xffffff, -1, -1, 0xc0c0c0, -1);

#Add a legend box at (55, 25) (top of the chart) with horizontal layout. Use 8
#pts Arial font. Set the background and border color to Transparent.
$legendObj = $c->addLegend(55, 25, false, "", 8);
$legendObj->setBackground(Transparent);

#Add a title box to the chart using 11 pts Arial Bold Italic font. The text is
#white (0xffffff) on a dark red (0x800000) background, with a 1 pixel 3D border.

$titleObj = $c->addTitle("", "arialb.ttf", 10, 0x404040);

$titleObj->setBackground(0x404040, -1, 1);

#Add a title to the y axis
$c->yAxis->setTitle("Number of Trades");

#Set the labels on the x axis
$c->xAxis->setLabels($labels);

#Add a title to the x axis
$c->xAxis->setTitle("Trades/day for the past 3 months");

#Add a line layer to the chart
$layer = $c->addLineLayer2();

#Set the default line width to 2 pixels
$layer->setLineWidth(2);

#Add the three data sets to the line layer. For demo purpose, we use a dash line
#color for the last line
$layer->addDataSet($data_emp, -1, "Employee Trades");
$layer->addDataSet($data_cust, -1, "Customer Trades");
$layer->addDataSet($data_exceptions, $c->dashLineColor(0x3333ff, DashLine), "Exceptions");

$fx = fopen($exportchartlocation."custvsemp.png", "w");
$imagedata = $c->makeChart2(PNG);
fputs($fx, $imagedata, 400000000); 
fclose($fx);

#output the chart
//header("Content-type: image/png");
//print($c->makeChart2(PNG));
?>