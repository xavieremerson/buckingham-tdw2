<?php
require_once("../lib/phpchartdir.php");

# The data for the bar chart
$data0 = array(100, 125, 245, 147, 67);
$data1 = array(85, 156, 179, 211, 123);
$data2 = array(97, 87, 56, 267, 157);
$labels = array("Mon", "Tue", "Wed", "Thur", "Fri");

# Create a XYChart object of size 540 x 375 pixels
$c = new XYChart(540, 375);

# Add a title to the chart using 18 pts Times Bold Italic font
$c->addTitle("Average Weekly Network Load", "timesbi.ttf", 18);

# Set the plotarea at (50, 55) and of 440 x 280 pixels in size. Use a vertical
# gradient color from light red (ffdddd) to dark red (880000) as background. Set
# border and grid lines to white (ffffff).
$c->setPlotArea(50, 55, 440, 280, $c->linearGradientColor(0, 55, 0, 335, 0xffdddd,
    0x880000), -1, 0xffffff, 0xffffff);

# Add a legend box at (50, 25) using horizontal layout. Use 10pts Arial Bold as font,
# with transparent background.
$legendObj = $c->addLegend(50, 25, false, "arialbd.ttf", 10);
$legendObj->setBackground(Transparent);

# Set the x axis labels
$c->xAxis->setLabels($labels);

# Draw the ticks between label positions (instead of at label positions)
$c->xAxis->setTickOffset(0.5);

# Set axis label style to 8pts Arial Bold
$c->xAxis->setLabelStyle("arialbd.ttf", 8);
$c->yAxis->setLabelStyle("arialbd.ttf", 8);

# Set axis line width to 2 pixels
$c->xAxis->setWidth(2);
$c->yAxis->setWidth(2);

# Add axis title
$c->yAxis->setTitle("Throughput (MBytes Per Hour)");

# Add a multi-bar layer with 3 data sets and 4 pixels 3D depth
$layer = $c->addBarLayer2(Side, 4);
$layer->addDataSet($data0, 0xffff00, "Server #1");
$layer->addDataSet($data1, 0x00ff00, "Server #2");
$layer->addDataSet($data2, 0x9999ff, "Server #3");

# Set bar border to transparent. Use soft lighting effect with light direction from
# top.
$layer->setBorderColor(Transparent, softLighting(Top));

# Configure the bars within a group to touch each others (no gap)
$layer->setBarGap(0.2, TouchBar);

# output the chart
header("Content-type: image/png");
print($c->makeChart2(PNG));
?>
