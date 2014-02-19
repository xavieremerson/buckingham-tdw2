<?php
require_once("../lib/phpchartdir.php");

# The data for the chart
$data = array(90, 60, 65, 75, 40);

# The labels for the chart
$labels = array("Speed", "Reliability", "Comfort", "Safety", "Efficiency");

# Create a PolarChart object of size 450 x 350 pixels
$c = new PolarChart(450, 350);

# Set center of plot area at (225, 185) with radius 150 pixels
$c->setPlotArea(225, 185, 150);

# Add an area layer to the polar chart
$c->addAreaLayer($data, 0x9999ff);

# Set the labels to the angular axis as spokes
$angularAxisObj = $c->angularAxis();
$angularAxisObj->setLabels($labels);

# output the chart
header("Content-type: image/png");
print($c->makeChart2(PNG));
?>
