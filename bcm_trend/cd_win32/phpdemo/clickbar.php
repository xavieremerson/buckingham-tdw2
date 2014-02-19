<?php
require_once("../lib/phpchartdir.php");

# The data for the bar chart
$data = array(450, 560, 630, 800, 1100, 1350, 1600, 1950, 2300, 2700);

# The labels for the bar chart
$labels = array("1996", "1997", "1998", "1999", "2000", "2001", "2002", "2003",
    "2004", "2005");

# Create a XYChart object of size 600 x 360 pixels
$c = new XYChart(600, 360);

# Add a title to the chart using 18pts Times Bold Italic font
$c->addTitle("Annual Revenue for Star Tech", "timesbi.ttf", 18);

# Set the plotarea at (60, 40) and of size 500 x 280 pixels. Use a vertical gradient
# color from light blue (eeeeff) to deep blue (0000cc) as background. Set border and
# grid lines to white (ffffff).
$c->setPlotArea(60, 40, 500, 280, $c->linearGradientColor(60, 40, 60, 280, 0xeeeeff,
    0x0000cc), -1, 0xffffff, 0xffffff);

# Add a multi-color bar chart layer using the supplied data. Use soft lighting effect
# with light direction from top.
$barLayerObj = $c->addBarLayer3($data);
$barLayerObj->setBorderColor(Transparent, softLighting(Top));

# Set x axis labels using the given labels
$c->xAxis->setLabels($labels);

# Draw the ticks between label positions (instead of at label positions)
$c->xAxis->setTickOffset(0.5);

# When auto-scaling, use tick spacing of 40 pixels as a guideline
$c->yAxis->setTickDensity(40);

# Add a title to the y axis with 12pts Times Bold Italic font
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
$imageMap = $c->getHTMLImageMap("clickline.php", "",
    "title='{xLabel}: US\$ {value|0}M'");
?>
<html>
<body topmargin="5" leftmargin="5" rightmargin="0" marginwidth="5" marginheight="5">
<div style="font-size:18pt; font-family:verdana; font-weight:bold">
    Simple Clickable Bar Chart
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
