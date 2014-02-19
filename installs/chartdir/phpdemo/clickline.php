<?php
include("phpchartdir.php");

#Get the selected year.
$selectedYear = $HTTP_GET_VARS["xLabel"];

#Get the total revenue
$totalRevenue = $HTTP_GET_VARS["value"];

#
#  In this demo, we just split the total revenue into 12 months using random
#  numbers. In real life, the data can come from a database.
#
srand($selectedYear);
$data = array_pad(array(), 12, 0);
for($i = 0; $i < 11; ++$i) {
    $data[$i] = $totalRevenue * (rand() / getrandmax() * 0.6 + 0.6) / (12 - $i);
    $totalRevenue = $totalRevenue - $data[$i];
}
$data[11] = $totalRevenue;

#
#  Now we obtain the data into arrays, we can start to draw the chart using
#  ChartDirector
#

#Create a XYChart object of size 450 x 200 pixels
$c = new XYChart(450, 200);

#Add a title to the chart
$c->addTitle("Month Revenue for Star Tech for $selectedYear", "timesbi.ttf");

#Set the plotarea at (60, 5) and of size 350 x 150 pixels. Enable both
#horizontal and vertical grids by setting their colors to grey (0xc0c0c0)
$plotAreaObj = $c->setPlotArea(60, 25, 350, 150);
$plotAreaObj->setGridColor(0xc0c0c0, 0xc0c0c0);

#Add a line chart layer using the data
$lineLayerObj = $c->addLineLayer();
$dataSet = $lineLayerObj->addDataSet($data, 0x993399);

#Set the line width to 3 pixels
$dataSet->setLineWidth(3);

#Use a 11 point triangle symbol to plot the data points
$dataSet->setDataSymbol(TriangleSymbol, 11);

#Set the x axis labels. In this example, the labels must be Jan - Dec.
$labels = array("Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sept",
    "Oct", "Nov", "Dec");
$c->xAxis->setLabels($labels);

#Add a title to the x axis to reflect the selected year
$c->xAxis->setTitle("Year $selectedYear");

#Add a title to the y axis
$c->yAxis->setTitle("USD (K)");

#Reserve 10% margin at the top of the plot area just to make sure the line does
#not go too near the top of the plot area
$c->yAxis->setAutoScale(0.1);

#Create the image and save it in a temporary location
$chart1URL = $c->makeSession("chart1");

#Create an image map for the chart
$imageMap = $c->getHTMLImageMap("clickpie.php?year=$selectedYear", "",
    "title='{xLabel}: USD {value|0}K'");
?>
<html>
<body>
<h1>Simple Clickable Line Chart</h1>
<p><a href="viewsource.php?file=<?php echo $HTTP_SERVER_VARS["SCRIPT_NAME"]?>">
View Source Code
</a></p>

<img src="myimage.php?<?php echo $chart1URL?>" border="0" usemap="#map1">
<map name="map1">
<?php echo $imageMap?>
</map>
</body>
</html>
