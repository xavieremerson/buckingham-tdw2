<?php
require_once("../lib/phpchartdir.php");

# The data for the chart
$data0 = array(90, 60, 85, 75, 55);
$data1 = array(60, 80, 70, 80, 85);

# The labels for the chart
$labels = array("Speed", "Reliability", "Comfort", "Safety", "Efficiency");

# Create a PolarChart object of size 480 x 380 pixels. Set background color to gold,
# with 1 pixel 3D border effect
$c = new PolarChart(480, 380, goldColor(), 0x000000, 1);

# Add a title to the chart using 15 pts Times Bold Italic font. The title text is
# white (ffffff) on a deep blue (000080) background
$textBoxObj = $c->addTitle("Space Travel Vehicles Compared", "timesbi.ttf", 15,
    0xffffff);
$textBoxObj->setBackground(0x000080);

# Set plot area center at (240, 210), with 150 pixels radius, and a white (ffffff)
# background.
$c->setPlotArea(240, 210, 150, 0xffffff);

# Add a legend box at top right corner (470, 35) using 10 pts Arial Bold font. Set
# the background to silver, with 1 pixel 3D border effect.
$b = $c->addLegend(470, 35, true, "arialbd.ttf", 10);
$b->setAlignment(TopRight);
$b->setBackground(silverColor(), Transparent, 1);

# Add an area layer to the chart using semi-transparent blue (0x806666cc). Add a blue
# (0x6666cc) line layer using the same data with 3 pixel line width to highlight the
# border of the area.
$c->addAreaLayer($data0, 0x806666cc, "Model Saturn");
$lineLayerObj = $c->addLineLayer($data0, 0x6666cc);
$lineLayerObj->setLineWidth(3);

# Add an area layer to the chart using semi-transparent red (0x80cc6666). Add a red
# (0xcc6666) line layer using the same data with 3 pixel line width to highlight the
# border of the area.
$c->addAreaLayer($data1, 0x80cc6666, "Model Jupiter");
$lineLayerObj = $c->addLineLayer($data1, 0xcc6666);
$lineLayerObj->setLineWidth(3);

# Set the labels to the angular axis as spokes.
$angularAxisObj = $c->angularAxis();
$angularAxisObj->setLabels($labels);

# output the chart
header("Content-type: image/png");
print($c->makeChart2(PNG));
?>
