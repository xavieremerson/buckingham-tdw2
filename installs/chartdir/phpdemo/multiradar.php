<?php
include("phpchartdir.php");

#The data for the chart
$data0 = array(90, 60, 85, 75, 55);
$data1 = array(60, 80, 70, 80, 85);

#The labels for the chart
$labels = array("Speed", "Reliability", "Comfort", "Safety", "Efficiency");

#Create a PolarChart object of size 480 x 380 pixels
$c = new PolarChart(480, 380);

#Set background color to gold (goldGradient), with 1 pixel 3D border effect
$c->setBackground($c->gradientColor($goldGradient, 90, 2), Transparent, 1);

#Add a title to the chart using 12 pts Arial Bold Italic font. The title text is
#white (0xffffff) on a black background
$titleObj = $c->addTitle("Space Travel Vehicles Compared", "arialbi.ttf", 12,
    0xffffff);
$titleObj->setBackground(0x0);

#Set center of plot area at (240, 210) with radius 150 pixels
$c->setPlotArea(240, 210, 150);

#Add a legend box at (5, 30) using 10 pts Arial Bold font. Set the background to
#silver (silverGradient), with a black border, and 1 pixel 3D border effect.
$legendObj = $c->addLegend(5, 30, true, "arialbd.ttf", 10);
$legendObj->setBackground($c->gradientColor($silverGradient, 90, 0.5), 1, 1);

#Add an area layer to the chart using semi-transparent blue (0x806666cc). Add a
#blue (0x6666cc) line layer using the same data with 3 pixel line width to
#highlight the border of the area.
$c->addAreaLayer($data0, 0x806666cc, "Ultra Speed");
$lineLayerObj = $c->addLineLayer($data0, 0x6666cc);
$lineLayerObj->setLineWidth(3);

#Add an area layer to the chart using semi-transparent red (0x80cc6666). Add a
#red (0xcc6666) line layer using the same data with 3 pixel line width to
#highlight the border of the area.
$c->addAreaLayer($data1, 0x80cc6666, "Super Economy");
$lineLayerObj = $c->addLineLayer($data1, 0xcc6666);
$lineLayerObj->setLineWidth(3);

#Set the labels to the angular axis as spokes.
$angularAxisObj = $c->angularAxis();
$angularAxisObj->setLabels($labels);

#output the chart
header("Content-type: image/png");
print($c->makeChart2(PNG));
?>
