<?php
include("phpchartdir.php");

#
#Get the revenue for the last 10 years
#
$SQLstatement = 
	"Select Sum(Software + Hardware + Services), Year(TimeStamp) " .
	"From revenue Where Year(TimeStamp) >= 1991 And Year(TimeStamp) <= 2001 " .
	"Group By Year(TimeStamp) Order By Year(TimeStamp)";

#
#Read in the revenue data into arrays
#
mysql_connect("localhost", "test", "test");
$result = mysql_db_query("sample", $SQLstatement);
while ($row = mysql_fetch_row($result)) {
	$revenue[] = $row[0];
	$timestamp[] = $row[1];
}

#
#Now we obtain the data into arrays, we can start to draw the chart 
#using ChartDirector
#	
	
#Create a XYChart of size 420 pixels x 240 pixels
$c = new XYChart(420, 240);

#Set the chart background to pale yellow (0xffffc0) with a 2 pixel 3D border
$c->setBackground(0xffffc0, 0xffffc0, 2);
	
#Set the plotarea at (70, 50) and of size 320 x 150 pixels. Set background
#color to white (0xffffff). Enable both horizontal and vertical grids by
#setting their colors to light grey (0xc0c0c0)
$c->setPlotArea(70, 50, 320, 150, 0xffffff, 0xffffff, 0xc0c0c0, 0xc0c0c0);

#Add a title to the chart
$title = $c->addTitle("Revenue for Last 10 Years", "timesbi.ttf");
$title->setBackground(0xffff00);

#Add a legend box at the top of the plotarea
$legend = $c->addLegend(70, 30, 0, "", 8);
$legend->setBackground(Transparent);

#Add a multi-color bar chart layer using the supplied data
$layer = $c->addBarLayer3($revenue);
$layer->setBorderColor(Transparent, 1);
	
#Set the x-axis labels using the supplied labels
$c->xAxis->setLabels($timestamp);

#Set the x-axis width to 2 pixels
$c->xAxis->setWidth(2);

#Set the y axis title
$c->yAxis->setTitle("USD (K)");

#Set the y-axis width to 2 pixels
$c->yAxis->setWidth(2);

#Create the image and save it in a session variable
session_register("chart");
$HTTP_SESSION_VARS["chart"] = $chart = $c->makeChart2(PNG);
$chartURL = "myimage.php?img=chart&id=".uniqid(session_id())."&".SID;

#Create an image map for the chart
$imageMap = $c->getHTMLImageMap("dbdemo3a.php", "",
    "title='{xLabel}: USD {value|0}K'");
?>
<html>
<body>
<h1>Database Clickable Charts</h1>
<p style="width:500px;">The example demonstrates creating a clickable chart
using data from a database. Click on a bar below to "drill down" onto a 
particular year.</p>

<p><a href="viewsource.php?file=<?php echo $HTTP_SERVER_VARS["SCRIPT_NAME"] ?>">
	View source code
</a></p>

<img src="<?php echo $chartURL ?>" border="0" usemap="#map1">
<map name="map1">
<?php echo $imageMap ?>
</map>

</body>
</html>
