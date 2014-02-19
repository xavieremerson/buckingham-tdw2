<?php
require_once("../lib/phpchartdir.php");

# Data for the chart
$data0 = array(5, 3, 10, 4, 3, 5, 2, 5);
$data1 = array(12, 6, 17, 6, 7, 9, 4, 7);
$data2 = array(17, 7, 22, 7, 18, 13, 5, 11);

$labels = array("North", "North<*br*>East", "East", "South<*br*>East", "South",
    "South<*br*>West", "West", "North<*br*>West");

# Create a PolarChart object of size 460 x 500 pixels, with a grey (e0e0e0)
# background and 1 pixel 3D border
$c = new PolarChart(460, 500, 0xe0e0e0, 0x000000, 1);

# Add a title to the chart at the top left corner using 15pts Arial Bold Italic font.
# Use a wood pattern as the title background.
$textBoxObj = $c->addTitle("Polar Area Chart Demo", "arialbi.ttf", 15);
$textBoxObj->setBackground($c->patternColor(dirname(__FILE__)."/wood.png"));

# Set center of plot area at (230, 280) with radius 180 pixels, and white (ffffff)
# background.
$c->setPlotArea(230, 280, 180, 0xffffff);

# Set the grid style to circular grid
$c->setGridStyle(false);

# Add a legend box at top-center of plot area (230, 35) using horizontal layout. Use
# 10 pts Arial Bold font, with 1 pixel 3D border effect.
$b = $c->addLegend(230, 35, false, "arialbd.ttf", 9);
$b->setAlignment(TopCenter);
$b->setBackground(Transparent, Transparent, 1);

# Set angular axis using the given labels
$angularAxisObj = $c->angularAxis();
$angularAxisObj->setLabels($labels);

# Specify the label format for the radial axis
$radialAxisObj = $c->radialAxis();
$radialAxisObj->setLabelFormat("{value}%");

# Set radial axis label background to semi-transparent grey (40cccccc)
$radialAxisObj = $c->radialAxis();
$textBoxObj = $radialAxisObj->setLabelStyle();
$textBoxObj->setBackground(0x40cccccc, 0);

# Add the data as area layers
$c->addAreaLayer($data2, -1, "5 m/s or above");
$c->addAreaLayer($data1, -1, "1 - 5 m/s");
$c->addAreaLayer($data0, -1, "less than 1 m/s");

# output the chart
header("Content-type: image/png");
print($c->makeChart2(PNG));
?>
