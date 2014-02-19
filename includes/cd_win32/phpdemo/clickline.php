<?php
require_once("../lib/phpchartdir.php");

# Get the selected year.
$selectedYear = $_REQUEST["xLabel"];

#
# In this demo, we just split the annual revenue into 12 months using random ratios.
# In real life, the data can come from a database based on selectedYear.
#

# Get the annual revenue
$annualRevenue = $_REQUEST["value"];

# Split into 12 months
srand((int)($selectedYear));
$data = array_pad(array(), 12, 0);
for($i = 0; $i < 11; ++$i) {
    $data[$i] = $annualRevenue * (rand() / getrandmax() * 0.6 + 0.6) / (12 - $i);
    $annualRevenue = $annualRevenue - $data[$i];
}
$data[11] = $annualRevenue;

#
# Now we obtain the data into arrays, we can start to draw the chart using
# ChartDirector
#

# Create a XYChart object of size 600 x 320 pixels
$c = new XYChart(600, 360);

# Add a title to the chart using 18pts Times Bold Italic font
$c->addTitle("Month Revenue for Star Tech for $selectedYear", "timesbi.ttf", 18);

# Set the plotarea at (60, 40) and of size 500 x 280 pixels. Use a vertical gradient
# color from light blue (eeeeff) to deep blue (0000cc) as background. Set border and
# grid lines to white (ffffff).
$c->setPlotArea(60, 40, 500, 280, $c->linearGradientColor(60, 40, 60, 280, 0xeeeeff,
    0x0000cc), -1, 0xffffff, 0xffffff);

# Add a red line (ff0000) chart layer using the data
$lineLayerObj = $c->addLineLayer();
$dataSet = $lineLayerObj->addDataSet($data, 0xff0000, "Revenue");

# Set the line width to 3 pixels
$dataSet->setLineWidth(3);

# Use a 13 point circle symbol to plot the data points
$dataSet->setDataSymbol(CircleSymbol, 13);

# Set the labels on the x axis. In this example, the labels must be Jan - Dec.
$labels = array("Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sept",
    "Oct", "Nov", "Dec");
$c->xAxis->setLabels($labels);

# When auto-scaling, use tick spacing of 40 pixels as a guideline
$c->yAxis->setTickDensity(40);

# Add a title to the x axis to reflect the selected year
$c->xAxis->setTitle("Year $selectedYear", "timesbi.ttf", 12);

# Add a title to the y axis
$c->yAxis->setTitle("USD (millions)", "timesbi.ttf", 12);

# Set axis label style to 8pts Arial Bold
$c->xAxis->setLabelStyle("arialbd.ttf", 8);
$c->yAxis->setLabelStyle("arialbd.ttf", 8);

# Set axis line width to 2 pixels
$c->xAxis->setWidth(2);
$c->yAxis->setWidth(2);

# Create the image and save it in a temporary location
$chart1URL = $c->makeSession("chart1");

# Create an image map for the chart
$imageMap = $c->getHTMLImageMap("clickpie.php?year=$selectedYear", "",
    "title='{xLabel}: US\$ {value|0}M'");
?>
<html>
<body topmargin="5" leftmargin="5" rightmargin="0" marginwidth="5" marginheight="5">
<div style="font-size:18pt; font-family:verdana; font-weight:bold">
    Simple Clickable Line Chart
</div>
<hr color="#000080">
<div style="font-size:10pt; font-family:verdana; margin-bottom:20">
    <a href="viewsource.php?file=<?php echo $_SERVER["SCRIPT_NAME"]?>">View Source Code</a>
</div>
<img src="getchart.php?<?php echo $chart1URL?>" border="0" usemap="#map1">
<map name="map1">
<?php echo $imageMap?>
</map>
</body>
</html>
