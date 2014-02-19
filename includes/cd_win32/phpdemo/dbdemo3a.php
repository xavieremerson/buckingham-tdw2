<?php
require_once("../lib/phpchartdir.php");

#
# Displays the monthly revenue for the selected year. The selected year should be
# passed in as a query parameter called "xLabel"
#
if (isset($_GET["xLabel"]))
    $selectedYear = $_GET["xLabel"];
else
    $selectedYear = 2001;

# SQL statement to get the monthly revenues for the selected year.
$SQL =
    "Select Software, Hardware, Services From revenue Where Year(TimeStamp) = ".
    "$selectedYear Order By TimeStamp";

#
# Connect to database and read the query result into arrays
#

mysql_connect("localhost", "test", "test");
mysql_select_db("sample");
$result = mysql_query($SQL);

while ($row = mysql_fetch_row($result)) {
    $software[] = $row[0];
    $hardware[] = $row[1];
    $services[] = $row[2];
}

#
# Now we have read data into arrays, we can draw the chart using ChartDirector
#

# Create a XYChart object of size 600 x 360 pixels
$c = new XYChart(600, 360);

# Set the plotarea at (60, 50) and of size 480 x 270 pixels. Use a vertical gradient
# color from light blue (eeeeff) to deep blue (0000cc) as background. Set border and
# grid lines to white (ffffff).
$c->setPlotArea(60, 50, 480, 270, $c->linearGradientColor(60, 50, 60, 270, 0xeeeeff,
    0x0000cc), -1, 0xffffff, 0xffffff);

# Add a title to the chart using 15pts Times Bold Italic font
$c->addTitle("Global Revenue for Year $selectedYear", "timesbi.ttf", 18);

# Add a legend box at (60, 25) (top of the plotarea) with 9pts Arial Bold font
$legendObj = $c->addLegend(60, 25, false, "arialbd.ttf", 9);
$legendObj->setBackground(Transparent);

# Add a line chart layer using the supplied data
$layer = $c->addLineLayer2();
$dataSetObj = $layer->addDataSet($software, 0xffaa00, "Software");
$dataSetObj->setDataSymbol(CircleShape, 9);
$dataSetObj = $layer->addDataSet($hardware, 0x00ff00, "Hardware");
$dataSetObj->setDataSymbol(DiamondShape, 11);
$dataSetObj = $layer->addDataSet($services, 0xff0000, "Services");
$dataSetObj->setDataSymbol(Cross2Shape(), 11);

# Set the line width to 3 pixels
$layer->setLineWidth(3);

# Set the x axis labels. In this example, the labels must be Jan - Dec.
$labels = array("Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sept",
    "Oct", "Nov", "Dec");
$c->xAxis->setLabels($labels);

# Set y-axis tick density to 30 pixels. ChartDirector auto-scaling will use this as
# the guideline when putting ticks on the y-axis.
$c->yAxis->setTickDensity(30);

# Synchronize the left and right y-axes
$c->syncYAxis();

# Set the y axes titles with 10pts Arial Bold font
$c->yAxis->setTitle("USD (Millions)", "arialbd.ttf", 10);
$c->yAxis2->setTitle("USD (Millions)", "arialbd.ttf", 10);

# Set all axes to transparent
$c->xAxis->setColors(Transparent);
$c->yAxis->setColors(Transparent);
$c->yAxis2->setColors(Transparent);

# Set the label styles of all axes to 8pt Arial Bold font
$c->xAxis->setLabelStyle("arialbd.ttf", 8);
$c->yAxis->setLabelStyle("arialbd.ttf", 8);
$c->yAxis2->setLabelStyle("arialbd.ttf", 8);

# Create the image and save it in a temporary location
$chart1URL = $c->makeSession("chart1");

# Create an image map for the chart
$imageMap = $c->getHTMLImageMap("xystub.php", "",
    "title='{dataSetName} @ {xLabel} = USD {value|0}M'");
?>
<html>
<body topmargin="5" leftmargin="5" rightmargin="0" marginwidth="5" marginheight="5">
<div style="font-size:18pt; font-family:verdana; font-weight:bold">
    Database Clickable Chart
</div>
<hr color="#000080">
<div style="font-size:10pt; font-family:verdana; width:600px; margin-bottom:20px">
You have click the bar of the year <?php echo $selectedYear?>. Below is the "drill-down" chart
showing the monthly details.
<br><br>
<a href="viewsource.php?file=<?php echo $_SERVER["SCRIPT_NAME"]?>">
    View source code
</a>
</div>

<img src="getchart.php?<?php echo $chart1URL?>" border="0" usemap="#map1">
<map name="map1">
<?php echo $imageMap?>
</map>

</body>
</html>
