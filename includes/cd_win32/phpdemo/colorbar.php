<?php
require_once("../lib/phpchartdir.php");

# The data for the bar chart
$data = array(85, 156, 179.5, 211, 123);

# The labels for the bar chart
$labels = array("Mon", "Tue", "Wed", "Thu", "Fri");

# The colors for the bar chart
$colors = array(0xb8bc9c, 0xa0bdc4, 0x999966, 0x333366, 0xc3c3e6);

# Create a XYChart object of size 300 x 220 pixels. Use golden background color. Use
# a 2 pixel 3D border.
$c = new XYChart(300, 220, goldColor(), -1, 2);

# Add a title box using 10 point Arial Bold font. Set the background color to
# metallic blue (9999FF) Use a 1 pixel 3D border.
$textBoxObj = $c->addTitle("Daily Network Load", "arialbd.ttf", 10);
$textBoxObj->setBackground(metalColor(0x9999ff), -1, 1);

# Set the plotarea at (40, 40) and of 240 x 150 pixels in size
$c->setPlotArea(40, 40, 240, 150);

# Add a multi-color bar chart layer using the given data and colors. Use a 1 pixel 3D
# border for the bars.
$barLayerObj = $c->addBarLayer3($data, $colors);
$barLayerObj->setBorderColor(-1, 1);

# Set the labels on the x axis.
$c->xAxis->setLabels($labels);

# output the chart
header("Content-type: image/png");
print($c->makeChart2(PNG));
?>
